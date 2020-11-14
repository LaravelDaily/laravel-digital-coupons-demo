<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionRoleTableSeeder extends Seeder
{
    public function run()
    {
        $admin_permissions = Permission::all();
        Role::findOrFail(1)->permissions()->sync($admin_permissions->pluck('id'));
        $user_permissions = $admin_permissions->filter(function ($permission) {
            return in_array($permission->title, ['coupon_access', 'coupon_show', 'purchase_access', 'purchase_show', 'profile_password_edit']);
        });
        Role::findOrFail(2)->permissions()->sync($user_permissions);
    }
}
