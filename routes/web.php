<?php

use App\Http\Controllers\AssetController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\ContentTypeController;
use App\Http\Controllers\FieldController;
use App\Http\Controllers\TaxonomyController;
use App\Http\Controllers\TermController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');

    Route::resources([
        'contents' => ContentController::class,
        'content-types' => ContentTypeController::class,
        'fields' => FieldController::class,
        'taxonomies' => TaxonomyController::class,
        'terms' => TermController::class,
        'assets' => AssetController::class,
        'comments' => CommentController::class,
    ]);
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
