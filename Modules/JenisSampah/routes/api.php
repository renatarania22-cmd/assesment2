<?php

use Illuminate\Support\Facades\Route;
use Modules\JenisSampah\Http\Controllers\JenisSampahController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('jenissampahs', JenisSampahController::class)->names('jenissampah');
});
