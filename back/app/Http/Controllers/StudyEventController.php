<?php

namespace App\Http\Controllers;

use App\Models\StudyEvent;
use Illuminate\Http\Request;

class StudyEventController extends Controller
{
    // Lista eventos del usuario autenticado (opcionalmente filtrados)
    public function index(Request $request)
    {
        $query = StudyEvent::with(['materia'])
            ->where('user_id', $request->user()->id);

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }
        if ($request->filled('fecha')) {
            $query->whereDate('fecha', $request->fecha);
        }

        return $query->orderBy('fecha')->orderBy('hora_inicio')->get();
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $data['user_id'] = $request->user()->id;

        $event = StudyEvent::create($data);
        return response()->json($event, 201);
    }

    public function show($id)
    {
        return StudyEvent::with('materia')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $event = StudyEvent::where('user_id', $request->user()->id)->findOrFail($id);
        $event->update($request->all());
        return $event;
    }

    public function destroy(Request $request, $id)
    {
        $event = StudyEvent::where('user_id', $request->user()->id)->findOrFail($id);
        $event->delete();
        return response()->json(['message' => 'Eliminado']);
    }
}
