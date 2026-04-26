<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        // Admin principal
        $admin = User::create([
            'name' => 'Admin Principal',
            'email' => 'admin@gestdepenses.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'is_active' => true,
            'phone' => '0612345678',
        ]);
        
        echo "✅ Admin créé : admin@gestdepenses.com / admin123\n";
        
        // Utilisateur normal
        $user = User::create([
            'name' => 'Jean Dupont',
            'email' => 'user@test.com',
            'password' => Hash::make('user123'),
            'role' => 'user',
            'is_active' => true,
            'phone' => '0687654321',
        ]);
        
        echo "✅ Utilisateur créé : user@test.com / user123\n";
    }
}