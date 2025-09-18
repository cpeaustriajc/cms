<?php

namespace Database\Seeders;

use App\Models\RelationType;
use Illuminate\Database\Seeder;

class RelationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            ['name' => 'Related', 'slug' => 'related', 'description' => 'General related content', 'is_bidirectional' => true],
            ['name' => 'Has Lesson', 'slug' => 'has-lesson', 'description' => 'Course has lesson', 'is_bidirectional' => false],
            ['name' => 'Part of Series', 'slug' => 'part-of-series', 'description' => 'Belongs to series', 'is_bidirectional' => false],
            ['name' => 'Upsell', 'slug' => 'upsell', 'description' => 'Suggest this content as upsell', 'is_bidirectional' => false],
        ];

        foreach ($types as $data) {
            RelationType::firstOrCreate(['slug' => $data['slug']], $data);
        }
    }
}
