<?php

namespace Database\Seeders;

use App\Models\ContentStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContentStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            ['code' => 'draft', 'label' => 'Draft'],
            ['code' => 'scheduled', 'label' => 'Scheduled'],
            ['code' => 'published', 'label' => 'Published'],
            ['code' => 'archived', 'label' => 'Archived'],
        ];

        foreach ($statuses as $status) {
            ContentStatus::firstOrCreate(['code' => $status['code']], $status);
        }
    }
}
