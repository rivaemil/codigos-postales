<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers;

class Consulta extends Model
{
    protected $fillable = ['estado', 'municipio', 'colonia', 'codigo_postal'];

}
