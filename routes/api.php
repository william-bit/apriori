<?php

use App\Http\Controllers\AlgorithmController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RuleController;
use App\Http\Controllers\TransactionController;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::middleware('auth:sanctum')->group(
    function () {
        Route::post('/rule', [RuleController::class, 'index'])->name('apriori');
        Route::get('/rule/get-status', [RuleController::class, 'getStatus'])->name('apriori.getStatus');
        Route::get('/transaction/get-data', [TransactionController::class, 'getData'])->name('transaction.data');
        Route::get('/product/get-data', [ProductController::class, 'getData'])->name('product.data');
        Route::get('/eoq/get-data', [AlgorithmController::class, 'eoq'])->name('product.data');
        Route::get('/moving/get-data', [AlgorithmController::class, 'movingAverage'])->name('product.data');
        Route::get('/product/get-data-rank', [ProductController::class, 'getDataMost'])->name('product.data');
        Route::post('/product', [ProductController::class, 'store'])->name('product.store');
        Route::post('/product/destroy', [ProductController::class, 'destroy'])->name('product.destroy');
        Route::get('/algorithm/get-data', [AlgorithmController::class, 'getData'])->name('algorithm.data');
        Route::post('/transaction', [TransactionController::class, 'index'])->name('transaction');
        Route::get('/algorithm/start', [AlgorithmController::class, 'index'])->name('algorithm.start');
        Route::get('/graphic', [TransactionController::class, 'graphic'])->name('grapic.start');
    }
);


require __DIR__ . '/auth.php';
