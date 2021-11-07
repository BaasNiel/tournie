<?php

use App\Http\Controllers\ClientExceptionController;
use App\Http\Controllers\ScreenshotController;
use App\Http\Controllers\ScreenshotMappingController;
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

Route::get('/players', function () {
    return Inertia::render('Players');
})->name('players');

Route::get('/matches', function () {
    return Inertia::render('Matches');
})->name('matches');

Route::get('/screenshots', function () {
    return Inertia::render('Screenshots');
})->name('screenshots');

Route::post('/screenshot', [ScreenshotController::class, 'upload'])->name('upload');

Route::post('/client-exception/option', [ClientExceptionController::class, 'post'])
    ->name('clientExceptionPost');


Route::get('/screenshot/mapping/text', [ScreenshotMappingController::class, 'findTextFromCoordinates'])->name('findTextFromCoordinates');
Route::get('/screenshot/mapping/text/coordinates', [ScreenshotMappingController::class, 'findTextCoordinates'])->name('findTextCoordinates');
Route::post('/screenshot/mapping/anchor', [ScreenshotMappingController::class, 'saveAnchor'])->name('saveAnchor');
Route::post('/screenshot/mapping/field', [ScreenshotMappingController::class, 'saveField'])->name('saveField');

require __DIR__.'/auth.php';
