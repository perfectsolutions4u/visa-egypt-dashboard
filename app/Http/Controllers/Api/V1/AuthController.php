<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\LoginRequest;
use App\Http\Requests\Api\V1\RegisterRequest;
use App\Http\Requests\Api\V1\VerifyOtpRequest;
use App\Http\Resources\Api\V1\ClientResource;
use App\Models\Client;
use App\Models\PasswordReset;
use App\Traits\Response\HasApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    use HasApiResponse;

    public function register(RegisterRequest $request)
    {
        $client = Client::create($request->getSanitized());
        $otp = (string) random_int(100000, 999999);
        Cache::put("visa_otp:{$client->email}", $otp, now()->addMinutes(10));

        return $this->send([
            'client' => new ClientResource($client),
            'otp_required' => true,
            'otp' => app()->environment('local') ? $otp : null,
        ], __('messages.auth.registered'), Response::HTTP_CREATED);
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
        $token = $client->createToken('visa-app')->plainTextToken;

        return $this->send([
            'client' => new ClientResource($client->load(['activeMembership', 'wallet'])),
            'token' => $token,
        ], 'OTP verified successfully.');
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
}
