@extends('layouts.dashboard.app')

@section('content')
    <form action="{{ route('dashboard.trip-bookings.store') }}" method="POST" class="page-body">
        @csrf

        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="Create Trip Booking" :hideFirst="true">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard.trip-bookings.index') }}">Trip Bookings</a>
            </li>
        </x-dashboard.partials.breadcrumb>
        <!-- Container-fluid Ends-->

        <!-- Container-fluid starts-->
        <div class="container-fluid">
            <div class="row">
                <x-dashboard.partials.message-alert/>

                <div class="card tab2-card">
                    <div class="card-body needs-validation">
                        <x-dashboard.form.multi-tab-card
                            :tabs="['booking_details', 'passenger_info', 'pricing']"
                            tab-id="booking-tabs">
                            
                            <!-- Booking Details Tab -->
                            <div class="tab-pane fade active show"
                                 id="{{ 'booking-tabs-0' }}" role="tabpanel"
                                 aria-labelledby="{{ 'booking-tabs-0' }}-tab">

                                <x-dashboard.form.input-select 
                                    id="trip_id"
                                    name="trip_id"
                                    labelTitle="Select Trip"
                                    :options="$trips"
                                    errorKey="trip_id"
                                    required="true"
                                    :value="old('trip_id', $selectedTripId)"
                                />

                                <x-dashboard.form.input-select 
                                    id="client_id"
                                    name="client_id"
                                    labelTitle="Client (Optional)"
                                    :options="$clients"
                                    errorKey="client_id"
                                    :value="old('client_id')"
                                />

                                <x-dashboard.form.input-select 
                                    id="status"
                                    name="status"
                                    labelTitle="Booking Status"
                                    :options="$statuses"
                                    errorKey="status"
                                    required="true"
                                    :value="old('status', 'pending')"
                                />

                                <div class="form-group row">
                                    <label class="col-xl-3 col-md-4" for="booking_reference">Booking Reference</label>
                                    <div class="col-xl-8 col-md-7">
                                        <input type="text" 
                                               class="form-control" 
                                               id="booking_reference" 
                                               name="booking_reference" 
                                               value="{{ old('booking_reference', \App\Models\TripBooking::generateBookingReference()) }}"
                                               readonly>
                                        <small class="form-text text-muted">Auto-generated booking reference</small>
                                        @error('booking_reference')
                                            <span class="d-block text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                            </div>

                            <!-- Passenger Information Tab -->
                            <div class="tab-pane fade"
                                 id="{{ 'booking-tabs-1' }}" role="tabpanel"
                                 aria-labelledby="{{ 'booking-tabs-1' }}-tab">

                                <x-dashboard.form.input-text 
                                    id="passenger_name"
                                    name="passenger_name"
                                    labelTitle="Passenger Name"
                                    value="{{ old('passenger_name') }}"
                                    errorKey="passenger_name"
                                    required="true"
                                />

                                <x-dashboard.form.input-text 
                                    id="passenger_email"
                                    name="passenger_email"
                                    labelTitle="Passenger Email"
                                    type="email"
                                    value="{{ old('passenger_email') }}"
                                    errorKey="passenger_email"
                                    required="true"
                                />

                                <x-dashboard.form.input-text 
                                    id="passenger_phone"
                                    name="passenger_phone"
                                    labelTitle="Passenger Phone"
                                    value="{{ old('passenger_phone') }}"
                                    errorKey="passenger_phone"
                                    required="true"
                                />

                                <x-dashboard.form.input-number 
                                    id="adults_count"
                                    name="adults_count"
                                    labelTitle="Number of Adults"
                                    value="{{ old('adults_count', 1) }}"
                                    errorKey="adults_count"
                                    required="true"
                                    min="1"
                                    max="50"
                                />

                                <x-dashboard.form.input-number 
                                    id="children_count"
                                    name="children_count"
                                    labelTitle="Number of Children"
                                    value="{{ old('children_count', 0) }}"
                                    errorKey="children_count"
                                    min="0"
                                    max="50"
                                />

                                <div class="form-group row">
                                    <label class="col-xl-3 col-md-4"></label>
                                    <div class="col-xl-8 col-md-7">
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle"></i>
                                            <strong>Note:</strong> Children over 6 years old require a full ticket.
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <!-- Pricing Tab -->
                            <div class="tab-pane fade"
                                 id="{{ 'booking-tabs-2' }}" role="tabpanel"
                                 aria-labelledby="{{ 'booking-tabs-2' }}-tab">

                                <div class="form-group row">
                                    <label class="col-xl-3 col-md-4">Trip Information</label>
                                    <div class="col-xl-8 col-md-7">
                                        <div id="trip-info" class="alert alert-info" style="display: none;">
                                            <!-- Trip info will be populated via JavaScript -->
                                        </div>
                                    </div>
                                </div>

                                <x-dashboard.form.input-number 
                                    id="total_price"
                                    name="total_price"
                                    labelTitle="Total Price (EGP)"
                                    value="{{ old('total_price') }}"
                                    errorKey="total_price"
                                    required="true"
                                    min="0"
                                    step="0.01"
                                />

                                <div class="form-group row">
                                    <label class="col-xl-3 col-md-4">Price Breakdown</label>
                                    <div class="col-xl-8 col-md-7">
                                        <div id="price-breakdown" class="alert alert-secondary">
                                            <!-- Price breakdown will be populated via JavaScript -->
                                            <p class="mb-0">Select a trip to see price breakdown</p>
                                        </div>
                                    </div>
                                </div>

                                <x-dashboard.form.input-textarea 
                                    id="notes"
                                    name="notes"
                                    labelTitle="Special Requests / Notes"
                                    value="{{ old('notes') }}"
                                    errorKey="notes"
                                    rows="4"
                                />

                            </div>

                        </x-dashboard.form.multi-tab-card>

                        <x-dashboard.form.submit-button 
                            title="Create Booking"
                            class="btn-primary"
                        />

                    </div>
                </div>
            </div>
        </div>
        <!-- Container-fluid Ends-->
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tripSelect = document.getElementById('trip_id');
            const adultsInput = document.getElementById('adults_count');
            const childrenInput = document.getElementById('children_count');
            const totalPriceInput = document.getElementById('total_price');
            const tripInfoDiv = document.getElementById('trip-info');
            const priceBreakdownDiv = document.getElementById('price-breakdown');

            // Initialize select2
            if (typeof $.fn.select2 !== 'undefined') {
                $('.select2').select2({
                    width: '100%',
                    placeholder: '--Select Option--'
                });
            }

            // Function to update trip information and pricing
            function updateTripInfo() {
                const tripId = tripSelect.value;
                const adults = parseInt(adultsInput.value) || 0;
                const children = parseInt(childrenInput.value) || 0;

                if (!tripId) {
                    tripInfoDiv.style.display = 'none';
                    priceBreakdownDiv.innerHTML = '<p class="mb-0">Select a trip to see price breakdown</p>';
                    return;
                }

                // Fetch trip details via AJAX
                fetch(`/dashboard/trips/${tripId}/details`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    credentials: 'same-origin'
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            const trip = data.trip;
                            
                            // Update trip info
                            tripInfoDiv.innerHTML = `
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>From:</strong> ${trip.departure_city_name}<br>
                                        <strong>To:</strong> ${trip.arrival_city_name}<br>
                                        <strong>Date:</strong> ${trip.travel_date}<br>
                                        <strong>Time:</strong> ${trip.departure_time} - ${trip.arrival_time}
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Available Seats:</strong> ${trip.available_seats}<br>
                                        <strong>Seat Price:</strong> ${trip.seat_price} EGP<br>
                                        <strong>Trip Type:</strong> ${trip.trip_type_label}
                                    </div>
                                </div>
                            `;
                            tripInfoDiv.style.display = 'block';

                            // Calculate and update pricing
                            const totalPassengers = adults + children;
                            const adultPrice = trip.seat_price * adults;
                            const childPrice = trip.seat_price * children; // Full price for children over 6
                            const totalPrice = adultPrice + childPrice;

                            priceBreakdownDiv.innerHTML = `
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Adults (${adults}):</strong> ${adults} × ${trip.seat_price} EGP = ${adultPrice} EGP<br>
                                        <strong>Children (${children}):</strong> ${children} × ${trip.seat_price} EGP = ${childPrice} EGP
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Total Passengers:</strong> ${totalPassengers}<br>
                                        <strong>Total Price:</strong> <span class="text-primary font-weight-bold">${totalPrice} EGP</span>
                                    </div>
                                </div>
                            `;

                            // Update total price input
                            totalPriceInput.value = totalPrice;

                            // Validate seat availability
                            if (totalPassengers > trip.available_seats) {
                                priceBreakdownDiv.innerHTML += `
                                    <div class="alert alert-warning mt-2">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        <strong>Warning:</strong> Only ${trip.available_seats} seats available for this trip.
                                    </div>
                                `;
                            }
                        } else {
                            throw new Error(data.message || 'Failed to load trip details');
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching trip details:', error);
                        tripInfoDiv.innerHTML = `
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle"></i>
                                <strong>Error loading trip details:</strong><br>
                                ${error.message}<br>
                                <small>Please try refreshing the page or contact support if the problem persists.</small>
                            </div>
                        `;
                        tripInfoDiv.style.display = 'block';
                    });
            }

            // Event listeners
            tripSelect.addEventListener('change', updateTripInfo);
            adultsInput.addEventListener('input', updateTripInfo);
            childrenInput.addEventListener('input', updateTripInfo);

            // Initial update if values are pre-filled
            if (tripSelect.value) {
                updateTripInfo();
            }

            // Form validation
            document.querySelector('form').addEventListener('submit', function(e) {
                const tripId = tripSelect.value;
                const adults = parseInt(adultsInput.value) || 0;
                const children = parseInt(childrenInput.value) || 0;
                const totalPassengers = adults + children;

                if (!tripId) {
                    e.preventDefault();
                    alert('Please select a trip');
                    return;
                }

                if (totalPassengers === 0) {
                    e.preventDefault();
                    alert('Please enter at least one passenger');
                    return;
                }
            });
        });
    </script>
@endsection 