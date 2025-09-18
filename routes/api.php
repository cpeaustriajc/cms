<?php

use App\Http\Controllers\AssetController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\ContentTypeController;
use App\Http\Controllers\FieldController;
use App\Http\Controllers\TaxonomyController;
use App\Http\Controllers\TermController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Http\Middleware\CheckToken;

Route::name('api.')->middleware(['auth:api', CheckToken::using('user:read')])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

Route::name('api.')->middleware(['auth:api'])->group(function () {
    Route::apiResources([
        'contents' => ContentController::class,
        'content-types' => ContentTypeController::class,
        'fields' => FieldController::class,
        'taxonomies' => TaxonomyController::class,
        'terms' => TermController::class,
        'assets' => AssetController::class,
        'comments' => CommentController::class,
    ]);
});
