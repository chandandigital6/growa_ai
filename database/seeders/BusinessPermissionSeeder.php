<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class BusinessPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'business.view',
            'business.create',
            'business.update',
            'business.delete',

            'products.view',
            'products.create',
            'products.update',
            'products.delete',

            'services.view',
            'services.create',
            'services.update',
            'services.delete',

            'orders.view',
            'orders.manage',

            'customers.view',
            'customers.manage',

            'staff.view',
            'staff.manage',

            'reviews.view',
            'reviews.manage',

            'offers.view',
            'offers.manage',

            'analytics.view',

            'social.view',
            'social.manage',

            'ai.use',

            'subscriptions.view',

            'gallery.view',
            'gallery.manage',

            'business.hours.manage',
            'business.social.manage',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate([
                'name' => $name,
                'guard_name' => 'web',
            ]);
        }

        app(PermissionRegistrar::class)
            ->forgetCachedPermissions();
    }
}
