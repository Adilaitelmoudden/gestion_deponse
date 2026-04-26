<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class DefaultCategoriesSeeder extends Seeder
{
    public function run()
    {
        $categories = [
            // Dépenses (expense)
            ['name' => 'Nourriture', 'color' => '#FF6B6B', 'type' => 'expense', 'icon' => 'fa-utensils'],
            ['name' => 'Transport', 'color' => '#4ECDC4', 'type' => 'expense', 'icon' => 'fa-car'],
            ['name' => 'Logement', 'color' => '#45B7D1', 'type' => 'expense', 'icon' => 'fa-home'],
            ['name' => 'Shopping', 'color' => '#96CEB4', 'type' => 'expense', 'icon' => 'fa-shopping-bag'],
            ['name' => 'Divertissement', 'color' => '#FFEAA7', 'type' => 'expense', 'icon' => 'fa-film'],
            ['name' => 'Santé', 'color' => '#DDA0DD', 'type' => 'expense', 'icon' => 'fa-heartbeat'],
            ['name' => 'Éducation', 'color' => '#98D8C8', 'type' => 'expense', 'icon' => 'fa-graduation-cap'],
            ['name' => 'Factures', 'color' => '#FDCB6E', 'type' => 'expense', 'icon' => 'fa-file-invoice'],
            
            // Revenus (income)
            ['name' => 'Salaire', 'color' => '#2ECC71', 'type' => 'income', 'icon' => 'fa-money-bill-wave'],
            ['name' => 'Freelance', 'color' => '#3498DB', 'type' => 'income', 'icon' => 'fa-laptop-code'],
            ['name' => 'Investissements', 'color' => '#9B59B6', 'type' => 'income', 'icon' => 'fa-chart-line'],
            ['name' => 'Cadeaux', 'color' => '#E74C3C', 'type' => 'income', 'icon' => 'fa-gift'],
        ];

        foreach($categories as $category) {
            Category::create([
                'user_id' => null,
                'name' => $category['name'],
                'color' => $category['color'],
                'type' => $category['type'],
                'icon' => $category['icon'],
                'is_default' => true
            ]);
        }
        
        echo "✅ " . count($categories) . " catégories par défaut créées\n";
    }
}