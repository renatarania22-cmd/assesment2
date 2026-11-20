<?php

use Illuminate\Support\Facades\Route;
use Modules\Setoran\Http\Controllers\SetoranController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('setorans', SetoranController::class)->names('setoran');
});
