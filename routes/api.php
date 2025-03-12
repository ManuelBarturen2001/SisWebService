<?php


use App\Http\Controllers\MigracionesController;
use App\Http\Controllers\ReniecController;
use App\Http\Controllers\SunatController;
use Illuminate\Support\Facades\Route;

Route::post('/migraciones/consultar', [MigracionesController::class, 'consultarMigraciones']);
Route::post('/reniec/consultar', [ReniecController::class, 'consultarReniec']);
Route::post('/sunat/consultar', [SunatController::class, 'consultarSunat'])->name('sunat.consultar');
// Route::post('/migraciones/consultar', [MigracionesController::class, 'consultarMigraciones'])->name('migraciones.consultar');
