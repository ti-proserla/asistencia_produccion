<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Producto;

class ProductoController extends Controller
{
    public function nuevo(){
        $producto=new Producto();
        $producto->codigo="02";
        $producto->nombre_producto="MANGO";
        $producto->save();
        return response()->json($producto);
    }
    public function editar(){
        $producto=Producto::where('id',1)->first();
        $producto->codigo="03";
        $producto->save();
        dd($producto);
        return response()->json($producto);
    }
    public function eliminar(){
        $producto=Producto::where('id',1)->first();
        $producto->delete();
        return response()->json($producto);
    }
}
