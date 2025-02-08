<?php

use App\Http\Controllers\API\BankDetailController;
use App\Http\Controllers\API\WalletController;
use App\Models\MainAppStatus;
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:customer')->group(function () {
    Route::post('/bank-details', [BankDetailController::class, 'store']);
    Route::post('/deposit', [WalletController::class, 'deposit']);
    Route::post('/withdraw', [WalletController::class, 'withdraw']);
    Route::get('get-status', function () {
        $status = MainAppStatus::first();
        $data = $status->status;
        return response()->json($data, 200);
    });
});
