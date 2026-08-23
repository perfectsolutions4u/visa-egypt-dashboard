@extends('layouts.dashboard.app')

@section('content')
    <div class="page-body">
        <x-dashboard.partials.breadcrumb title="Database Backup" :hideFirst="true">
            <li class="breadcrumb-item active">Backup & Restore</li>
        </x-dashboard.partials.breadcrumb>

        <div class="container-fluid">
            <div class="row">
                <x-dashboard.partials.message-alert/>

                <div class="col-12">
                    <div class="alert alert-warning">
                        Restore replaces the current database with the uploaded copy.
                        A safety backup is created automatically before restore. You may need to sign in again afterwards.
                    </div>
                </div>

                <div class="col-12 col-lg-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5>Take a copy</h5>
                            <span class="text-muted">Save a snapshot of <strong>{{ $database }}</strong> on the server, then download it.</span>
                        </div>
                        <div class="card-body">
                            @if($canEdit)
                                <form action="{{ route('dashboard.database-backups.store') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary">
                                        <i data-feather="download"></i> Create backup now
                                    </button>
                                </form>
                            @else
                                <p class="mb-0 text-muted">You can download existing copies. Creating a new backup needs edit permission.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5>Upload a copy</h5>
                            <span class="text-muted">Restore from a <code>.sql</code> or <code>.sql.gz</code> file taken from this page.</span>
                        </div>
                        <div class="card-body">
                            @if($canEdit)
                                <form action="{{ route('dashboard.database-backups.restore') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-3">
                                        <input type="file" name="backup" class="form-control" accept=".sql,.gz,.sql.gz" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Type <strong>RESTORE</strong> to confirm</label>
                                        <input type="text" name="confirmation" class="form-control" placeholder="RESTORE" autocomplete="off" required>
                                    </div>
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('This will overwrite the live database. Continue?')">
                                        <i data-feather="upload"></i> Restore uploaded file
                                    </button>
                                </form>
                            @else
                                <p class="mb-0 text-muted">Restore is limited to users who can edit settings.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5>Saved copies</h5>
                            <span class="text-muted">The last 30 backups are kept on the server.</span>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                    <tr>
                                        <th>File</th>
                                        <th>Size</th>
                                        <th>Created</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($backups as $backup)
                                        <tr>
                                            <td><code>{{ $backup['name'] }}</code></td>
                                            <td>{{ $backup['formatted_size'] }}</td>
                                            <td>{{ $backup['formatted_date'] }}</td>
                                            <td class="text-end">
                                                <a href="{{ route('dashboard.database-backups.download', $backup['name']) }}" class="btn btn-sm btn-outline-primary">
                                                    Download
                                                </a>
                                                @if($canEdit)
                                                    <form action="{{ route('dashboard.database-backups.restore') }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <input type="hidden" name="filename" value="{{ $backup['name'] }}">
                                                        <input type="hidden" name="confirmation" value="RESTORE">
                                                        <button type="submit" class="btn btn-sm btn-outline-warning" onclick="return confirm('Restore this copy and overwrite the live database?') && prompt('Type RESTORE to confirm') === 'RESTORE'">
                                                            Restore
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('dashboard.database-backups.destroy', $backup['name']) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this backup file?')">
                                                            Delete
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-4">No backups yet. Create one to download a copy of the database.</td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
