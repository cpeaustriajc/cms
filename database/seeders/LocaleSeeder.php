<?php

namespace Database\Seeders;

use App\Models\Locale;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LocaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locales = [
            ['code' => 'en-US', 'name' => 'English (United States)'],
            ['code' => 'en-GB', 'name' => 'English (United Kingdom)']
        ];

        foreach ($locales as $locale) {
            Locale::firstOrCreate(['code' => $locale['code']], $locale);
        }
    }
}
