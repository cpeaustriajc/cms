<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContentDestroyRequest;
use App\Http\Requests\ContentIndexRequest;
use App\Http\Requests\ContentStoreRequest;
use App\Http\Requests\ContentUpdateRequest;
use App\Models\Content;
use App\Models\ContentStatus;
use App\Models\ContentType;
use App\Models\Locale;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class ContentController extends Controller
{
    public function index(ContentIndexRequest $request): JsonResponse|InertiaResponse
    {
        $contents = Content::query()
            ->with([
                'type:id,name,slug',
                'status:id,code',
                'routes' => fn ($query) => $query->where('is_primary', true),
                'fieldValues' => function ($query) {
                    $query->whereHas('field', fn ($field) => $field->where('handle', 'title'))
                        ->with('field:id,handle');
                },
            ])
            ->latest('id')
            ->when($request->string('type')->value(), function ($query, $type) {
                $query->whereHas('type', fn ($queryInstance) => $queryInstance->where('slug', $type));
            })
            ->when($request->string('status')->value(), function ($query, $status) {
                $query->whereHas('status', fn ($queryInstance) => $queryInstance->where('code', $status));
            })
            ->paginate(10)
            ->withQueryString()
            ->through(fn ($content) => [
                'id' => $content->id,
                'title' => optional($content->fieldValues->first())->value_string ??
                    optional($content->fieldValues->first())->value_text ?? '(untitled)',
                'fields' => $content->fieldValues->map(fn ($field) => [
                    'id' => $field->id,
                    'field_id' => $field->field_id,
                    'value_string' => $field->value_string,
                    'value_text' => $field->value_text,
                    'value_integer' => $field->value_integer,
                    'value_decimal' => $field->value_decimal,
                    'value_boolean' => $field->value_boolean,
                    'value_datetime' => $field->value_datetime,
                ]),
                'type' => $content->type->only('id', 'name', 'slug'),
                'status' => $content->status->only('id', 'code'),
                'published_at' => optional($content->published_at)->toIso8601String(),
                'path' => optional($content->routes->first())->path,
            ]);

        $types = ContentType::query()->get(['id', 'name', 'slug'])->map->only(['id', 'name', 'slug']);
        $statuses = ContentStatus::query()->get(['id', 'code', 'label'])->map->only(['id', 'code', 'label']);

        if ($request->wantsJson()) {
            return response()->json($contents, 200);
        }

        return Inertia::render('contents/index', [
            'contents' => $contents,
            'filters' => [
                'type' => $request->query('type'),
                'status' => $request->query('status'),
            ],
            'type' => $types,
            'statuses' => $statuses,
        ]);
    }

    public function create(): InertiaResponse
    {
        $types = ContentType::with([
            'fields' => function ($query) {
                $query->orderBy('sort_order');
            },
        ])->get(['id', 'name', 'slug', 'description'])
            ->map(fn ($type) => [
                'id' => $type->id,
                'name' => $type->name,
                'slug' => $type->slug,
                'description' => $type->description,
                'fields' => $type->fields->map(fn ($field) => [
                    'id' => $field->id,
                    'name' => $field->name,
                    'handle' => $field->handle,
                    'type' => (bool) $field->type,
                    'settings' => (bool) $field->settings,
                    'is_required' => (bool) $field->is_required,
                ]),
            ]);

        $statuses = ContentStatus::all(['id', 'code', 'label']);

        $locales = Locale::all(['id', 'code', 'name']);

        return Inertia::render('contents/create', [
            'types' => $types,
            'statuses' => $statuses,
            'locales' => $locales,
        ]);
    }

    public function store(ContentStoreRequest $request): JsonResponse|RedirectResponse
    {
        $data = $request->validated();

        if (empty($data['content_type_id'])) {
            $type = ContentType::where('slug', $data['content_type'])->firstOrFail();
            $data['content_type_id'] = $type->id;
        }

        $content = (new Content)->createFromPayload($data);

        if ($request->wantsJson()) {
            return response()->json([
                'id' => $content->id,
                'message' => 'Content created successfully.',
            ], 201);
        }

        return redirect()->route('contents.edit', ['content' => $content->id])
            ->with('success', 'Content created successfully.');
    }

    public function edit(Content $content): InertiaResponse
    {
        $content->load([
            'type:id,name,slug',
            'status:id,code',
            'routes:id,content_id,locale_id,path,is_primary',
            'fieldValues.field:id,handle,data_type,is_translatable,is_repeatable',
        ]);

        $types = ContentType::with([
            'fields' => function ($query) {
                $query->orderBy('sort_order');
            },
        ])->get(['id', 'name', 'slug', 'description'])
            ->map(fn ($type) => [
                'id' => $type->id,
                'name' => $type->name,
                'slug' => $type->slug,
                'description' => $type->description,
                'fields' => $type->fields->map(fn ($field) => [
                    'id' => $field->id,
                    'name' => $field->name,
                    'handle' => $field->handle,
                    'type' => (bool) $field->type,
                    'settings' => (bool) $field->settings,
                    'is_required' => (bool) $field->is_required,
                ]),
            ]);

        $statuses = ContentStatus::all(['id', 'code', 'label']);
        $locales = Locale::all(['id', 'code', 'name']);

        $values = [];
        foreach ($content->fieldValues as $value) {
            $handle = $value->field->handle;
            if (! $handle || array_key_exists($handle, $values)) {
                continue;
            }
            $values[$handle] = $value->value_string
                ?? $value->value_text
                ?? $value->value_integer
                ?? $value->value_decimal
                ?? $value->value_boolean
                ?? $value->value_datetime;
        }

        $route = $content->routes->first();

        return Inertia::render('contents/edit', [
            'content' => [
                'id' => $content->id,
                'content_type_id' => $content->content_type_id,
                'type_slug' => $content->type?->slug,
                'status_id' => $content->status_id,
                'status_code' => $content->status?->code,
                'published_at' => optional($content->published_at)?->format('Y-m-d\TH:i'),
                'values' => $values,
                'route' => $route ? [
                    'locale_id' => $route->locale_id,
                    'path' => $route->path,
                    'is_primary' => (bool) $route->is_primary,
                ] : null,
            ],
            'types' => $types,
            'statuses' => $statuses,
            'locales' => $locales,
        ]);
    }

    public function update(Content $content, ContentUpdateRequest $request): JsonResponse|RedirectResponse
    {
        $updated = $content->updateFromPayload($request->validated());

        if ($request->wantsJson()) {
            return response()->json([
                'id' => $updated->id,
                'message' => 'Content updated successfully.',
            ], 200);
        }

        return redirect()->route('contents.edit', ['content' => $content->id])
            ->with('success', 'Content updated successfully.');
    }

    public function destroy(Content $content, ContentDestroyRequest $request): JsonResponse|RedirectResponse
    {
        $content->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Content deleted.',
            ], 200);
        }

        return redirect()
            ->route('contents.index')
            ->with('success', 'Content deleted.');
    }
}
