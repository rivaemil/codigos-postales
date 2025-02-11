<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DireccionController;
use App\Http\Controllers\ConsultaController;

// Ruta para la página principal
Route::get('/', [ConsultaController::class, 'index'])->name('welcome');

// Ruta para almacenar una consulta
Route::post('/consultas', [ConsultaController::class, 'store'])->name('consultas.store');

// Ruta para eliminar una consulta
Route::delete('/consultas/{id}', [ConsultaController::class, 'destroy'])->name('consultas.destroy');

// Rutas para los selects dinámicos
Route::get('/municipios/{estado}', [ConsultaController::class, 'getMunicipios']);
Route::get('/localidades/{municipio}', [ConsultaController::class, 'getLocalidades']);
Route::get('/colonias/{codigoPostal}', [ConsultaController::class, 'getColonias']);
Route::get('/', function () {
    return view('direccion');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
