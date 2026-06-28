<div class="btn-group">
    @can('visa-bookings.show')
        <a href="{{ route('dashboard.visa-bookings.show', $id) }}" class="btn btn-sm btn-primary">View</a>
    @endcan
</div>
