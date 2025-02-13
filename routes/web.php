<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ConsultaController;

Route::get('/', [ConsultaController::class, 'index'])->name('direccion');
Route::post('/consultas', [ConsultaController::class, 'store'])->name('consultas.store');
Route::delete('/consultas/{id}', [ConsultaController::class, 'destroy'])->name('consultas.destroy');

Route::get('/municipios/{estado}', [ConsultaController::class, 'getMunicipios']);
Route::get('/colonias/{municipio}', [ConsultaController::class, 'getColonias']);
Route::get('/codigo-postal/{estado}/{municipio}/{colonia}', [ConsultaController::class, 'getCodigoPostal']);
