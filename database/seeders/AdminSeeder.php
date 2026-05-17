<?php
namespace Database\Seeders;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(['email' => 'superadmin@nexus.com'], [
            'name'              => 'NEXUS Super Admin',
            'password'          => Hash::make('superadmin123456'),
            'role'              => User::ROLE_SUPER_ADMIN,
            'email_verified_at' => now(),
        ]);
        User::updateOrCreate(['email' => 'admin@nexus.com'], [
            'name'              => 'NEXUS Admin',
            'password'          => Hash::make('admin123456'),
            'role'              => User::ROLE_ADMIN,
            'email_verified_at' => now(),
        ]);
        $this->command->info('Super Admin: superadmin@nexus.com / superadmin123456');
        $this->command->info('Admin: admin@nexus.com / admin123456');
    }
}
