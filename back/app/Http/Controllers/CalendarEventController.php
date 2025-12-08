<?php

namespace App\Http\Controllers;

use App\Models\Tarea;
use App\Models\StudyEvent;
use Illuminate\Http\Request;

class CalendarEventController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        error_log("CalendarEventController@index: user_id=" . $user->id);

        $request->validate([
            'start'         => ['nullable', 'date'],
            'end'           => ['nullable', 'date'],
            'only_priority' => ['nullable'],
            'materia_id'    => ['nullable', 'integer'],
            'estado'        => ['nullable', 'string'],
        ]);

        $start = $request->input('start');
        $end   = $request->input('end');
        error_log("CalendarEventController@index: start=$start, end=$end");

        // FullCalendar SIEMPRE manda start/end, pero ponemos default por si acaso
        if (!$start || !$end) {
            $start = now()->subMonth()->toDateString();
            $end   = now()->addMonths(2)->toDateString();
        }

        $onlyPriority = filter_var($request->input('only_priority'), FILTER_VALIDATE_BOOLEAN);
        $materiaId    = $request->integer('materia_id');
        $estado       = $request->input('estado');

        /*
         * 1) TAREAS → se muestran como eventos de calendario
         */
        $tareasQuery = Tarea::with('materia')
            ->where('user_id', $user->id);
        error_log("TareaController@index: start=$start, end=$end");
        error_log("tareas query before filters: " . $tareasQuery->toSql());
        error_log("tareas".json_encode($tareasQuery->getBindings()));

        if ($materiaId) {
            $tareasQuery->where('materia_id', $materiaId);
        }

        if ($estado) {
            $tareasQuery->where('estado', $estado);
        }

        if ($onlyPriority) {
            $tareasQuery->where('prioridad', 'ALTA');
        }

        $tareas = $tareasQuery->get();

        $events = [];

        foreach ($tareas as $t) {
            // Fecha base: primero fecha_planificada, si no, fecha_entrega
            $date = $t->fecha_planificada ?: $t->fecha_entrega;
            if (!$date) {
                continue;
            }

            // descartamos lo que está fuera del rango pedido por FullCalendar
            if ($date < $start || $date > $end) {
                continue;
            }

            $startDateTime = $date . 'T' . ($t->hora_inicio_plan ?: '08:00:00');
            $endDateTime   = $date . 'T' . ($t->hora_fin_plan   ?: '09:00:00');

            // Color base: el color de la materia; si no tiene, depende de prioridad
            $color = optional($t->materia)->color;
            if (!$color) {
                $color = strtoupper((string) $t->prioridad) === 'ALTA'
                    ? '#E91E63'  // alta prioridad
                    : '#2196F3'; // normal
            }

            $tituloMateria = optional($t->materia)->abreviatura
                ?: optional($t->materia)->nombre
                    ?: 'Tarea';

            $events[] = [
                'id'    => 'T-' . $t->id,
                'groupId' => 'tarea',
                'title' => trim($tituloMateria . ' · ' . ($t->tipo_tarea ?: 'Tarea')),
                'start' => $startDateTime,
                'end'   => $endDateTime,
                'allDay' => false,

                'backgroundColor' => $color,
                'borderColor'     => $color,
                'textColor'       => '#ffffff',

                'extendedProps' => [
                    'modelo'             => 'tarea',
                    'tarea_id'           => $t->id,
                    'tipo'               => $t->tipo_tarea,
                    'prioridad'          => $t->prioridad,
                    'estado'             => $t->estado,
                    'descripcion'        => $t->descripcion,
                    'materia'            => optional($t->materia)->nombre,
                    'materia_abreviatura'=> optional($t->materia)->abreviatura,
                    'tiempo_estimado'    => $t->tiempo_estimado,
                    'fecha_entrega'      => $t->fecha_entrega,
                    'fecha_planificada'  => $t->fecha_planificada,
                ],
            ];
        }

        /*
         * 2) StudyEvent → bloques de estudio planificados
         */
        $studyEvents = StudyEvent::with('materia')
            ->where('user_id', $user->id)
            ->whereBetween('fecha', [$start, $end])
            ->get();

        foreach ($studyEvents as $e) {
            $color = optional($e->materia)->color ?: '#4CAF50'; // verde estudio

            $events[] = [
                'id'    => 'E-' . $e->id,
                'groupId' => 'study',
                'title' => $e->titulo ?: ($e->tipo ?: 'Estudio'),
                'start' => $e->fecha . 'T' . $e->hora_inicio,
                'end'   => $e->fecha . 'T' . $e->hora_fin,
                'allDay' => false,

                'backgroundColor' => $color,
                'borderColor'     => $color,
                'textColor'       => '#ffffff',

                'extendedProps' => [
                    'modelo'             => 'study_event',
                    'study_event_id'     => $e->id,
                    'tipo'               => $e->tipo,
                    'descripcion'        => $e->descripcion,
                    'materia'            => optional($e->materia)->nombre,
                    'materia_abreviatura'=> optional($e->materia)->abreviatura,
                ],
            ];
        }

        return response()->json($events);
    }
}

