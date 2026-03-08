<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\SearchController;

// Route::post('/tours/{tour}/price', [TourPriceController::class, 'calculate']); // Use web route instead


Route::post('/chat', [ChatController::class, 'chat']);

// Search autocomplete
Route::get('/search/autocomplete', [SearchController::class, 'autocomplete']);

// Payment routes removed
