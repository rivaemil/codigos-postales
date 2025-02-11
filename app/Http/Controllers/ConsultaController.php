<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\{Estado, Consulta};

class ConsultaController extends Controller
{
    // Mostrar el historial de consultas
    public function index()
    {
        // Obtener todos los estados desde la API de Copomex
        $estados = $this->getEstadosFromApi();
        $estados = Estado::all();

        // Obtener todas las consultas ordenadas por fecha de creación
        $consultas = Consulta::latest()->get();

        // Pasar las variables $estados y $consultas a la vista
        return view('direccion', compact('estados', 'consultas'));
    }

    // Almacenar una nueva consulta
    public function store(Request $request)
    {
        $request->validate([
            'estado' => 'required|string',
            'municipio' => 'required|string',
            'localidad' => 'required|string',
            'codigo_postal' => 'required|string|max:5',
            'colonia' => 'required|string'
        ]);

        Consulta::create($request->all());

        return response()->json(['success' => 'Consulta almacenada']);
    }

    // Eliminar una consulta
    public function destroy($id)
    {
        Consulta::findOrFail($id)->delete();
        return redirect()->route('consultas.index')->with('success', 'Consulta eliminada');
    }

    // Método para obtener estados desde la API de Copomex
    private function getEstadosFromApi()
    {
        $token = env('COPOMEX_API_KEY');
        $response = Http::get("https://api.copomex.com/query/get_estados?token={$token}");

        if ($response->successful()) {
            return $response->json();
        }

        return [];
    }

    // Método para obtener municipios por estado desde la API de Copomex
    public function getMunicipios($estado)
    {
        $token = env('COPOMEX_API_KEY');
        $response = Http::get("https://api.copomex.com/query/get_municipios_por_estado/{$estado}?token={$token}");

        if ($response->successful()) {
            return $response->json();
        }

        return [];
    }

    // Método para obtener localidades por municipio desde la API de Copomex
    public function getLocalidades($municipio)
    {
        $token = env('COPOMEX_API_KEY');
        $response = Http::get("https://api.copomex.com/query/get_localidades_por_municipio/{$municipio}?token={$token}");

        if ($response->successful()) {
            return $response->json();
        }

        return [];
    }

    // Método para obtener colonias por código postal desde la API de Copomex
    public function getColonias($codigoPostal)
    {
        $token = env('COPOMEX_API_KEY');
        $response = Http::get("https://api.copomex.com/query/get_colonias_por_cp/{$codigoPostal}?token={$token}");

        if ($response->successful()) {
            return $response->json();
        }

        return [];
    }
}