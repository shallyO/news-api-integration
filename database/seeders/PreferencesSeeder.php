<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PreferencesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('preferences')->insert([
            'preferred_sources' => json_encode(['newsapi', 'bbc', 'guardian']),
            'preferred_categories' => json_encode(['technology', 'business', 'sports']),
            'preferred_authors' => json_encode(['John Doe', 'Jane Smith']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
