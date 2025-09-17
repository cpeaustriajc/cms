<?php

namespace Database\Seeders;

use App\Models\Content;
use App\Models\ContentFieldValue;
use App\Models\ContentStatus;
use App\Models\ContentType;
use App\Models\Field;
use App\Models\Locale;
use App\Models\Taxonomy;
use App\Models\Term;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class DemoContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $status = ContentStatus::firstWhere('code', 'published') ?? ContentStatus::firstWhere('code', 'draft');
        $locale = Locale::firstWhere('code', 'en-US') ?? Locale::first();

        $categoryTax = Taxonomy::firstOrCreate(['slug' => 'category'], ['name' => 'Category']);
        $tagTax = Taxonomy::firstOrCreate(['slug' => 'tag'], ['name' => 'Tag']);

        $news = Term::firstOrCreate([
            'taxonomy_id' => $categoryTax->id,
            'slug' => 'news',
        ], [
            'name' => 'News',
        ]);
        $tech = Term::firstOrCreate([
            'taxonomy_id' => $categoryTax->id,
            'slug' => 'tech',
        ], [
            'name' => 'Tech',
        ]);

        $blogType = ContentType::firstWhere('slug', 'blog_post');

        if ($blogType) {
            $content = Content::create([
                'content_type_id' => $blogType->id,
                'author_id' => null,
                'status_id' => $status?->id,
                'published_at' => now(),
            ]);

            $title = 'Hello World';
            $slug = Str::slug($title);

            $fields = Field::where('content_type_id', $blogType->id)->get()->keyBy('handle');

            // Values by handle
            $this->setValue($content->id, $fields, 'title', 'string', $title, $locale?->id);
            $this->setValue($content->id, $fields, 'slug', 'string', $slug, $locale?->id);
            $this->setValue($content->id, $fields, 'body', 'richtext', '<p>Welcome to your new CMS.</p>', $locale?->id);
            $this->setValue($content->id, $fields, 'excerpt', 'text', 'A friendly hello world.', $locale?->id);

        }
    }

    protected function setValue(int $contentId, Collection $fieldsByHandle, string $handle, string $type, mixed $value, ?int $localeId): void
    {
        $field = $fieldsByHandle->get($handle);

        if (!$field) {
            return;
        }

        $data = [
            'content_id' => $contentId,
            'field_id' => $field->id,
            'locale_id' => $localeId,
            'sort_order' => 0,
        ];

        switch ($type) {
            case 'string':
            case 'richtext':
                $data['value_string'] = (string) $value;
            case 'text':
                $data['value_text'] = (string) $value;
                break;
            case 'integer':
            case 'reference':
                $data['value_integer'] = (int) $value;
                break;
            case 'decimal':
                $data['value_decimal'] = (float) $value;
                break;
            case 'boolean':
                $data['value_boolean'] = (bool) $value;
                break;
            case 'datetime':
                $data['value_datetime'] = $value instanceof \DateTimeInterface ? $value : now();
                break;
        }

        ContentFieldValue::create($data);
    }
}
