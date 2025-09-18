<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaxonomyShowRequest;
use App\Http\Requests\TaxonomyStoreRequest;
use App\Http\Requests\TaxonomyUpdateRequest;
use App\Models\Taxonomy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class TaxonomyController extends Controller
{
    public function index(\App\Http\Requests\TaxonomyIndexRequest $request): JsonResponse|InertiaResponse
    {
        $taxonomies = Taxonomy::query()
            ->withCount('terms')
            ->when($request->string('search')->value(), function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            })
            ->latest('id')
            ->paginate(15);

        if ($request->wantsJson()) {
            return response()->json($taxonomies);
        }

        return Inertia::render('taxonomies/index', [
            'taxonomies' => $taxonomies,
            'filters' => [
                'search' => $request->query('search'),
            ],
        ]);
    }

    public function create(): InertiaResponse
    {
        return Inertia::render('taxonomies/create');
    }

    public function store(TaxonomyStoreRequest $request): JsonResponse|RedirectResponse
    {
        $taxonomy = Taxonomy::create($request->validated());

        if ($request->wantsJson()) {
            return response()->json([
                'id' => $taxonomy->id,
                'message' => 'Taxonomy created successfully',
            ], 201);
        }

        return redirect()->route('taxonomies.index')
            ->with('success', 'Taxonomy created successfully');
    }

    public function show(Taxonomy $taxonomy, TaxonomyShowRequest $request): JsonResponse|InertiaResponse
    {
        $taxonomy->load(['terms' => fn ($query) => $query->whereNull('parent_id')->with('children')->orderBy('name')]);

        if ($request->wantsJson()) {
            return response()->json($taxonomy);
        }

        return Inertia::render('taxonomies/show', [
            'taxonomy' => $taxonomy,
        ]);
    }

    public function edit(Taxonomy $taxonomy): InertiaResponse
    {
        return Inertia::render('taxonomies/edit', [
            'taxonomy' => $taxonomy,
        ]);
    }

    public function update(TaxonomyUpdateRequest $request, Taxonomy $taxonomy): JsonResponse|RedirectResponse
    {
        $taxonomy->update($request->validated());

        if ($request->wantsJson()) {
            return response()->json([
                'id' => $taxonomy->id,
                'message' => 'Taxonomy updated successfully',
            ]);
        }

        return redirect()->route('taxonomies.index')
            ->with('success', 'Taxonomy updated successfully');
    }

    public function destroy(Taxonomy $taxonomy, \App\Http\Requests\TaxonomyDestroyRequest $request): JsonResponse|RedirectResponse
    {
        if ($taxonomy->terms()->exists()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Cannot delete taxonomy with associated terms.',
                ], 422);
            }

            return redirect()->route('taxonomies.index')
                ->withErrors('Cannot delete taxonomy with existing terms.');
        }

        $taxonomy->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Taxonomy deleted successfully.',
            ]);
        }

        return redirect()->route('taxonomies.index')
            ->with('success', 'Taxonomy deleted successfully.');
    }
}
