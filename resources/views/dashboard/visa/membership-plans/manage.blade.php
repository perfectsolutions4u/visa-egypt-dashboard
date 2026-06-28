@extends('layouts.dashboard.app')

@section('content')
    <div class="page-body">
        <x-dashboard.partials.breadcrumb title="Manage Membership Plans">
            <li class="breadcrumb-item active">Manage Plans</li>
        </x-dashboard.partials.breadcrumb>

        <div class="container-fluid">
            <x-dashboard.partials.message-alert/>

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="text-muted mb-1">Total Plans</h6>
                            <h3 class="mb-0">{{ $stats['total'] }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="text-muted mb-1">Active Plans</h6>
                            <h3 class="mb-0 text-success">{{ $stats['active'] }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="text-muted mb-1">Featured Plan</h6>
                            <h3 class="mb-0">{{ $stats['featured'] ?? 'None' }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="mb-1">Current Plans</h5>
                    <p class="text-muted mb-0">These plans are shown in the Visa Egypt mobile app.</p>
                </div>
                <div class="d-flex gap-2">
                    @can('membership-plans.list')
                        <a href="{{ route('dashboard.membership-plans.index') }}" class="btn btn-outline-primary">
                            Table View
                        </a>
                    @endcan
                    @can('membership-plans.create')
                        <a href="{{ route('dashboard.membership-plans.create') }}" class="btn btn-primary">
                            <i class="fa fa-plus"></i> Add Plan
                        </a>
                    @endcan
                </div>
            </div>

            @if($plans->isEmpty())
                <div class="card">
                    <div class="card-body text-center py-5">
                        <h5>No membership plans yet</h5>
                        <p class="text-muted">Create your first plan to show it in the mobile app.</p>
                        @can('membership-plans.create')
                            <a href="{{ route('dashboard.membership-plans.create') }}" class="btn btn-primary mt-2">
                                Create Plan
                            </a>
                        @endcan
                    </div>
                </div>
            @else
                <div class="row g-3">
                    @foreach($plans as $plan)
                        @php
                            $themeColor = $plan->theme_color ?: '#001438';
                            $features = is_array($plan->features) ? $plan->features : [];
                        @endphp
                        <div class="col-xl-3 col-lg-4 col-md-6">
                            <div class="card h-100 border-0 shadow-sm" style="border-top: 4px solid {{ $themeColor }} !important;">
                                <div class="card-body d-flex flex-column">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <span class="badge bg-light text-dark text-uppercase">{{ $plan->slug }}</span>
                                            @if($plan->is_featured)
                                                <span class="badge bg-warning text-dark">Featured</span>
                                            @endif
                                            @if($plan->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">Inactive</span>
                                            @endif
                                        </div>
                                        <strong style="color: {{ $themeColor }}">${{ number_format($plan->price_usd, 0) }}</strong>
                                    </div>

                                    <h5 class="mb-1">{{ $plan->name }}</h5>
                                    <p class="text-muted small mb-2">{{ $plan->tagline }}</p>
                                    <p class="small mb-3">{{ Str::limit($plan->description, 90) }}</p>

                                    <ul class="small ps-3 mb-3 flex-grow-1">
                                        @forelse(array_slice($features, 0, 4) as $feature)
                                            <li>{{ $feature }}</li>
                                        @empty
                                            <li class="text-muted">No features listed</li>
                                        @endforelse
                                    </ul>

                                    <div class="small mb-3">
                                        <div><strong>Discount:</strong> {{ rtrim(rtrim(number_format($plan->discount_percent, 2), '0'), '.') }}%</div>
                                        <div><strong>Daily Points:</strong> {{ number_format($plan->daily_points ?? 0) }} pts/day</div>
                                        <div><strong>Sort:</strong> {{ $plan->sort_order }}</div>
                                        @if($plan->special_offer_text)
                                            <div><strong>Offer:</strong> {{ $plan->special_offer_text }}</div>
                                        @endif
                                        @if($plan->vouchers->isNotEmpty())
                                            <div><strong>Vouchers:</strong> {{ $plan->vouchers->pluck('code')->join(', ') }}</div>
                                        @endif
                                        @if($plan->coupons->isNotEmpty())
                                            <div><strong>Coupons:</strong> {{ $plan->coupons->pluck('code')->join(', ') }}</div>
                                        @endif
                                    </div>

                                    <div class="d-grid gap-2">
                                        @can('membership-plans.edit')
                                            <a href="{{ route('dashboard.membership-plans.edit', $plan) }}" class="btn btn-primary btn-sm">
                                                <i class="fa fa-edit"></i> Edit Plan
                                            </a>
                                        @endcan
                                        <div class="btn-group">
                                            @can('membership-plans.edit')
                                                <form action="{{ route('dashboard.membership-plans.toggle-active', $plan) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-secondary btn-sm">
                                                        {{ $plan->is_active ? 'Deactivate' : 'Activate' }}
                                                    </button>
                                                </form>
                                                <form action="{{ route('dashboard.membership-plans.toggle-featured', $plan) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-warning btn-sm">
                                                        {{ $plan->is_featured ? 'Unfeature' : 'Set Featured' }}
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection
