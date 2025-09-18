<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssetDestroyRequest;
use App\Http\Requests\AssetIndexRequest;
use App\Http\Requests\AssetStoreRequest;
use App\Http\Requests\AssetUpdateRequest;
use App\Models\Asset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class AssetController extends Controller
{
    public function index(AssetIndexRequest $request): JsonResponse|InertiaResponse
    {
        $assets = Asset::query()
            ->with('creator:id,name')
            ->when($request->string('search'), fn ($query, $search) => $query->where('filename', 'like', "%{$search}%")
                ->orWhere('alt_text', 'like', "%{$search}%"))
            ->when($request->string('mime_type'), fn ($query, $mimeType) => $query->where('mime_type', 'like', "{$mimeType}%"))
            ->latest('id')
            ->paginate(24);

        $mimeTypes = ['image', 'video', 'audio', 'application', 'text'];

        if ($request->wantsJson()) {
            return response()->json($assets);
        }

        return Inertia::render('assets/index', [
            'assets' => $assets,
            'mimeTypes' => $mimeTypes,
            'filters' => [
                'search' => $request->query('search'),
                'mime_type' => $request->query('mime_type'),
            ],
        ]);
    }

    public function create(): InertiaResponse
    {
        return Inertia::render('assets/create');
    }

    public function store(AssetStoreRequest $request): JsonResponse|RedirectResponse
    {
        $file = $request->file('file');
        $disk = config('filesystems.default');
        $path = $file->store('assets', $disk);

        $asset = Asset::create([
            'disk' => $disk,
            'path' => $path,
            'filename' => $file->getClientOriginalName(),
            'ext' => $file->getClientOriginalExtension(),
            'mime_type' => $file->getMimeType(),
            'size_bytes' => $file->getSize(),
            'alt_text' => $request->validated('alt_text'),
            'created_by_id' => auth()->id(),
        ]);

        if (str_starts_with($asset->mime_type, 'image/')) {
            $imagePath = Storage::disk($disk)->path($path);
            if (file_exists($imagePath)) {
                [$width, $height] = getimagesize($imagePath);
                $asset->update(['width' => $width, 'height' => $height]);
            }
        }

        if ($request->wantsJson()) {
            return response()->json([
                'id' => $asset->id,
                'message' => 'Asset uploaded successfully.',
            ], 201);
        }

        return redirect()->route('assets.index')
            ->with('success', 'Asset uploaded successfully.');
    }

    public function show(Asset $asset, Request $request): JsonResponse|InertiaResponse
    {
        $asset->load(['creator', 'contents']);

        if ($request->wantsJson()) {
            return response()->json($asset);
        }

        $diskAdapter = Storage::disk($asset->disk);
        /** @var \Illuminate\Filesystem\FilesystemAdapter $diskAdapter */
        $url = method_exists($diskAdapter, 'url') ? $diskAdapter->url($asset->path) : null;

        return Inertia::render('assets/show', [
            'asset' => $asset,
            'url' => $url,
        ]);
    }

    public function edit(Asset $asset): InertiaResponse
    {
        $diskAdapter = Storage::disk($asset->disk);
        /** @var \Illuminate\Filesystem\FilesystemAdapter $diskAdapter */
        $url = method_exists($diskAdapter, 'url') ? $diskAdapter->url($asset->path) : null;

        return Inertia::render('assets/edit', [
            'asset' => $asset,
            'url' => $url,
        ]);
    }

    public function update(AssetUpdateRequest $request, Asset $asset): JsonResponse|RedirectResponse
    {
        $asset->update($request->validated());

        if ($request->wantsJson()) {
            return response()->json([
                'id' => $asset->id,
                'message' => 'Asset updated successfully.',
            ]);
        }

        return redirect()->route('assets.index')
            ->with('success', 'Asset updated successfully.');
    }

    public function destroy(Asset $asset, AssetDestroyRequest $request): JsonResponse|RedirectResponse
    {
        Storage::disk($asset->disk)->delete($asset->path);

        $asset->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Asset deleted successfully.',
            ]);
        }

        return redirect()->route('assets.index')
            ->with('success', 'Asset deleted successfully.');
    }
}
