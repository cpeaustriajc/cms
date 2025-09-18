<?php

use App\Models\ContentType;
use App\Models\Field;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('renders fields index', function () {
    $user = User::factory()->create();
    actingAs($user);

    $type = ContentType::factory()->create();
    Field::factory()->create(['content_type_id' => $type->id]);

    get('/fields')->assertOk()->assertInertia(fn (Assert $page) => $page
        ->component('fields/index')
        ->has('fields')
        ->has('contentTypes')
        ->has('filters', fn (Assert $filters) => $filters
            ->has('content_type')
            ->has('search')
        )
    );
});

it('renders fields create', function () {
    $user = User::factory()->create();
    actingAs($user);

    ContentType::factory()->count(2)->create();

    get('/fields/create')->assertOk()->assertInertia(fn (Assert $page) => $page
        ->component('fields/create')
        ->has('contentTypes')
        ->has('selectedContentType')
        ->has('fieldTypes')
    );
});

it('renders fields show', function () {
    $user = User::factory()->create();
    actingAs($user);

    $type = ContentType::factory()->create();
    $field = Field::factory()->create(['content_type_id' => $type->id]);

    get("/fields/{$field->id}")->assertOk()->assertInertia(fn (Assert $page) => $page
        ->component('fields/show')
        ->has('field')
    );
});

it('renders fields edit', function () {
    $user = User::factory()->create();
    actingAs($user);

    $type = ContentType::factory()->create();
    $field = Field::factory()->create(['content_type_id' => $type->id]);

    get("/fields/{$field->id}/edit")->assertOk()->assertInertia(fn (Assert $page) => $page
        ->component('fields/edit')
        ->has('field')
        ->has('contentTypes')
        ->has('fieldTypes')
    );
});
