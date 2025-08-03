<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dashboard = Menu::create([
            'title' => 'Dashboard',
            'icon' => 'mdi-home',
            'route' => '/dashboard',
            'order' => 1
        ]);

        $users = Menu::create([
            'title' => 'User Management',
            'icon' => 'mdi-account',
            'route' => null,
            'order' => 2
        ]);

        Menu::create([
            'title' => 'Users',
            'icon' => 'mdi-account-multiple',
            'route' => '/users',
            'order' => 1,
            'parent_id' => $users->id,
            'permission_name' => 'user.read'
        ]);

        Menu::create([
            'title' => 'Roles',
            'icon' => 'mdi-shield-account',
            'route' => '/roles',
            'order' => 2,
            'parent_id' => $users->id
        ]);

        $settings = Menu::create([
            'title' => 'Settings',
            'icon' => 'mdi-account',
            'route' => null,
            'order' => 3
        ]);

        Menu::create([
            'title' => 'Menu',
            'icon' => 'mdi-home',
            'route' => '/menu',
            'order' => 1,
            'parent_id' => $settings->id
        ]);


        // Hubungkan dengan role
        $adminRole = Role::where('name', 'admin')->first();
        $userRole = Role::where('name', 'user')->first();

        $dashboard->roles()->attach([$adminRole->id, $userRole->id]);
        $users->roles()->attach([$adminRole->id]);
        $settings->roles()->attach([$adminRole->id]);
    }
}
