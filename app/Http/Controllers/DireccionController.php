<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\ConsultaController;
use App\Models\Direccion;

class DireccionController extends Controller
{
    protected $copomexService;

    public function __construct(CopomexService $copomexService)
    {
        $this->copomexService = $copomexService;
    }

    // Mostrar el formulario y la tabla
    public function index()
    {
        $estados = $this->copomexService->obtenerEstados();
        $direcciones = Direccion::all();
        return view('direccion', compact('direcciones', 'estados'));
    }

    // Obtener municipios por estado
    public function obtenerMunicipios(Request $request)
    {
        $estado = $request->estado;
        $municipios = $this->copomexService->obtenerMunicipios($estado);
        return response()->json($municipios);
    }

    // Obtener colonias por código postal
    public function obtenerColonias(Request $request)
    {
        $codigoPostal = $request->codigo_postal;
        $colonias = $this->copomexService->obtenerColonias($codigoPostal);
        return response()->json($colonias);
    }

    // Guardar en la base de datos
    public function store(Request $request)
    {
        $direccion = new Direccion();
        $direccion->estado = $request->estado;
        $direccion->municipio = $request->municipio;
        $direccion->localidad = $request->localidad;
        $direccion->colonia = $request->colonia;
        $direccion->codigo_postal = $request->codigo_postal;
        $direccion->save();

        return redirect()->back()->with('success', 'Datos guardados correctamente.');
    }

    // Eliminar un registro
    public function destroy($id)
    {
        $direccion = Direccion::find($id);
        $direccion->delete();

        return redirect()->back()->with('success', 'Registro eliminado correctamente.');
    }
}