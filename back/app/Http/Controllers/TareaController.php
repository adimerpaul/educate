<?php

namespace App\Http\Controllers;

use App\Models\Tarea;
use Illuminate\Http\Request;

class TareaController extends Controller
{
    // GET /tareas
    public function index(Request $request)
    {
        $q          = $request->get('q');
        $materiaId  = $request->get('materia_id');
        $estado     = $request->get('estado');

        $tareas = Tarea::with(['materia'])
            ->when($materiaId, fn($qry) => $qry->where('materia_id', $materiaId))
            ->when($estado, fn($qry) => $qry->where('estado', $estado))
            ->when($q, function ($qry) use ($q) {
                $t = "%{$q}%";
                $qry->where(function ($sub) use ($t) {
                    $sub->where('tipo_tarea', 'like', $t)
                        ->orWhere('descripcion', 'like', $t)
                        ->orWhere('estado', 'like', $t);
                });
            })
            ->orderBy('id', 'desc')
            ->get();

        return response()->json($tareas);
    }

    // POST /tareas
    public function store(Request $request)
    {
        $user = $request->user();
        if ($user) {
            $request->merge(['user_id' => $user->id]);
        }
        $tarea = Tarea::create($request->all());
//        Tarea::create($request->all() + ['user_id' => $request->user()->id]);
        return response()->json($tarea->load('materia'), 201);
    }

    // GET /tareas/{tarea}
    public function show(Tarea $tarea)
    {
        return response()->json($tarea->load('materia'));
    }

    // PUT/PATCH /tareas/{tarea}
    public function update(Request $request, Tarea $tarea)
    {
        $tarea->update($request->all());
        return response()->json($tarea->load('materia'));
    }

    // DELETE /tareas/{tarea}
    public function destroy(Tarea $tarea)
    {
        $tarea->delete();
        return response()->json(['message' => 'Eliminado']);
    }
}
