<?php

namespace App\Http\Controllers;

use App\Models\StudySchedule;
use Illuminate\Http\Request;

class StudyScheduleController extends Controller
{
    // Lista todos los horarios del usuario logueado
    public function index(Request $request)
    {
        $user = $request->user();

        return StudySchedule::where('user_id', $user->id)
            ->orderBy('dia_semana')
            ->orderBy('tipo')
            ->orderBy('hora_inicio')
            ->get();
    }

    // Crea uno o varios horarios
    public function store(Request $request)
    {
        $user = $request->user();

        // Permitimos que envíes un solo objeto o un array de objetos
        $data = $request->all();

        if (isset($data[0]) && is_array($data[0])) {
            // array de horarios
            $created = [];
            foreach ($data as $item) {
                $item['user_id'] = $user->id;
                $created[] = StudySchedule::create($item);
            }
            return $created;
        }

        // un solo horario
        $data['user_id'] = $user->id;
        return StudySchedule::create($data);
    }

    // Actualizar un horario
    public function update(Request $request, $id)
    {
        $user = $request->user();
        $schedule = StudySchedule::where('user_id', $user->id)->findOrFail($id);
        $schedule->update($request->all());

        return $schedule;
    }

    // Eliminar un horario
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $schedule = StudySchedule::where('user_id', $user->id)->findOrFail($id);
        $schedule->delete();

        return response()->json(['message' => 'Horario eliminado']);
    }
}
