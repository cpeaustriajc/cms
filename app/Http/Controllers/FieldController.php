<?php

namespace App\Http\Controllers;

use App\Http\Requests\FieldDestroyRequest;
use App\Http\Requests\FieldIndexRequest;
use App\Http\Requests\FieldStoreRequest;
use App\Models\ContentType;
use App\Models\Field;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class FieldController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(FieldIndexRequest $request): JsonResponse|InertiaResponse
    {
        $fields = Field::query()
            ->with('contentType:id,name,slug')
            ->when($request->string('content_type')->value(), function ($query, $contentType) {
                $query->whereHas('contentType', fn ($query) => $query->where('slug', $contentType));
            })
            ->when($request->string('search')->value(), function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('handle', 'like', "%{$search}%");
            })
            ->orderBy('sort_order')
            ->paginate(15);

        $contentTypes = ContentType::query()->get(['id', 'name', 'slug']);

        if ($request->wantsJson()) {
            return response()->json($fields, 200);
        }

        return Inertia::render('fields/index', [
            'fields' => $fields,
            'contentTypes' => $contentTypes,
            'filters' => [
                'content_type' => $request->query('content_type'),
                'search' => $request->query('search'),
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $contentTypes = ContentType::query()->get(['id', 'name', 'slug']);
        $selectedContentType = $request->string('content_type');

        return Inertia::render('fields/create', [
            'contentTypes' => $contentTypes,
            'selectedContentType' => $selectedContentType,
            'fieldTypes' => Field::ALLOWED_TYPES,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FieldStoreRequest $request)
    {
        $field = Field::create($request->validated());

        if ($request->wantsJson()) {
            return response()->json([
                'id' => $field->id,
                'message' => 'Field created successfully',
            ], 201);
        }

        return redirect()->route('fields.index')
            ->with('success', 'Field created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Field $field, Request $request)
    {
        $field->load('contentType');

        if ($request->wantsJson()) {
            return response()->json($field);
        }

        return Inertia::render('fields/show', [
            'field' => $field,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Field $field): InertiaResponse
    {
        $field->load('contentType');

        $contentTypes = ContentType::query()->get(['id', 'name', 'slug']);

        return Inertia::render('fields/edit', [
            'field' => $field,
            'contentTypes' => $contentTypes,
            'fieldTypes' => Field::ALLOWED_TYPES,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Field $field)
    {
        $field->update($request->all());

        if ($request->wantsJson()) {
            return response()->json([
                'id' => $field->id,
                'message' => 'Field updated successfully',
            ]);
        }

        return redirect()->route('fields.index')
            ->with('success', 'Field updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Field $field, FieldDestroyRequest $request): JsonResponse|RedirectResponse
    {
        $field->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Field deleted successfully',
            ]);
        }

        return redirect()->route('fields.index')
            ->with('success', 'Field deleted successfully');
    }
}
