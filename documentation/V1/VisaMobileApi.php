<?php

namespace Documentation\V1;

use App\Http\Controllers\Controller;
use App\Traits\Response\HasApiResponse;

/**
 * @OA\Tag(
 *     name="Visa Mobile API",
 *     description="Visa Egypt mobile app REST API (base: /api/v1). Auth: Bearer Sanctum token. Header: X-Localize (en, ar, ...)"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="visaBearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="Sanctum"
 * )
 */
class VisaMobileApi extends Controller
{
    use HasApiResponse;

    /**
     * @OA\Post(
     *     path="/v1/auth/register",
     *     tags={"Visa Mobile API"},
     *     summary="Register client",
     *     @OA\Parameter(name="X-Localize", in="header", @OA\Schema(type="string", example="en")),
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         required={"name","email","password","password_confirmation"},
     *         @OA\Property(property="name", type="string"),
     *         @OA\Property(property="email", type="string"),
     *         @OA\Property(property="password", type="string"),
     *         @OA\Property(property="password_confirmation", type="string"),
     *         @OA\Property(property="phone", type="string"),
     *         @OA\Property(property="whatsapp", type="string"),
     *         @OA\Property(property="language", type="string", example="en")
     *     )),
     *     @OA\Response(response=201, description="Registered — verify OTP next")
     * )
     */
    public function register() {}

    /**
     * @OA\Post(
     *     path="/v1/auth/login",
     *     tags={"Visa Mobile API"},
     *     summary="Login with email or phone",
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         required={"password"},
     *         @OA\Property(property="email", type="string"),
     *         @OA\Property(property="phone", type="string"),
     *         @OA\Property(property="password", type="string")
     *     )),
     *     @OA\Response(response=200, description="Returns client + token")
     * )
     */
    public function login() {}

    /**
     * @OA\Post(
     *     path="/v1/auth/verify-otp",
     *     tags={"Visa Mobile API"},
     *     summary="Verify OTP after registration",
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         @OA\Property(property="email", type="string"),
     *         @OA\Property(property="otp", type="string")
     *     )),
     *     @OA\Response(response=200, description="Returns token")
     * )
     */
    public function verifyOtp() {}

    /**
     * @OA\Post(
     *     path="/v1/auth/logout",
     *     tags={"Visa Mobile API"},
     *     summary="Logout",
     *     security={{"visaBearerAuth":{}}},
     *     @OA\Response(response=200, description="Logged out")
     * )
     */
    public function logout() {}

    /**
     * @OA\Get(
     *     path="/v1/auth/me",
     *     tags={"Visa Mobile API"},
     *     summary="Current user",
     *     security={{"visaBearerAuth":{}}},
     *     @OA\Response(response=200, description="Client profile")
     * )
     */
    public function me() {}

    /**
     * @OA\Get(
     *     path="/v1/programs",
     *     tags={"Visa Mobile API"},
     *     summary="List Explore Egypt programs",
     *     @OA\Parameter(name="best_seller", in="query", @OA\Schema(type="boolean")),
     *     @OA\Response(response=200, description="Programs list")
     * )
     */
    public function programs() {}

    /**
     * @OA\Get(
     *     path="/v1/programs/{slug}",
     *     tags={"Visa Mobile API"},
     *     summary="Program details by slug",
     *     @OA\Parameter(name="slug", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Program detail")
     * )
     */
    public function programShow() {}

    /**
     * @OA\Get(
     *     path="/v1/service-packages",
     *     tags={"Visa Mobile API"},
     *     summary="Meet & Assist and Transit packages",
     *     @OA\Parameter(name="service_type", in="query", @OA\Schema(type="string", example="meet_assist")),
     *     @OA\Response(response=200, description="Packages list")
     * )
     */
    public function servicePackages() {}

    /**
     * @OA\Get(
     *     path="/v1/vehicles",
     *     tags={"Visa Mobile API"},
     *     summary="Available vehicles",
     *     @OA\Response(response=200, description="Vehicles list")
     * )
     */
    public function vehicles() {}

    /**
     * @OA\Get(
     *     path="/v1/offers",
     *     tags={"Visa Mobile API"},
     *     summary="Active offers",
     *     @OA\Response(response=200, description="Offers list")
     * )
     */
    public function offers() {}

    /**
     * @OA\Get(
     *     path="/v1/settings/visa",
     *     tags={"Visa Mobile API"},
     *     summary="Visa app settings (FAQ, terms, support)",
     *     @OA\Response(response=200, description="Settings key-value")
     * )
     */
    public function settings() {}

    /**
     * @OA\Get(
     *     path="/v1/settings/app-update",
     *     tags={"Visa Mobile API"},
     *     summary="Mobile app update config (version, force flag, store links)",
     *     @OA\Parameter(name="platform", in="query", required=false, @OA\Schema(type="string", enum={"android","ios"})),
     *     @OA\Parameter(name="version", in="query", required=false, @OA\Schema(type="string", example="1.0.0")),
     *     @OA\Parameter(name="build", in="query", required=false, @OA\Schema(type="integer", example=1)),
     *     @OA\Response(response=200, description="Update settings and optional update_required decision")
     * )
     */
    public function appUpdateSettings() {}

    /**
     * @OA\Get(
     *     path="/v1/bookings",
     *     tags={"Visa Mobile API"},
     *     summary="My bookings",
     *     security={{"visaBearerAuth":{}}},
     *     @OA\Response(response=200, description="Paginated bookings")
     * )
     */
    public function bookingsIndex() {}

