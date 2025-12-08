<template>
  <q-page class="q-pa-md bg-grey-2">
    <q-card flat bordered class="q-pa-md">

      <!-- HEADER / TOOLBAR -->
      <div class="row items-center q-gutter-sm q-mb-md">
        <div class="col-auto">
          <div class="text-h6 text-weight-bold">Calendario de estudio</div>
          <div class="text-caption text-grey-7">
            Visualiza tus tareas, eventos de estudio y prioridades en un solo calendario.
          </div>
        </div>

        <q-space />

        <q-select
          v-model="filtroMateriaId"
          :options="materiaOptions"
          emit-value
          map-options
          dense
          outlined
          clearable
          style="min-width: 220px"
          placeholder="Materia"
          @update:model-value="refetchEvents"
        />

        <q-select
          v-model="filtroEstado"
          :options="estadoOptions"
          dense
          outlined
          clearable
          style="min-width: 180px"
          placeholder="Estado tarea"
          @update:model-value="refetchEvents"
        />

        <q-toggle
          v-model="soloPrioritarias"
          color="deep-orange"
          keep-color
          label="Solo prioritarias"
          @update:model-value="refetchEvents"
        />

        <q-btn-group flat rounded>
          <q-btn
            dense
            :flat="currentView !== 'dayGridMonth'"
            :color="currentView === 'dayGridMonth' ? 'primary' : 'grey-7'"
            icon="calendar_month"
            @click="changeView('dayGridMonth')"
          >
            <q-tooltip>Mes</q-tooltip>
          </q-btn>
          <q-btn
            dense
            :flat="currentView !== 'timeGridWeek'"
            :color="currentView === 'timeGridWeek' ? 'primary' : 'grey-7'"
            icon="view_week"
            @click="changeView('timeGridWeek')"
          >
            <q-tooltip>Semana</q-tooltip>
          </q-btn>
          <q-btn
            dense
            :flat="currentView !== 'listWeek'"
            :color="currentView === 'listWeek' ? 'primary' : 'grey-7'"
            icon="list_alt"
            @click="changeView('listWeek')"
          >
            <q-tooltip>Lista semanal</q-tooltip>
          </q-btn>
        </q-btn-group>
      </div>

      <!-- CALENDARIO -->
      <div class="calendar-wrapper">
        <FullCalendar
          ref="fullCalendar"
          :options="calendarOptions"
        >
          <!-- Plantilla para contenido de evento (slot) -->
          <template #eventContent="arg">
            <div class="row no-wrap items-center">
              <div class="dot q-mr-xs"></div>
              <div class="text-caption">
                <strong>{{ arg.event.title }}</strong>
              </div>
            </div>
          </template>
        </FullCalendar>
      </div>
    </q-card>

    <!-- DIÁLOGO DETALLE EVENTO -->
    <q-dialog v-model="dialogEvento">
      <q-card style="max-width: 420px; width: 90vw">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-subtitle1 text-weight-bold">
            {{ eventoSeleccionado?.title || 'Detalle' }}
          </div>
          <q-space />
          <q-btn icon="close" flat round dense v-close-popup />
        </q-card-section>

        <q-card-section class="q-pt-sm">
          <div v-if="eventoSeleccionado">
            <div class="text-caption text-grey-7 q-mb-xs">
              {{ rangoFechasEvento }}
            </div>

            <q-chip
              dense
              outline
              color="primary"
              text-color="primary"
              v-if="eventoSeleccionado.extendedProps.materia"
              class="q-mb-xs"
            >
              {{ eventoSeleccionado.extendedProps.materia }}
            </q-chip>

            <div class="row q-col-gutter-xs q-mb-xs">
              <div class="col-6" v-if="eventoSeleccionado.extendedProps.tipo">
                <div class="text-caption text-grey-7">Tipo</div>
                <div class="text-body2">{{ eventoSeleccionado.extendedProps.tipo }}</div>
              </div>

              <div class="col-6" v-if="eventoSeleccionado.extendedProps.prioridad">
                <div class="text-caption text-grey-7">Prioridad</div>
                <q-chip
                  dense
                  :color="colorPrioridad(eventoSeleccionado.extendedProps.prioridad)"
                  text-color="white"
                >
                  {{ eventoSeleccionado.extendedProps.prioridad }}
                </q-chip>
              </div>
            </div>

            <div class="q-mb-sm" v-if="eventoSeleccionado.extendedProps.estado">
              <div class="text-caption text-grey-7">Estado</div>
              <q-chip
                dense
                :color="colorEstado(eventoSeleccionado.extendedProps.estado)"
                text-color="white"
              >
                {{ eventoSeleccionado.extendedProps.estado }}
              </q-chip>
            </div>

            <div v-if="eventoSeleccionado.extendedProps.descripcion">
              <div class="text-caption text-grey-7 q-mb-xs">Descripción</div>
              <div class="text-body2">
                {{ eventoSeleccionado.extendedProps.descripcion }}
              </div>
            </div>

            <div class="q-mt-sm" v-if="eventoSeleccionado.extendedProps.fecha_entrega">
              <div class="text-caption text-grey-7">Fecha entrega</div>
              <div class="text-body2">
                {{ eventoSeleccionado.extendedProps.fecha_entrega }}
              </div>
            </div>

          </div>
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Cerrar" v-close-popup />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
// import '@fullcalendar/core/main.css'
// import '@fullcalendar/daygrid/main.css'
// import '@fullcalendar/timegrid/main.css'
// import '@fullcalendar/list/main.css'
// import FullCalendar from '@fullcalendar/vue3'
// import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import listPlugin from '@fullcalendar/list'
// import interactionPlugin from '@fullcalendar/interaction'
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import interactionPlugin from '@fullcalendar/interaction'
export default {
  name: 'CalendarPage',

  components: {
    FullCalendar
  },

  data () {
    return {
      materias: [],
      filtroMateriaId: null,
      filtroEstado: null,
      soloPrioritarias: false,

      estadoOptions: ['PENDIENTE', 'ENTREGADA', 'ATRASADA'],

      currentView: 'dayGridMonth',

      dialogEvento: false,
      eventoSeleccionado: null,

      calendarOptions: {
        plugins: [dayGridPlugin, timeGridPlugin, listPlugin, interactionPlugin],
        initialView: 'dayGridMonth',
        locale: 'es',
        height: 'auto',
        firstDay: 1, // lunes

        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: '' // las vistas las controlamos desde los botones de Quasar
        },

        // Cargamos eventos desde el backend
        events: null, // se asigna en created()

        // Interacciones
        dateClick: null, // si quieres hacer algo al clickear un día
        eventClick: null
      }
    }
  },

  computed: {
    materiaOptions () {
      return (this.materias || []).map(m => ({
        label: m.nombre,
        value: m.id
      }))
    },

    rangoFechasEvento () {
      if (!this.eventoSeleccionado) return ''
      const start = this.eventoSeleccionado.start
      const end = this.eventoSeleccionado.end

      if (!start) return ''

      const s = start.toLocaleString()
      if (!end) return s

      const e = end.toLocaleString()
      return `${s} – ${e}`
    }
  },

  created () {
    // asignamos las funciones aquí para tener this disponible
    this.calendarOptions.events = this.fetchEvents
    this.calendarOptions.eventClick = this.handleEventClick
    // opcional: this.calendarOptions.dateClick = this.handleDateClick

    this.cargarMaterias()
  },

  methods: {
    async cargarMaterias () {
      try {
        const { data } = await this.$axios.get('/materias')
        this.materias = data || []
      } catch (_) {
        // silencioso
      }
    },

    async fetchEvents (fetchInfo, successCallback, failureCallback) {
      try {
        const params = {
          start: fetchInfo.startStr,
          end: fetchInfo.endStr,
          materia_id: this.filtroMateriaId || undefined,
          estado: this.filtroEstado || undefined,
          only_priority: this.soloPrioritarias ? 1 : undefined
        }

        const { data } = await this.$axios.get('/calendar/events', { params })
        successCallback(data)
      } catch (e) {
        failureCallback(e)
        this.$alert?.error?.('No se pudieron cargar los eventos')
      }
    },

    refetchEvents () {
      const api = this.$refs.fullCalendar?.getApi?.()
      if (api) {
        api.refetchEvents()
      }
    },

    changeView (viewName) {
      this.currentView = viewName
      const api = this.$refs.fullCalendar?.getApi?.()
      if (api) {
        api.changeView(viewName)
      }
    },

    handleEventClick (arg) {
      this.eventoSeleccionado = arg.event
      this.dialogEvento = true
    },

    colorEstado (estado) {
      switch ((estado || '').toUpperCase()) {
        case 'ENTREGADA': return 'positive'
        case 'ATRASADA':  return 'negative'
        case 'PENDIENTE': return 'warning'
        default:          return 'grey-7'
      }
    },

    colorPrioridad (prioridad) {
      const p = (prioridad || 'NORMAL').toUpperCase()
      if (p === 'ALTA') return 'deep-orange'
      return 'grey-7'
    }
  }
}
</script>

<style scoped>
.calendar-wrapper {
  background: white;
  border-radius: 12px;
  padding: 8px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.05);
}

/* Puntito de color en el slot del evento */
.dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: currentColor;
}
</style>
