<?php

namespace App\Http\Controllers;

use App\Models\Materia;
use Illuminate\Http\Request;

class MateriaController extends Controller
{
    // GET /materias
    public function index(Request $request)
    {
        $q = $request->get('q');
        $user = $request->user();
        $materias = Materia::when($q, function ($query) use ($q) {
            $t = "%{$q}%";
            $query->where('nombre', 'like', $t)
                ->orWhere('descripcion', 'like', $t)
                ->orWhere('docente', 'like', $t);
        })
            ->orderBy('id', 'desc')
            ->where('user_id', $user->id)
            ->get();

        return response()->json($materias);
    }

    // POST /materias
    public function store(Request $request)
    {
        $colores = ['#F44336', '#E91E63', '#9C27B0', '#673AB7', '#3F51B5', '#2196F3', '#03A9F4', '#00BCD4', '#009688', '#4CAF50', '#8BC34A', '#CDDC39', '#FFEB3B', '#FFC107', '#FF9800', '#FF5722'];

        $user = $request->user();
        $request->merge(['user_id' => $user->id]);
        if (!$request->has('color')) {
            $request->merge(['color' => $colores[array_rand($colores)]]);
        }
        $materia = Materia::create($request->all());
        return response()->json($materia, 201);
    }

    // GET /materias/{materia}
    public function show(Materia $materia)
    {
        return response()->json($materia);
    }

    // PUT/PATCH /materias/{materia}
    public function update(Request $request, Materia $materia)
    {
        $materia->update($request->all());
        return response()->json($materia);
    }

    // DELETE /materias/{materia}
    public function destroy(Materia $materia)
    {
        $materia->delete();
        return response()->json(['message' => 'Eliminado']);
    }
}
