<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ArticleController;

Route::middleware('api')->group(function () {
    Route::get('/articles', [ArticleController::class, 'index']);
});
