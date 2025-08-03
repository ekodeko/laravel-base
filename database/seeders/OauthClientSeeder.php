<?php

namespace Database\Seeders;

use App\Models\OauthClient;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OauthClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        OauthClient::create([
            'name' => 'My Frontend App',
            'client_id' => 'frontend_app',
            'client_secret' => hash('sha256', 'secret123'),
        ]);
    }
}
