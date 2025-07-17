<?php

use App\Http\Controllers\Api\ServiceOrderController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/service-orders', [ServiceOrderController::class, 'store']);
    Route::get('/service-orders', [ServiceOrderController::class, 'index']);
});
