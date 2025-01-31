<?php

use App\Http\Controllers\PurchaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/devices', function (Request $request) {
    logger("POST Body", $request->all());
});

Route::controller(PurchaseController::class)->prefix('purchases')->group(function () {
    Route::get('', 'index');
    Route::post('', 'store');
});
