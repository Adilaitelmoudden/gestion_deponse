<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class DefaultCategoriesSeeder extends Seeder
{
    public function run()
    {
        $categories = [
            // ── Dépenses (10) ──────────────────────────────────────────────
            ['name' => 'Nourriture',        'color' => '#FF6B6B', 'type' => 'expense', 'icon' => 'fa-utensils'],
            ['name' => 'Transport',          'color' => '#4ECDC4', 'type' => 'expense', 'icon' => 'fa-car'],
            ['name' => 'Logement',           'color' => '#45B7D1', 'type' => 'expense', 'icon' => 'fa-home'],
            ['name' => 'Shopping',           'color' => '#96CEB4', 'type' => 'expense', 'icon' => 'fa-shopping-bag'],
            ['name' => 'Divertissement',     'color' => '#FFEAA7', 'type' => 'expense', 'icon' => 'fa-film'],
            ['name' => 'Santé',              'color' => '#DDA0DD', 'type' => 'expense', 'icon' => 'fa-heartbeat'],
            ['name' => 'Éducation',          'color' => '#98D8C8', 'type' => 'expense', 'icon' => 'fa-graduation-cap'],
            ['name' => 'Factures',           'color' => '#FDCB6E', 'type' => 'expense', 'icon' => 'fa-file-invoice'],
            ['name' => 'Voyage',             'color' => '#A29BFE', 'type' => 'expense', 'icon' => 'fa-plane'],
            ['name' => 'Abonnements',        'color' => '#FD79A8', 'type' => 'expense', 'icon' => 'fa-credit-card'],

            // ── Revenus (10) ───────────────────────────────────────────────
            ['name' => 'Salaire',            'color' => '#2ECC71', 'type' => 'income',  'icon' => 'fa-money-bill-wave'],
            ['name' => 'Freelance',          'color' => '#3498DB', 'type' => 'income',  'icon' => 'fa-laptop-code'],
            ['name' => 'Investissements',    'color' => '#9B59B6', 'type' => 'income',  'icon' => 'fa-chart-line'],
            ['name' => 'Cadeaux',            'color' => '#E74C3C', 'type' => 'income',  'icon' => 'fa-gift'],
            ['name' => 'Location',           'color' => '#F39C12', 'type' => 'income',  'icon' => 'fa-building'],
            ['name' => 'Remboursement',      'color' => '#1ABC9C', 'type' => 'income',  'icon' => 'fa-hand-holding-usd'],
            ['name' => 'Vente',              'color' => '#E67E22', 'type' => 'income',  'icon' => 'fa-tag'],
            ['name' => 'Pension / Retraite', 'color' => '#7F8C8D', 'type' => 'income',  'icon' => 'fa-user-tie'],
            ['name' => 'Bourse / Dividendes','color' => '#27AE60', 'type' => 'income',  'icon' => 'fa-coins'],
            ['name' => 'Autres revenus',     'color' => '#BDC3C7', 'type' => 'income',  'icon' => 'fa-plus-circle'],
        ];

        foreach ($categories as $category) {
            // Éviter les doublons si on re-lance le seeder
            Category::firstOrCreate(
                ['name' => $category['name'], 'is_default' => true],
                [
                    'user_id'    => null,
                    'color'      => $category['color'],
                    'type'       => $category['type'],
                    'icon'       => $category['icon'],
                    'is_default' => true,
                ]
            );
        }

        echo "✅ " . count($categories) . " catégories par défaut créées (10 dépenses + 10 revenus)\n";
    }
}
