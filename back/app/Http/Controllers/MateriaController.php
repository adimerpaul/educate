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
        $materias = Materia::when($q, function ($query) use ($q) {
            $t = "%{$q}%";
            $query->where('nombre', 'like', $t)
                ->orWhere('descripcion', 'like', $t)
                ->orWhere('docente', 'like', $t);
        })
            ->orderBy('id', 'desc')
            ->get();

        return response()->json($materias);
    }

    // POST /materias
    public function store(Request $request)
    {
        $user = $request->user();
        $request->merge(['user_id' => $user->id]);
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
