<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\PreferenceController;
use App\Http\Controllers\SourceController;
use Illuminate\Support\Facades\Route;

Route::get('articles', [ArticleController::class, 'fetchArticles']);
Route::get('sources', [SourceController::class, 'getSources']);

Route::get('preferences', [PreferenceController::class, 'getPreferences']);
Route::put('preferences', [PreferenceController::class, 'updatePreferences']);



