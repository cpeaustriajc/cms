<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentDestroyRequest;
use App\Http\Requests\CommentIndexRequest;
use App\Http\Requests\CommentShowRequest;
use App\Http\Requests\CommentStoreRequest;
use App\Http\Requests\CommentUpdateRequest;
use App\Models\Comment;
use App\Models\CommentStatus;
use App\Models\Content;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class CommentController extends Controller
{
    public function index(CommentIndexRequest $request): JsonResponse|InertiaResponse
    {
        $comments = Comment::query()
            ->with(['content:id,slug', 'author:id,name', 'status:id,name'])
            ->when($request->string('content')->value(), function ($query, $content) {
                $query->whereHas('content', fn ($query) => $query->where('slug', $content));
            })
            ->when($request->string('status')->value(), function ($query, $status) {
                $query->whereHas('status', fn ($query) => $query->where('code', $status));
            })
            ->when($request->string('search')->value(), function ($query, $search) {
                $query->where('body', 'like', "%{$search}%")
                    ->orWhereHas('author', fn ($query) => $query->where('name', 'like', "%{$search}%"));
            })
            ->latest('id')
            ->paginate(15);

        $statuses = CommentStatus::query()->get(['id', 'name', 'code']);

        if ($request->wantsJson()) {
            return response()->json($comments);
        }

        return Inertia::render('comments/index', [
            'comments' => $comments,
            'statuses' => $statuses,
            'filters' => [
                'search' => $request->query('search'),
                'content' => $request->query('content'),
                'status' => $request->query('status'),
            ],
        ]);
    }

    public function create(Request $request): InertiaResponse
    {
        $contents = Content::query()->get(['id', 'slug']);
        $statuses = CommentStatus::query()->get(['id', 'name', 'code']);
        $selectedContent = $request->query('content');

        return Inertia::render('comments/create', [
            'contents' => $contents,
            'statuses' => $statuses,
            'selectedContent' => $selectedContent,
        ]);
    }

    public function store(CommentStoreRequest $request): JsonResponse|RedirectResponse
    {
        $comment = Comment::create([
            ...$request->validated(),
            'user_id' => auth()->id(),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'id' => $comment->id,
                'message' => 'Comment created successfully.',
            ], 201);
        }

        return redirect()->route('comments.index')
            ->with('success', 'Comment created successfully.');
    }

    public function show(Comment $comment, CommentShowRequest $request): JsonResponse|InertiaResponse
    {
        $comment->load(['content', 'author', 'status', 'parent']);

        if ($request->wantsJson()) {
            return response()->json($comment);
        }

        return Inertia::render('comments/show', [
            'comment' => $comment,
        ]);
    }

    public function edit(Comment $comment): InertiaResponse
    {
        $comment->load(['content', 'status']);
        $contents = Content::query()->get(['id', 'slug']);
        $statuses = CommentStatus::query()->get(['id', 'name', 'code']);

        return Inertia::render('comments/edit', [
            'comment' => $comment,
            'contents' => $contents,
            'statuses' => $statuses,
        ]);
    }

    public function update(CommentUpdateRequest $request, Comment $comment): JsonResponse|RedirectResponse
    {
        $comment->update($request->validated());

        if ($request->wantsJson()) {
            return response()->json([
                'id' => $comment->id,
                'message' => 'Comment updated successfully.',
            ]);
        }

        return redirect()->route('comments.index')
            ->with('success', 'Comment updated successfully.');
    }

    public function destroy(Comment $comment, CommentDestroyRequest $request): JsonResponse|RedirectResponse
    {
        $comment->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Comment deleted successfully.',
            ]);
        }

        return redirect()->route('comments.index')
            ->with('success', 'Comment deleted successfully.');
    }
}
