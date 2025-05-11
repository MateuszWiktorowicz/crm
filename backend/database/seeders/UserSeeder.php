<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Mateusz Wiktorowicz',
            'email' => 'wiktorowicz.mat@gmail.com',
            'password' => Hash::make('password'),
            'roles' => ['admin'],
            'marker' => 'admin-marker',
        ]);
    }
}
