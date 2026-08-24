<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Password\ForgetPasswordRequest;
use App\Http\Requests\Api\Password\OtpVerifyRequest;
use App\Http\Requests\Api\Password\ResetPasswordRequest;
use App\Http\Requests\Api\V1\LoginRequest;
use App\Http\Requests\Api\V1\RegisterRequest;
use App\Http\Requests\Api\V1\ResendOtpRequest;
use App\Http\Requests\Api\V1\SocialLoginRequest;
use App\Http\Requests\Api\V1\VerifyOtpRequest;
use App\Http\Resources\Api\V1\ClientResource;
use App\Models\Client;
use App\Models\PasswordReset;
use App\Notifications\Client\ForgetPasswordNotification;
use App\Notifications\Client\VerifyOtpNotification;
use App\Traits\Response\HasApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    use HasApiResponse;

    public function register(RegisterRequest $request)
    {
        $client = Client::create($request->getSanitized());
        $otp = $this->issueEmailOtp($client);

        return $this->send([
            'client' => new ClientResource($client),
            'otp_required' => true,
            'otp' => app()->environment('local') ? $otp : null,
        ], __('messages.auth.registered'), Response::HTTP_CREATED);
    }

    public function resendOtp(ResendOtpRequest $request)
    {
        $email = $request->get('email');
        $client = Client::whereEmail($email)->firstOrFail();

        $cacheKey = "visa_otp_resend:{$email}";
        if (Cache::has($cacheKey)) {
            return $this->send(
                message: __('messages.notifications.password.forget.try_after_60'),
                statusCode: Response::HTTP_BAD_REQUEST
            );
        }

        $otp = $this->issueEmailOtp($client);
        Cache::put($cacheKey, true, now()->addSeconds(60));

        return $this->send([
            'otp_required' => true,
            'otp' => app()->environment('local') ? $otp : null,
        ], 'Verification code resent.');
    }

    public function login(LoginRequest $request)
    {
        $query = Client::query();
        if ($request->filled('email')) {
            $query->where('email', $request->get('email'));
        } else {
            $query->where('phone', $request->get('phone'));
        }

        $client = $query->first();
        if (! $client || $client->blocked || ! Hash::check($request->get('password'), $client->password)) {
            return $this->send(message: __('auth.failed'), statusCode: Response::HTTP_UNAUTHORIZED);
        }

        $token = $client->createToken('visa-app')->plainTextToken;

        return $this->send([
            'client' => new ClientResource($client->load(['activeMembership', 'wallet'])),
            'token' => $token,
        ], __('messages.auth.logged_in_successfully'));
    }

    public function verifyOtp(VerifyOtpRequest $request)
    {
        $email = $request->get('email');
        $otp = $request->get('otp');
        $cached = Cache::get("visa_otp:{$email}");
        $reset = PasswordReset::where('email', $email)->first();

        $valid = ($cached && $cached === $otp)
            || ($reset && ! $reset->expired() && (string) $reset->token === (string) $otp);

        if (! $valid) {
            return $this->send(message: __('messages.password.otp_invalid'), statusCode: Response::HTTP_BAD_REQUEST);
        }

        Cache::forget("visa_otp:{$email}");

        $client = Client::whereEmail($email)->firstOrFail();
        if ($client->email_verified_at === null) {
            $client->forceFill(['email_verified_at' => now()])->save();
        }

        $token = $client->createToken('visa-app')->plainTextToken;

        return $this->send([
            'client' => new ClientResource($client->load(['activeMembership', 'wallet'])),
            'token' => $token,
        ], 'OTP verified successfully.');
    }

    public function forgetPassword(ForgetPasswordRequest $request)
    {
        $email = $request->get('email');

        if ($reset = PasswordReset::whereEmail($email)->first()) {
            if (now()->diffInSeconds($reset->created_at) < 60) {
                return $this->send(
                    message: __('messages.notifications.password.forget.try_after_60'),
                    statusCode: Response::HTTP_BAD_REQUEST
                );
            }
        }

        $otp = (string) random_int(100000, 999999);

        PasswordReset::updateOrCreate(
            ['email' => $email],
            [
                'email' => $email,
                'token' => $otp,
                'created_at' => now(),
            ]
        );

        $client = Client::whereEmail($email)->firstOrFail();
        $client->notify(new ForgetPasswordNotification($otp));

        return $this->send([
            'otp' => app()->environment('local') ? $otp : null,
        ], __('messages.password.forget'));
    }

    public function verifyPasswordOtp(OtpVerifyRequest $request)
    {
        $reset = PasswordReset::where('email', $request->get('email'))->first();

        if (! $reset || $reset->expired()) {
            return $this->send(
                message: __('messages.password.otp_expired'),
                statusCode: Response::HTTP_BAD_REQUEST
            );
        }

        if ((string) $reset->token !== (string) $request->get('otp')) {
            return $this->send(
                message: __('messages.password.otp_invalid'),
                statusCode: Response::HTTP_BAD_REQUEST
            );
        }

        return $this->ok();
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $reset = PasswordReset::where('email', $request->get('email'))->first();

        if (! $reset || $reset->expired()) {
            return $this->send(
                message: __('messages.password.otp_expired'),
                statusCode: Response::HTTP_BAD_REQUEST
            );
        }

        if ((string) $reset->token !== (string) $request->get('otp')) {
            return $this->send(
                message: __('messages.password.otp_invalid'),
                statusCode: Response::HTTP_BAD_REQUEST
            );
        }

        Client::whereEmail($request->get('email'))
            ->firstOrFail()
            ->update([
                'password' => Hash::make($request->get('password')),
            ]);

        $reset->delete();

        return $this->send(message: __('messages.password.reset'));
    }

    public function social(SocialLoginRequest $request)
    {
        $email = $request->get('email');
        $client = Client::whereEmail($email)->first();

        if (! $client) {
            $client = Client::create([
                'name' => $request->get('name'),
                'email' => $email,
                'password' => Hash::make(Str::random(32)),
                'language' => $request->get('language') ?? 'en',
                'email_verified_at' => now(),
            ]);
        } elseif ($client->blocked) {
            return $this->send(message: __('auth.failed'), statusCode: Response::HTTP_UNAUTHORIZED);
        } elseif ($client->email_verified_at === null) {
            $client->forceFill(['email_verified_at' => now()])->save();
        }

        $token = $client->createToken('visa-app-'.$request->get('provider'))->plainTextToken;

        return $this->send([
            'client' => new ClientResource($client->load(['activeMembership', 'wallet'])),
            'token' => $token,
        ], __('messages.auth.logged_in_successfully'));
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();

        return $this->send(message: 'Logged out.');
    }

    public function me(Request $request)
    {
        return $this->send(new ClientResource(
            $request->user()->load(['activeMembership', 'wallet'])
        ));
    }

    private function issueEmailOtp(Client $client): string
    {
        $otp = (string) random_int(100000, 999999);
        Cache::put("visa_otp:{$client->email}", $otp, now()->addMinutes(10));
        $client->notify(new VerifyOtpNotification($otp));

        return $otp;
    }
}
