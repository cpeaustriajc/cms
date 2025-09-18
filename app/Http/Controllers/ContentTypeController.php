<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContentTypeShowRequest;
use App\Http\Requests\ContentTypeStoreRequest;
use App\Http\Requests\ContentTypeUpdateRequest;
use App\Models\ContentType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class ContentTypeController extends Controller
{
    public function index(\App\Http\Requests\ContentTypeIndexRequest $request): JsonResponse|InertiaResponse
    {
        $contentTypes = ContentType::query()->withCount(['contents', 'fields'])
            ->latest('id')
            ->when($request->string('search')->value(), function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->paginate(15);

        if ($request->wantsJson()) {
            return response()->json($contentTypes);
        }

        return Inertia::render('content-types/index', [
            'contentTypes' => $contentTypes,
        ]);
    }

    public function create(): InertiaResponse
    {
        return Inertia::render('content-types/create');
    }

    public function store(ContentTypeStoreRequest $request): JsonResponse|RedirectResponse
    {
        $contentType = ContentType::create($request->validated());

        if ($request->wantsJson()) {
            return response()->json([
                'id' => $contentType->id,
                'message' => 'Content type created successfully',
            ], 201);
        }

        return redirect()->route('content-types.index')
            ->with('success', 'Content type created successfully');
    }

    public function show(ContentType $contentType, ContentTypeShowRequest $request): JsonResponse|InertiaResponse
    {
        $contentType->load([
            'fields',
            'contents' => function ($query) {
                $query->latest()->limit(10);
            },
        ]);

        if ($request->wantsJson()) {
            return response()->json($contentType);
        }

        return Inertia::render('content-types/show', [
            'contentType' => $contentType,
        ]);
    }

    public function edit(ContentType $contentType): InertiaResponse
    {
        $contentType->load('fields');

        return Inertia::render('content-types/edit', [
            'contentType' => $contentType,
        ]);
    }

    public function update(ContentTypeUpdateRequest $request, ContentType $contentType): JsonResponse|RedirectResponse
    {
        if ($contentType->contents()->exists()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Cannot update content type with existing contents.',
                ], 422);
            }

            return redirect()->route('content-types.index')
                ->withErrors('Cannot delete content types with existing contents.');
        }

        $contentType->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Content type deleted successfully.',
            ]);
        }

        return redirect()->route('content-types.index')
            ->with('success', 'Content type deleted successfully.');
    }
}
