<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\Visa\VisaBookingStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\CreateVisaBookingRequest;
use App\Http\Resources\Api\V1\VisaBookingResource;
use App\Models\Visa\VisaBooking;
use App\Services\Visa\BookingRefGenerator;
use App\Traits\Response\HasApiResponse;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class VisaBookingController extends Controller
{
    use HasApiResponse;

    public function index(Request $request)
    {
        $bookings = VisaBooking::with(['program', 'servicePackage', 'currentTrackingEvent'])
            ->where('client_id', $request->user()->id)
            ->latest()
            ->paginate($request->integer('per_page', 20));

        return $this->send(VisaBookingResource::collection($bookings)->response()->getData(true));
    }

    public function store(CreateVisaBookingRequest $request, BookingRefGenerator $refs)
    {
        $data = $request->validated();
        $client = $request->user();

        $payload = array_merge($data, [
            'client_id' => $client->id,
            'booking_ref' => $refs->generate(),
            'status' => VisaBookingStatus::PENDING,
            'contact_email' => $data['contact_email'] ?? $client->email,
            'contact_whatsapp' => $data['contact_whatsapp'] ?? $client->whatsapp,
            'nationality' => $data['nationality'] ?? $client->nationality,
            'travelers_count' => $data['travelers_count'] ?? 1,
        ]);

        try {
            $booking = VisaBooking::create($payload);
        } catch (QueryException $exception) {
            if (empty($payload['program_id']) || ! str_contains($exception->getMessage(), 'program_id')) {
                throw $exception;
            }

            $metadata = is_array($payload['metadata'] ?? null) ? $payload['metadata'] : [];
            $metadata['tour_id'] = $payload['program_id'];
            $payload['metadata'] = $metadata;
            unset($payload['program_id']);
            $booking = VisaBooking::create($payload);
        }

        return $this->send(new VisaBookingResource($booking->load(['program', 'servicePackage'])), 'Booking created.', 201);
    }

    public function show(Request $request, VisaBooking $visaBooking)
    {
        abort_if($visaBooking->client_id !== $request->user()->id, 403);

        return $this->send(new VisaBookingResource(
            $visaBooking->load(['program', 'servicePackage', 'vehicle', 'trackingEvents', 'assignment.staff', 'assignment.vehicle', 'currentTrackingEvent', 'payments'])
        ));
    }
}
