<?php

namespace Database\Seeders\Permissions;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class VisaPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            'visa-bookings' => ['list', 'show', 'edit', 'confirm', 'assign', 'cancel', 'accept', 'reject'],
            'tracking' => ['list', 'show', 'advance', 'complete'],
            'guest-requests' => ['list', 'show', 'advance', 'note'],
            'programs' => ['list', 'create', 'edit', 'delete'],
            'service-packages' => ['list', 'create', 'edit', 'delete'],
            'additional-services' => ['list', 'create', 'edit', 'delete'],
            'vehicles' => ['list', 'create', 'edit', 'delete'],
            'staff' => ['list', 'create', 'edit', 'delete'],
            'offers' => ['list', 'create', 'edit', 'delete'],
            'vouchers' => ['list', 'create', 'edit', 'delete'],
            'membership-plans' => ['list', 'create', 'edit', 'delete'],
            'memberships' => ['list', 'show', 'create', 'edit', 'delete'],
            'visa-payments' => ['list', 'show'],
            'app-notifications' => ['list', 'show', 'create'],
            'visa-settings' => ['edit'],
        ];

        $permissionIds = [];
        foreach ($modules as $module => $actions) {
            foreach ($actions as $action) {
                $permissionIds[] = Permission::updateOrCreate(['name' => "{$module}.{$action}"])->id;
            }
        }

        $admin = Role::firstOrCreate(['name' => 'Administrator']);
        $admin->givePermissionTo($permissionIds);

        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdmin->syncPermissions($permissionIds);

        $operatorPerms = Permission::whereIn('name', [
            'visa-bookings.list', 'visa-bookings.show', 'visa-bookings.edit', 'visa-bookings.confirm', 'visa-bookings.assign', 'visa-bookings.cancel', 'visa-bookings.accept', 'visa-bookings.reject',
            'tracking.list', 'tracking.show', 'tracking.advance', 'tracking.complete',
            'guest-requests.list', 'guest-requests.show', 'guest-requests.advance', 'guest-requests.note',
            'staff.list', 'staff.create', 'staff.edit',
            'programs.list', 'service-packages.list', 'vehicles.list',
            'clients.list', 'clients.show',
            'visa-settings.edit',
        ])->pluck('name');
        $operator = Role::firstOrCreate(['name' => 'operator']);
        $operator->syncPermissions($operatorPerms);

        $supportPerms = Permission::whereIn('name', [
            'visa-bookings.list', 'visa-bookings.show',
            'clients.list', 'clients.show', 'clients.edit',
            'app-notifications.list', 'app-notifications.show', 'app-notifications.create',
            'memberships.list', 'memberships.show', 'memberships.create', 'memberships.edit',
            'membership-plans.list', 'membership-plans.create', 'membership-plans.edit',
            'vouchers.list', 'vouchers.create', 'vouchers.edit',
            'visa-settings.edit',
        ])->pluck('name');
        $support = Role::firstOrCreate(['name' => 'support']);
        $support->syncPermissions($supportPerms);

        $fieldStaffPerms = Permission::whereIn('name', [
            'guest-requests.list', 'guest-requests.show', 'guest-requests.advance', 'guest-requests.note',
        ])->pluck('name');
        $fieldStaff = Role::firstOrCreate(['name' => 'field_staff']);
        $fieldStaff->syncPermissions($fieldStaffPerms);
    }
}
