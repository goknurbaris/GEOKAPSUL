<?php

namespace Database\Seeders;

use App\Models\Badge;
use Illuminate\Database\Seeder;

class BadgeSeeder extends Seeder
{
    public function run(): void
    {
        $badges = [
            // Kapsül Oluşturma Rozetleri
            [
                'slug' => 'first-capsule',
                'name' => 'İlk Kapsül',
                'description' => 'İlk zaman kapsülünü oluşturdun!',
                'icon' => '🎉',
                'color' => 'indigo',
                'xp_reward' => 50,
                'criteria' => ['type' => 'capsule_count', 'value' => 1],
            ],
            [
                'slug' => 'capsule-collector-5',
                'name' => 'Koleksiyoncu',
                'description' => '5 kapsül oluşturdun',
                'icon' => '📦',
                'color' => 'emerald',
                'xp_reward' => 100,
                'criteria' => ['type' => 'capsule_count', 'value' => 5],
            ],
            [
                'slug' => 'capsule-master-25',
                'name' => 'Kapsül Ustası',
                'description' => '25 kapsül oluşturdun',
                'icon' => '🏆',
                'color' => 'gold',
                'xp_reward' => 250,
                'criteria' => ['type' => 'capsule_count', 'value' => 25],
            ],
            [
                'slug' => 'capsule-legend-100',
                'name' => 'Kapsül Efsanesi',
                'description' => '100 kapsül oluşturdun!',
                'icon' => '👑',
                'color' => 'gold',
                'xp_reward' => 1000,
                'criteria' => ['type' => 'capsule_count', 'value' => 100],
            ],

            // Kapsül Açma Rozetleri
            [
                'slug' => 'first-discovery',
                'name' => 'İlk Keşif',
                'description' => 'İlk kapsülü keşfettin!',
                'icon' => '🔍',
                'color' => 'cyan',
                'xp_reward' => 30,
                'criteria' => ['type' => 'capsule_opened', 'value' => 1],
            ],
            [
                'slug' => 'explorer-10',
                'name' => 'Kaşif',
                'description' => '10 kapsül keşfettin',
                'icon' => '🧭',
                'color' => 'emerald',
                'xp_reward' => 150,
                'criteria' => ['type' => 'capsule_opened', 'value' => 10],
            ],
            [
                'slug' => 'adventurer-50',
                'name' => 'Maceracı',
                'description' => '50 kapsül keşfettin',
                'icon' => '🗺️',
                'color' => 'violet',
                'xp_reward' => 500,
                'criteria' => ['type' => 'capsule_opened', 'value' => 50],
            ],

            // Mesafe Rozetleri
            [
                'slug' => 'walker-1km',
                'name' => 'Yürüyüşçü',
                'description' => 'Toplam 1 km yürüdün',
                'icon' => '🚶',
                'color' => 'emerald',
                'xp_reward' => 50,
                'criteria' => ['type' => 'distance', 'value' => 1],
            ],
            [
                'slug' => 'hiker-10km',
                'name' => 'Gezgin',
                'description' => 'Toplam 10 km yürüdün',
                'icon' => '🥾',
                'color' => 'cyan',
                'xp_reward' => 200,
                'criteria' => ['type' => 'distance', 'value' => 10],
            ],
            [
                'slug' => 'marathon-42km',
                'name' => 'Maratoncu',
                'description' => '42 km tamamladın!',
                'icon' => '🏃',
                'color' => 'gold',
                'xp_reward' => 500,
                'criteria' => ['type' => 'distance', 'value' => 42],
            ],
            [
                'slug' => 'globetrotter-100km',
                'name' => 'Dünya Gezgini',
                'description' => '100 km keşfettin!',
                'icon' => '🌍',
                'color' => 'violet',
                'xp_reward' => 1000,
                'criteria' => ['type' => 'distance', 'value' => 100],
            ],

            // Seviye Rozetleri
            [
                'slug' => 'level-5',
                'name' => 'Yükselen Yıldız',
                'description' => 'Seviye 5\'e ulaştın',
                'icon' => '⭐',
                'color' => 'amber',
                'xp_reward' => 100,
                'criteria' => ['type' => 'level', 'value' => 5],
            ],
            [
                'slug' => 'level-10',
                'name' => 'Parlayan Yıldız',
                'description' => 'Seviye 10\'a ulaştın',
                'icon' => '🌟',
                'color' => 'gold',
                'xp_reward' => 250,
                'criteria' => ['type' => 'level', 'value' => 10],
            ],
            [
                'slug' => 'level-25',
                'name' => 'Süper Nova',
                'description' => 'Seviye 25\'e ulaştın!',
                'icon' => '💫',
                'color' => 'violet',
                'xp_reward' => 500,
                'criteria' => ['type' => 'level', 'value' => 25],
            ],

            // Kategori Rozetleri
            [
                'slug' => 'gift-giver',
                'name' => 'Hediye Sever',
                'description' => '5 hediye kapsülü oluşturdun',
                'icon' => '🎁',
                'color' => 'rose',
                'xp_reward' => 150,
                'criteria' => ['type' => 'category', 'category' => 'gift', 'value' => 5],
            ],
            [
                'slug' => 'mystery-maker',
                'name' => 'Gizem Ustası',
                'description' => '5 gizem kapsülü oluşturdun',
                'icon' => '🔮',
                'color' => 'violet',
                'xp_reward' => 150,
                'criteria' => ['type' => 'category', 'category' => 'mystery', 'value' => 5],
            ],
            [
                'slug' => 'treasure-hunter',
                'name' => 'Hazine Avcısı',
                'description' => '3 hazine avı zinciri oluşturdun',
                'icon' => '💎',
                'color' => 'cyan',
                'xp_reward' => 300,
                'criteria' => ['type' => 'category', 'category' => 'treasure', 'value' => 3],
            ],
        ];

        foreach ($badges as $badge) {
            Badge::updateOrCreate(
                ['slug' => $badge['slug']],
                $badge
            );
        }
    }
}
