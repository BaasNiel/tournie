<?php

use App\Http\Controllers\ClientExceptionController;
use App\Http\Controllers\ScoreboardController;
use App\Http\Controllers\ScoreboardMappingController;
use App\Http\Controllers\PlayerController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Log');
})->name('log');

Route::resource('players', PlayerController::class);

Route::get('/matches', function () {
    return Inertia::render('Matches');
})->name('matches');

// To-do: (move to api)
Route::post('/client-exception/option', [ClientExceptionController::class, 'post'])
    ->name('clientExceptionPost');

Route::prefix('scoreboard')->group(function () {

    // (temp) Use the landing route for upload testing
    Route::get('/', function () {
        return Inertia::render('Scoreboard');
    })->name('scoreboard');

    // To-do: (move to api)
    Route::post('/', [ScoreboardController::class, 'upload']);
    Route::prefix('mapping')->group(function () {
        Route::get('/lines-from-coordinates', [ScoreboardMappingController::class, 'getLinesFromCoordinates']);
        Route::get('/coordinates-from-text', [ScoreboardMappingController::class, 'getCoordinatesFromText']);
        Route::post('/slot', [ScoreboardMappingController::class, 'saveSlot']);
    });
});

require __DIR__.'/auth.php';
