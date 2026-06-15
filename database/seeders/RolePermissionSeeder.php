<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    private const MODULES = [
        'system',
        'enterprise',
        'security',
        'rooms',
        'customers',
        'services',
        'bookings',
        'payments',
        'reports',
        'pricing',
        'luggage',
        'inventory',
        'maintenance',
        'contracts',
        'integrations',
    ];

    private const ACTIONS = [
        'view',
        'create',
        'update',
        'delete',
        'approve',
        'export',
        'import',
        'print',
    ];

    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [];

        foreach (self::MODULES as $module) {
            foreach (self::ACTIONS as $action) {
                $name = "{$module}.{$action}";
                $permissions[$name] = Permission::firstOrCreate(
                    ['name' => $name, 'guard_name' => 'web']
                );
            }
        }

        $allPermissionNames = array_keys($permissions);

        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $superAdmin->syncPermissions($allPermissionNames);

        $companyAdmin = Role::firstOrCreate(['name' => 'company_admin', 'guard_name' => 'web']);
        $companyAdmin->syncPermissions($this->filterPermissions($allPermissionNames, [
            'system.view',
            'enterprise',
            'security',
            'rooms',
            'customers',
            'services',
            'bookings',
            'payments',
            'reports',
            'pricing',
            'luggage',
            'inventory',
            'maintenance',
            'contracts',
            'integrations.view',
        ]));

        $hotelManager = Role::firstOrCreate(['name' => 'hotel_manager', 'guard_name' => 'web']);
        $hotelManager->syncPermissions($this->filterPermissions($allPermissionNames, [
            'system.view',
            'enterprise.view',
            'security.view',
            'rooms',
            'customers',
            'services',
            'bookings',
            'payments',
            'reports',
            'pricing',
            'luggage',
            'inventory',
            'maintenance',
            'contracts',
        ]));

        $receptionist = Role::firstOrCreate(['name' => 'receptionist', 'guard_name' => 'web']);
        $receptionist->syncPermissions($this->filterPermissions($allPermissionNames, [
            'rooms.view',
            'customers.view',
            'customers.create',
            'customers.update',
            'services.view',
            'bookings',
            'payments.view',
            'payments.create',
            'payments.print',
            'luggage.view',
            'luggage.create',
            'luggage.update',
        ]));

        $staff = Role::firstOrCreate(['name' => 'staff', 'guard_name' => 'web']);
        $staff->syncPermissions($this->filterPermissions($allPermissionNames, [
            'rooms.view',
            'services.view',
            'inventory.view',
            'inventory.update',
            'maintenance.view',
            'maintenance.create',
            'maintenance.update',
            'luggage.view',
            'luggage.update',
        ]));

        $customer = Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);
        $customer->syncPermissions($this->filterPermissions($allPermissionNames, [
            'bookings.view',
            'bookings.create',
            'payments.view',
            'customers.view',
            'customers.update',
        ]));
    }

    private function filterPermissions(array $allPermissions, array $patterns): array
    {
        return array_values(array_filter($allPermissions, function (string $permission) use ($patterns) {
            foreach ($patterns as $pattern) {
                if (str_ends_with($pattern, '.') || str_contains($pattern, '.')) {
                    if ($permission === $pattern) {
                        return true;
                    }
                }

                if (! str_contains($pattern, '.')) {
                    if (str_starts_with($permission, $pattern.'.')) {
                        return true;
                    }
                }
            }

            return false;
        }));
    }
}
