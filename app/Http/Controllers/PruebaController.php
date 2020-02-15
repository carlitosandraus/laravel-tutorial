<?php

namespace App\Http\Controllers;

use App\Models\Prueba;
use Illuminate\Http\Request;
use Faker;
use Carbon\Carbon;

class PruebaController extends Controller
{
    public function listado(Request $request)
    {

        $rand = $request->get('rand');

    
        $total = Prueba::all()->count();

        $datos = Prueba::paginate(20);

        if ($datos->count() == 0) {
            return response()->json(['status'=>401, "message" => "No hay información"], 400);
        }
        
        return view('listado', ['datos'=> $datos, 'total' => $total]);
    }

    public function borrar(Request $request, $id)
    {
        $prueba = Prueba::find($id);
        $prueba->delete();
    }

    public function obtener(Request $request, $id)
    {
        $prueba = Prueba::find($id);
        return view('registro', ['dato'=> $prueba]);
    }

    public function nuevo(Request $request)
    {
        $prueba = new Prueba;

        return view('registro', ['dato'=> $prueba]);
    }

    public function actualizar(Request $request, $id)
    {
        $data = $request->all();
        $prueba = Prueba::find($id);
        $prueba->updated_at = Carbon::now();
        return $this->guardar($prueba, $data);
    }


    public function crear(Request $request)
    {
        $data = $request->all();
        $prueba = new Prueba;
        return $this->guardar($prueba, $data);
    }

    public function guardar(Prueba $dato, $parametros)
    {
        $dato->nombre = $parametros['nombre'];
        $dato->edad = $parametros['edad'];
        $dato->save();
        return redirect('/web/'.$dato->id);
    }
}
