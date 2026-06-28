<?php

namespace App\Services\Visa;

use App\Models\User;
use App\Models\Visa\Staff;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class StaffAccountService
{
    public function syncLoginAccount(Staff $staff, ?string $email, ?string $password): void
    {
        $email = $email ? strtolower(trim($email)) : null;

        if (! $email) {
            return;
        }

        $existingUser = User::query()
            ->where('email', $email)
            ->when($staff->user_id, fn ($q) => $q->where('id', '!=', $staff->user_id))
            ->exists();

        if ($existingUser) {
            throw ValidationException::withMessages([
                'login_email' => ['This email is already used by another account.'],
            ]);
        }

        $role = Role::firstOrCreate(['name' => 'field_staff']);

        if ($staff->user_id) {
            $user = User::query()->findOrFail($staff->user_id);
            $user->update([
                'name' => $staff->full_name,
                'email' => $email,
                'phone' => $staff->phone,
            ]);

            if ($password) {
                $user->update(['password' => Hash::make($password)]);
            }
        } else {
            if (! $password) {
                throw ValidationException::withMessages([
                    'login_password' => ['Password is required when creating a staff login.'],
                ]);
            }

            $user = User::create([
                'name' => $staff->full_name,
                'email' => $email,
                'phone' => $staff->phone,
                'password' => Hash::make($password),
            ]);

            $user->assignRole($role);
            $staff->update(['user_id' => $user->id]);
        }

        if (! $user->hasRole('field_staff')) {
            $user->assignRole($role);
        }
    }
}
