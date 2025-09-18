<?php

namespace App\Http\Controllers;

use App\Http\Requests\TermShowRequest;
use App\Http\Requests\TermStoreRequest;
use App\Http\Requests\TermUpdateRequest;
use App\Models\Taxonomy;
use App\Models\Term;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class TermController extends Controller
{
    public function index(\App\Http\Requests\TermIndexRequest $request): JsonResponse|InertiaResponse
    {
        $term = Term::query()
            ->with(['taxonomy:id,name,slug', 'parent:id,name'])
            ->withCount('contents')
            ->when($request->string('taxonomy')->value(),
                fn ($query, $taxonomy) => $query->whereHas('taxonomy', fn ($query) => $query->where('slug', $taxonomy)))
            ->latest('id')
            ->paginate(15);

        $taxonomies = Taxonomy::query()->get(['id', 'name', 'slug']);

        if ($request->wantsJson()) {
            return response()->json($term);
        }

        return Inertia::render('terms/index', [
            'terms' => $term,
            'taxonomies' => $taxonomies,
            'filters' => [
                'search' => $request->query('search'),
                'taxonomy' => $request->query('taxonomy'),
            ],
        ]);
    }

    public function create(Request $request): InertiaResponse
    {
        $taxonomies = Taxonomy::query()->get(['id', 'name', 'slug']);
        $selectedTaxonomy = $request->string('taxonomy')->value();

        $parentTerms = [];
        if ($selectedTaxonomy) {
            $parentTerms = Term::query()
                ->whereHas('taxonomy', fn ($query) => $query->where('slug', $selectedTaxonomy))
                ->get(['id', 'name']);
        }

        return Inertia::render('terms/create', [
            'taxonomies' => $taxonomies,
            'parentTerms' => $parentTerms,
            'selectedTaxonomy' => $selectedTaxonomy,
        ]);
    }

    public function store(TermStoreRequest $request): JsonResponse|RedirectResponse
    {
        $term = Term::create($request->validated());

        if ($request->wantsJson()) {
            return response()->json([
                'id' => $term->id,
                'message' => 'Term created successfully.',
            ], 201);
        }

        return redirect()->route('terms.index')
            ->with('success', 'Term created successfully.');
    }

    public function show(Term $term, TermShowRequest $request): JsonResponse|InertiaResponse
    {
        $term->load(['taxonomy', 'parent', 'children', 'contents' => fn ($query) => $query->latest()->limit(10)]);

        if ($request->wantsJson()) {
            return response()->json($term);
        }

        return Inertia::render('terms/show', [
            'term' => $term,
        ]);
    }

    public function edit(Term $term): InertiaResponse
    {
        $term->load('taxonomy');
        $taxonomies = Taxonomy::query()->get(['id', 'name', 'slug']);
        $parentTerms = Term::query()
            ->where('id', '!=', $term->id)
            ->whereNull('parent_id')
            ->get(['id', 'name']);

        return Inertia::render('terms/edit', [
            'term' => $term,
            'taxonomies' => $taxonomies,
            'parentTerms' => $parentTerms,
        ]);
    }

    public function update(TermUpdateRequest $request, Term $term): JsonResponse|RedirectResponse
    {
        $term->update($request->validated());

        if ($request->wantsJson()) {
            return response()->json([
                'id' => $term->id,
                'message' => 'Term updated successfully.',
            ]);
        }

        return redirect()->route('terms.index')
            ->with('success', 'Term updated successfully.');
    }

    public function destroy(Term $term, \App\Http\Requests\TermDestroyRequest $request): JsonResponse|RedirectResponse
    {
        if ($term->children()->exists()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Cannot delete term with child terms. Please reassign or remove child terms first.',
                ], 422);
            }

            return redirect()->route('terms.index')
                ->withErrors('Cannot delete term with child terms. Please reassign or remove child terms first.');
        }

        $term->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Term deleted successfully.',
            ]);
        }

        return redirect()->route('terms.index')
            ->with('success', 'Term deleted successfully.');
    }
}
