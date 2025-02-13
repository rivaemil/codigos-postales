<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Consulta;

class ConsultaController extends Controller
{
    public function index()
    {
        $estados = $this->getEstadosFromApi();
        $consultas = Consulta::latest()->get();
        return view('direccion', compact('estados', 'consultas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'estado' => 'required|string',
            'municipio' => 'required|string',
            'colonia' => 'required|string',
            'codigo_postal' => 'required|string'
        ]);

        Consulta::create([
            'estado' => $request->estado,
            'municipio' => $request->municipio,
            'colonia' => $request->colonia,
            'codigo_postal' => $request->codigo_postal
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $consulta = Consulta::find($id);
        
        if ($consulta) {
            $consulta->delete();
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Consulta no encontrada']);
    }


    private function getEstadosFromApi()
    {
        $token = env('COPOMEX_API_KEY');
        $response = Http::get("https://api.copomex.com/query/get_estados?token={$token}");

        if ($response->successful()) {
            $data = $response->json();
            return $data['response']['estado'] ?? []; // Extraer el array de estados
        }

        return [];
    }

    public function getMunicipios($estado)
    {
        $token = env('COPOMEX_API_KEY');
        $response = Http::get("https://api.copomex.com/query/get_municipio_por_estado/{$estado}?token={$token}");

        if ($response->successful()) {
            $data = $response->json();
            return $data['response']['municipios'] ?? []; // Extraer el array de municipios
        }

        return [];
    }
    
    public function getColonias($municipio)
    {
        $token = env('COPOMEX_API_KEY');
        $response = Http::get("https://api.copomex.com/query/get_colonia_por_municipio/{$municipio}?token={$token}");

        if ($response->successful()) {
            $data = $response->json();
            return $data['response']['colonia'] ?? []; // Ajusta según la estructura de la respuesta
        }

        return [];
    }

    public function getCodigoPostal($estado,$municipio,$colonia)
    {
        $token = env('COPOMEX_API_KEY');
        $response = Http::get("https://api.copomex.com/query/get_cp_advanced/{$estado}?limit=10&municipio={$municipio}&colonia={$colonia}&token={$token}");

        if ($response->successful()) {
            $data = $response->json();
            $codigo_postal = $data['response']['cp'][0] ?? ''; 
    
            return response()->json(['codigo_postal' => $codigo_postal]);
        }
    
        return response()->json(['codigo_postal' => '']);
    }

}