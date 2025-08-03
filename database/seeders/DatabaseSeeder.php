<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        
        $this->call([
            OauthClientSeeder::class,
            RoleSeeder::class,
            PermissionSeeder::class,
            MenuSeeder::class,
        ]);
        
        $user = User::create([
            'name' => 'Test User',
            'email' => 'tes@tes.com',
            'password'  => '12345'
        ]);
        $user->assignRole('admin');
    }
}
