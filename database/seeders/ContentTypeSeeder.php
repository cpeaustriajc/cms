<?php

namespace Database\Seeders;

use App\Models\ContentType;
use App\Models\Field;
use Illuminate\Database\Seeder;

class ContentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $createType = function (string $name, string $slug, array $fields): ContentType {
            $type = ContentType::firstOrCreate(['slug' => $slug], ['name' => $name, 'description' => null]);

            $order = 0;
            foreach ($fields as $field) {
                Field::firstOrCreate([
                    'content_type_id' => $type->id,
                    'handle' => $field['handle'],
                ], [
                    'name' => $field['name'],
                    'data_type' => $field['data_type'],
                    'is_required' => $field['is_required'] ?? false,
                    'is_unique' => $field['is_unique'] ?? false,
                    'is_translatable' => $field['is_translatable'] ?? false,
                    'is_repeatable' => $field['is_repeatable'] ?? false,
                    'sort_order' => $field['sort_order'] ?? $order++,
                ]);
            }

            return $type;
        };

        // Blog Post
        $createType('Blog Post', 'blog_post', [
            ['name' => 'Title', 'handle' => 'title', 'data_type' => 'string', 'is_required' => true, 'is_translatable' => true],
            ['name' => 'Slug', 'handle' => 'slug', 'data_type' => 'string', 'is_required' => true, 'is_unique' => true],
            ['name' => 'Body', 'handle' => 'body', 'data_type' => 'richtext', 'is_required' => true, 'is_translatable' => true],
            ['name' => 'Excerpt', 'handle' => 'excerpt', 'data_type' => 'text', 'is_translatable' => true],
            ['name' => 'Featured Image', 'handle' => 'featured_image', 'data_type' => 'asset'],
        ]);

        // Product
        $createType('Product', 'product', [
            ['name' => 'Name', 'handle' => 'name', 'data_type' => 'string', 'is_required' => true],
            ['name' => 'Slug', 'handle' => 'slug', 'data_type' => 'string', 'is_required' => true, 'is_unique' => true],
            ['name' => 'SKU', 'handle' => 'sku', 'data_type' => 'string', 'is_unique' => true],
            ['name' => 'Price', 'handle' => 'price', 'data_type' => 'decimal', 'is_required' => true],
            ['name' => 'Description', 'handle' => 'description', 'data_type' => 'richtext'],
            ['name' => 'Gallery', 'handle' => 'gallery', 'data_type' => 'asset', 'is_repeatable' => true],
        ]);

        // Forum Post
        $createType('Forum Post', 'forum_post', [
            ['name' => 'Title', 'handle' => 'title', 'data_type' => 'string', 'is_required' => true],
            ['name' => 'Body', 'handle' => 'body', 'data_type' => 'richtext', 'is_required' => true],
        ]);

        // Course + Lesson
        $createType('Course', 'course', [
            ['name' => 'Title', 'handle' => 'title', 'data_type' => 'string', 'is_required' => true],
            ['name' => 'Summary', 'handle' => 'summary', 'data_type' => 'text'],
            ['name' => 'Hero Image', 'handle' => 'hero_image', 'data_type' => 'asset'],
        ]);

        $createType('Lesson', 'lesson', [
            ['name' => 'Title', 'handle' => 'title', 'data_type' => 'string', 'is_required' => true],
            ['name' => 'Body', 'handle' => 'body', 'data_type' => 'richtext'],
            ['name' => 'Video', 'handle' => 'video', 'data_type' => 'asset'],
        ]);
    }
}
