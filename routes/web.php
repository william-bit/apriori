<?php

use App\Http\Controllers\AlgorithmController;
use App\Http\Controllers\EOQController;
use App\Http\Controllers\MovingAverageController;
use App\System\MovingAverage;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

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

Route::get('/test', [MovingAverageController::class, 'store'])->name('algorithm.start');
Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::get('/migrate', function () {
    Artisan::call('migrate');
    echo "Migrate<br>";
});

Route::get('/seed', function () {
    Artisan::call('db:seed');
    echo "Migrate<br>";
});
