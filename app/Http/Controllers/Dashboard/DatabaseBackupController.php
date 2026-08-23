<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\Database\DatabaseBackupService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

class DatabaseBackupController extends Controller
{
    public function __construct(private readonly DatabaseBackupService $backups)
    {
    }

    public function index(): View
    {
        return view('dashboard.database-backups.index', [
            'backups' => $this->backups->list(),
            'database' => config('database.connections.'.config('database.default').'.database'),
            'canEdit' => admin()?->can('settings.edit') ?? false,
        ]);
    }

    public function store(): RedirectResponse
    {
        try {
            $filename = $this->backups->create();
        } catch (Throwable $exception) {
            report($exception);

            return $this->flashBack('Could not create backup: '.$exception->getMessage(), 'danger');
        }

        return $this->flashBack("Backup created: {$filename}", 'success');
    }

    public function download(string $filename): BinaryFileResponse|RedirectResponse
    {
        try {
            return $this->backups->download($filename);
        } catch (RuntimeException $exception) {
            return $this->flashBack($exception->getMessage(), 'danger');
        }
    }

    public function restore(Request $request): RedirectResponse
    {
        $request->validate([
            'confirmation' => ['required', 'in:RESTORE'],
            'backup' => ['nullable', 'file', 'max:262144'],
            'filename' => ['nullable', 'string', 'max:180'],
        ]);

        try {
            if ($request->hasFile('backup')) {
                $safetyCopy = $this->backups->restoreFromUpload($request->file('backup'));
            } elseif ($request->filled('filename')) {
                $safetyCopy = $this->backups->restoreStored((string) $request->input('filename'));
            } else {
                return $this->flashBack('Choose a backup file to restore.', 'danger');
            }
        } catch (Throwable $exception) {
            report($exception);

            return $this->flashBack('Restore failed: '.$exception->getMessage(), 'danger');
        }

        return $this->flashBack(
            "Database restored. A safety copy was saved first: {$safetyCopy}",
            'success'
        );
    }

    public function destroy(string $filename): RedirectResponse
    {
        try {
            $this->backups->delete($filename);
        } catch (RuntimeException $exception) {
            return $this->flashBack($exception->getMessage(), 'danger');
        }

        return $this->flashBack('Backup deleted.', 'success');
    }

    private function flashBack(string $message, string $type): RedirectResponse
    {
        session()->flash('message', $message);
        session()->flash('type', $type);

        return back();
    }
}
