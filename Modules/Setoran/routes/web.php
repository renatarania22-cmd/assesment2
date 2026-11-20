<?php

use Illuminate\Support\Facades\Route;
use Modules\Setoran\Http\Controllers\SetoranController;

Route::middleware(['auth'])->group(function () {
    Route::get('/setoran', [SetoranController::class, 'index'])->name('setoran.index');
    Route::get('/setoran/{setoran}', [SetoranController::class, 'show'])->name('setoran.show');
    Route::delete('/setoran/{setoran}', [SetoranController::class, 'destroy'])->name('setoran.destroy');

    Route::middleware(['role:nasabah'])->group(function () {
        Route::post('/setoran', [SetoranController::class, 'store'])->name('setoran.store');
        Route::get('/setoran/{setoran}/edit', [SetoranController::class, 'edit'])->name('setoran.edit');
        Route::put('/setoran/{setoran}', [SetoranController::class, 'update'])->name('setoran.update');
    });

    Route::middleware(['role:admin'])->group(function () {
        Route::patch('/setoran/{setoran}/keputusan', [SetoranController::class, 'keputusan'])->name('setoran.keputusan');
    });
});