<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Check if admin user already exists
        $adminExists = User::where('email', 'admin@ceramicartdinoyo.com')->first();
        
        if (!$adminExists) {
            User::create([
                'name' => 'Admin Ceramic Art Dinoyo',
                'email' => 'admin@ceramicartdinoyo.com',
                'email_verified_at' => now(),
                'password' => Hash::make('admin123'),
                'is_admin' => true,
                'phone' => '+62 341 123456'
            ]);
            
            $this->command->info('Admin user created successfully!');
            $this->command->info('Email: admin@ceramicartdinoyo.com');
            $this->command->info('Password: admin123');
        } else {
            // Update existing user to be admin
            $adminExists->update(['is_admin' => true]);
            $this->command->info('Existing user updated to admin!');
        }
    }
}