    /**
     * @OA\Post(
     *     path="/v1/bookings",
     *     tags={"Visa Mobile API"},
     *     summary="Create booking",
     *     security={{"visaBearerAuth":{}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         @OA\Property(property="service_type", type="string", example="meet_assist"),
     *         @OA\Property(property="travel_date", type="string", format="date"),
     *         @OA\Property(property="service_package_id", type="integer"),
     *         @OA\Property(property="program_id", type="integer"),
     *         @OA\Property(property="flight_number", type="string")
     *     )),
     *     @OA\Response(response=201, description="Booking created with booking_ref")
     * )
     */
    public function bookingsStore() {}

    /**
     * @OA\Get(
     *     path="/v1/bookings/{ref}",
     *     tags={"Visa Mobile API"},
     *     summary="Booking details by ref (VE########)",
     *     security={{"visaBearerAuth":{}}},
     *     @OA\Parameter(name="ref", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Booking detail")
     * )
     */
    public function bookingsShow() {}

    /**
     * @OA\Get(
     *     path="/v1/bookings/{ref}/tracking",
     *     tags={"Visa Mobile API"},
     *     summary="Live tracking timeline",
     *     security={{"visaBearerAuth":{}}},
     *     @OA\Parameter(name="ref", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Tracking flow and events")
     * )
     */
    public function bookingsTracking() {}

    /**
     * @OA\Get(
     *     path="/v1/membership",
     *     tags={"Visa Mobile API"},
     *     summary="Current membership",
     *     security={{"visaBearerAuth":{}}},
     *     @OA\Response(response=200, description="Active membership or null")
     * )
     */
    public function membership() {}

    /**
     * @OA\Post(
     *     path="/v1/membership/checkout",
     *     tags={"Visa Mobile API"},
     *     summary="Purchase or upgrade membership",
     *     security={{"visaBearerAuth":{}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         @OA\Property(property="plan_type", type="string", enum={"silver","gold","platinum"}),
     *         @OA\Property(property="payment_method", type="string", enum={"card","paypal","wallet"})
     *     )),
     *     @OA\Response(response=201, description="Pending membership + payment")
     * )
     */
    public function membershipCheckout() {}

    /**
     * @OA\Get(
     *     path="/v1/wallet",
     *     tags={"Visa Mobile API"},
     *     summary="Wallet balance",
     *     security={{"visaBearerAuth":{}}},
     *     @OA\Response(response=200, description="Wallet")
     * )
     */
    public function wallet() {}

    /**
     * @OA\Get(
     *     path="/v1/wallet/transactions",
     *     tags={"Visa Mobile API"},
     *     summary="Wallet transaction history",
     *     security={{"visaBearerAuth":{}}},
     *     @OA\Response(response=200, description="Paginated transactions")
     * )
     */
    public function walletTransactions() {}

    /**
     * @OA\Post(
     *     path="/v1/payments",
     *     tags={"Visa Mobile API"},
     *     summary="Initiate payment",
     *     security={{"visaBearerAuth":{}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         @OA\Property(property="visa_booking_id", type="integer"),
     *         @OA\Property(property="membership_id", type="integer"),
     *         @OA\Property(property="amount", type="number"),
     *         @OA\Property(property="method", type="string", example="card")
     *     )),
     *     @OA\Response(response=201, description="Payment pending")
     * )
     */
    public function paymentsStore() {}

    /**
     * @OA\Get(
     *     path="/v1/payments/{id}",
     *     tags={"Visa Mobile API"},
     *     summary="Payment status",
     *     security={{"visaBearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Payment detail")
     * )
     */
    public function paymentsShow() {}

    /**
     * @OA\Get(
     *     path="/v1/notifications",
     *     tags={"Visa Mobile API"},
     *     summary="App notifications",
     *     security={{"visaBearerAuth":{}}},
     *     @OA\Response(response=200, description="Paginated notifications")
     * )
     */
    public function notifications() {}

    /**
     * @OA\Patch(
     *     path="/v1/notifications/{id}/read",
     *     tags={"Visa Mobile API"},
     *     summary="Mark notification as read",
     *     security={{"visaBearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Updated notification")
     * )
     */
    public function notificationsRead() {}

    /**
     * @OA\Get(
     *     path="/v1/profile",
     *     tags={"Visa Mobile API"},
     *     summary="Profile",
     *     security={{"visaBearerAuth":{}}},
     *     @OA\Response(response=200, description="Client profile")
     * )
     */
    public function profileShow() {}

    /**
     * @OA\Put(
     *     path="/v1/profile",
     *     tags={"Visa Mobile API"},
     *     summary="Update profile",
     *     security={{"visaBearerAuth":{}}},
     *     @OA\RequestBody(@OA\JsonContent(
     *         @OA\Property(property="name", type="string"),
     *         @OA\Property(property="phone", type="string"),
     *         @OA\Property(property="whatsapp", type="string"),
     *         @OA\Property(property="nationality", type="string")
     *     )),
     *     @OA\Response(response=200, description="Updated profile")
     * )
     */
    public function profileUpdate() {}

    /**
     * @OA\Put(
     *     path="/v1/profile/language",
     *     tags={"Visa Mobile API"},
     *     summary="Change language",
     *     security={{"visaBearerAuth":{}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         @OA\Property(property="language", type="string", example="ar")
     *     )),
     *     @OA\Response(response=200, description="Language updated")
     * )
     */
    public function profileLanguage() {}
}
