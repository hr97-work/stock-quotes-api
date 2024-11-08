<?php

use App\Http\Controllers\QuoteController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/quotes', [QuoteController::class, 'index']);
Route::get('/polygon-quotes', [QuoteController::class, 'getPolygonQuotes']);
Route::post('/quotes/history', [QuoteController::class, 'fillQuotesHistory']);

