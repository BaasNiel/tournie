<?php

use App\Http\Controllers\SnapshotController;
use Illuminate\Foundation\Application;
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


Route::get('snapshots', [SnapshotController::class, 'index'])->name('index');
Route::post('snapshot', [SnapshotController::class, 'submit'])->name('submit');


require __DIR__.'/auth.php';
