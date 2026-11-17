<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'admin@evently.test'],
            ['name' => 'Evently Admin', 'password' => Hash::make('password')]
        );

        $user->assignRole('super_admin');
    }
}
