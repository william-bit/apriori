<?php

use App\Http\Controllers\AlgorithmController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\EOQController;
use App\Http\Controllers\MovingAverageController;
use App\Http\Controllers\ReportController;
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

Route::get('/example/product', [DownloadController::class, 'product'])->name('example.product');
Route::get('/example/transaction', [DownloadController::class, 'transaction'])->name('example.transaction');
Route::get('/pdf/eoq', [ReportController::class, 'EOQ'])->name('report.eoq');
Route::get('/pdf/apriori', [ReportController::class, 'apriori'])->name('report.apriori');
Route::get('/pdf/product', [ReportController::class, 'product'])->name('report.product');
Route::get('/pdf/transaction', [ReportController::class, 'transaction'])->name('report.transaction');
Route::get('/pdf/sold-product', [ReportController::class, 'soldProduct'])->name('report.soldProduct');
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
