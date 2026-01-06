<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@serverreport.com',
                'password' => Hash::make('password123'),
                'email_verified_at' => Carbon::now(),
                'role' => 'admin',
            ],
            [
                'name' => 'Test User',
                'email' => 'test@serverreport.com',
                'password' => Hash::make('password123'),
                'email_verified_at' => Carbon::now(),
                'role' => 'staff',
            ],
            [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => Hash::make('password123'),
                'email_verified_at' => Carbon::now(),
                'role' => 'staff',
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'password' => Hash::make('password123'),
                'email_verified_at' => Carbon::now(),
                'role' => 'staff',
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }

        $this->command->info('Users seeded successfully!');
        $this->command->info('Default password for all users: password123');
        $this->command->info('Admin user: admin@serverreport.com (role: admin)');
        $this->command->info('Staff users: test@serverreport.com, john@example.com, jane@example.com (role: staff)');
    }
}
