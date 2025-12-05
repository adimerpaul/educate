<template>
  <q-page class="q-pa-md bg-grey-2">

    <!-- CARD PRINCIPAL -->
    <q-card flat bordered class="q-pa-md">

      <!-- HEADER / TOOLBAR -->
      <div class="row items-center q-gutter-sm q-mb-md">
        <div class="col-auto">
          <div class="text-h6 text-weight-bold">Tareas y planificación</div>
          <div class="text-caption text-grey-7">
            Gestiona tus tareas, tiempos estimados, descansos y planificación de estudio.
          </div>
        </div>

        <q-space />

        <q-select
          v-model="filtroMateriaId"
          :options="materias"
          emit-value
          map-options
          option-value="id"
          option-label="label"
          dense
          outlined
          clearable
          style="min-width: 220px"
          placeholder="Filtrar por materia"
          @update:model-value="listar"
        />

        <q-select
          v-model="filtroEstado"
          :options="estadoOptions"
          dense
          outlined
          clearable
          style="min-width: 180px"
          placeholder="Estado"
          @update:model-value="listar"
        />

        <q-input
          v-model="filtro"
          dense
          outlined
          placeholder="Buscar... (tipo, descripción, estado)"
          style="min-width: 260px"
          @keyup.enter="listar"
        >
          <template #append>
            <q-icon name="search" class="cursor-pointer" @click="listar" />
          </template>
        </q-input>

        <q-btn
          color="primary"
          no-caps
          icon="refresh"
          label="Actualizar"
          :loading="loading"
          @click="listar"
        />
        <q-btn
          color="positive"
          no-caps
          icon="add_circle_outline"
          label="Nueva tarea"
          :loading="loading"
          @click="nuevo"
        />
      </div>

      <!-- TABS VISTA GENERAL / PRIORITARIA -->
      <q-tabs
        v-model="vista"
        dense
        active-color="primary"
        indicator-color="primary"
        align="left"
        class="q-mb-sm"
      >
        <q-tab name="general" label="Listado general" icon="list_alt" />
        <q-tab name="prioritaria" label="Tareas prioritarias" icon="priority_high" />
      </q-tabs>

      <!-- TABLA DE TAREAS -->
      <q-markup-table flat bordered dense wrap-cells class="bg-white">
        <thead>
        <tr>
          <th class="text-left">Acciones</th>
          <th class="text-left">ID</th>
          <th class="text-left">Materia</th>
          <th class="text-left">Tipo</th>
          <th class="text-left">Prioridad</th>
          <th class="text-left">Descripción</th>
          <th class="text-left">Tiempo estimado</th>
          <th class="text-left">Planificación</th>
          <th class="text-left">F. creación</th>
          <th class="text-left">F. entrega</th>
          <th class="text-left">Estado</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="t in tareasFiltradas" :key="t.id">
          <td>
            <q-btn
              size="sm"
              dense
              color="primary"
              icon="edit"
              flat
              round
              @click="editar(t)"
            >
              <q-tooltip>Editar</q-tooltip>
            </q-btn>
            <q-btn
              size="sm"
              dense
              color="negative"
              icon="delete"
              flat
              round
              @click="eliminar(t.id)"
            >
              <q-tooltip>Eliminar</q-tooltip>
            </q-btn>
          </td>
          <td>{{ t.id }}</td>
          <td>{{ t.materia?.nombre || '—' }}</td>
          <td>{{ t.tipo_tarea || '—' }}</td>
          <td>
            <q-chip
              dense
              :label="t.prioridad || 'Normal'"
              :color="colorPrioridad(t.prioridad)"
              text-color="white"
            />
          </td>
          <td class="ellipsis">
            {{ t.descripcion || '—' }}
          </td>
          <td>
            <div v-if="t.tiempo_estimado">
              {{ t.tiempo_estimado }} min
              <div class="text-caption text-grey-7">
                {{ t.descanso_duracion || 10 }} min descanso c/ {{ t.descanso_cada || 50 }}
              </div>
            </div>
            <span v-else>—</span>
          </td>
          <td>
            <div v-if="t.fecha_planificada">
              {{ t.fecha_planificada }}
              <div v-if="t.hora_inicio_plan && t.hora_fin_plan" class="text-caption text-grey-7">
                {{ t.hora_inicio_plan }} - {{ t.hora_fin_plan }}
              </div>
            </div>
            <span v-else>—</span>
          </td>
          <td>{{ t.fecha_creacion || '—' }}</td>
          <td>{{ t.fecha_entrega || '—' }}</td>
          <td>
            <q-chip
              dense
              :label="t.estado || '—'"
              :color="colorEstado(t.estado)"
              text-color="white"
            />
          </td>
        </tr>

        <tr v-if="!tareasFiltradas.length">
          <td colspan="11" class="text-center text-grey">
            No hay tareas para los filtros seleccionados.
          </td>
        </tr>
        </tbody>
      </q-markup-table>
    </q-card>

    <!-- DIÁLOGO CREAR / EDITAR TAREA -->
    <q-dialog v-model="dialog" persistent @hide="onDialogHide">
      <q-card style="width: 620px; max-width: 95vw">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-subtitle1 text-weight-bold">
            {{ form.id ? 'Editar tarea' : 'Nueva tarea' }}
          </div>
          <q-space />
          <q-btn icon="close" flat round dense v-close-popup />
        </q-card-section>

        <q-card-section class="q-pt-sm">
          <!-- Sin validación compleja, solo bindings -->
          <q-form @submit.prevent="guardar">
            <div class="row q-col-gutter-sm">

              <!-- COLUMNA IZQUIERDA -->
              <div class="col-12 col-md-6">
                <q-select
                  v-model="form.materia_id"
                  :options="materias"
                  emit-value
                  map-options
                  option-value="id"
                  option-label="nombre"
                  dense
                  outlined
                  label="Materia"
                  clearable
                />

                <q-select
                  v-model="form.tipo_tarea"
                  :options="tipoTareaOptions"
                  dense
                  outlined
                  label="Tipo de tarea"
                  class="q-mt-sm"
                  clearable
                />

                <q-select
                  v-model="form.prioridad"
                  :options="prioridadOptions"
                  dense
                  outlined
                  label="Prioridad"
                  class="q-mt-sm"
                />

                <q-input
                  v-model.number="form.tiempo_estimado"
                  type="number"
                  min="0"
                  dense
                  outlined
                  label="Tiempo estimado (minutos)"
                  class="q-mt-sm"
                />

                <div class="row q-col-gutter-xs q-mt-xs">
                  <div class="col-6">
                    <q-input
                      v-model.number="form.descanso_duracion"
                      type="number"
                      min="0"
                      dense
                      outlined
                      label="Descanso (min)"
                    />
                  </div>
                  <div class="col-6">
                    <q-input
                      v-model.number="form.descanso_cada"
                      type="number"
                      min="0"
                      dense
                      outlined
                      label="Cada (min de estudio)"
                    />
                  </div>
                </div>

                <div class="text-caption text-grey-7 q-mt-xs">
                  Por defecto 10 min de descanso cada 50 min de estudio.
                </div>
              </div>

              <!-- COLUMNA DERECHA -->
              <div class="col-12 col-md-6">
                <q-input
                  v-model="form.descripcion"
                  type="textarea"
                  autogrow
                  dense
                  outlined
                  label="Descripción"
                />

                <div class="row q-col-gutter-xs q-mt-sm">
                  <div class="col-6">
                    <q-input
                      v-model="form.fecha_creacion"
                      type="date"
                      dense
                      outlined
                      label="Fecha de creación"
                    />
                  </div>
                  <div class="col-6">
                    <q-input
                      v-model="form.fecha_entrega"
                      type="date"
                      dense
                      outlined
                      label="Fecha de entrega"
                    />
                  </div>
                </div>

                <div class="q-mt-sm text-subtitle2 text-weight-medium">
                  Planificación de estudio
                </div>

                <q-input
                  v-model="form.fecha_planificada"
                  type="date"
                  dense
                  outlined
                  label="Fecha planificada"
                  class="q-mt-xs"
                />

                <div class="row q-col-gutter-xs q-mt-xs">
                  <div class="col-6">
                    <q-input
                      v-model="form.hora_inicio_plan"
                      dense
                      outlined
                      label="Hora inicio"
                      mask="##:##"
                      hint="HH:MM"
                    />
                  </div>
                  <div class="col-6">
                    <q-input
                      v-model="form.hora_fin_plan"
                      dense
                      outlined
                      label="Hora fin"
                      mask="##:##"
                      hint="HH:MM"
                    />
                  </div>
                </div>

                <q-select
                  v-model="form.estado"
                  :options="estadoOptions"
                  dense
                  outlined
                  label="Estado"
                  class="q-mt-sm"
                />
              </div>
            </div>
          </q-form>
        </q-card-section>

        <q-card-actions align="right">
          <q-btn
            flat
            color="negative"
            no-caps
            label="Cancelar"
            :disable="loading"
            @click="dialog = false"
          />
          <q-btn
            color="primary"
            no-caps
            :label="form.id ? 'Actualizar' : 'Guardar'"
            :loading="loading"
            @click="guardar"
          />
        </q-card-actions>
      </q-card>
    </q-dialog>

  </q-page>
</template>

<script>
export default {
  name: 'TareasPage',
  data () {
    return {
      tareas: [],
      materias: [],
      filtro: '',
      filtroMateriaId: null,
      filtroEstado: null,
      vista: 'general', // general / prioritaria

      loading: false,
      dialog: false,

      form: {
        id: null,
        materia_id: null,
        tipo_tarea: '',
        descripcion: '',
        fecha_entrega: '',
        fecha_creacion: '',
        estado: '',
        tiempo_estimado: null,
        prioridad: 'Normal',
        fecha_planificada: '',
        hora_inicio_plan: '',
        hora_fin_plan: '',
        descanso_duracion: 10,
        descanso_cada: 50
        // user_id lo pone el backend
      },

      tipoTareaOptions: ['Normal', 'Estudio', 'Examen'],
      prioridadOptions: ['Normal', 'Alta'],
      estadoOptions: ['PENDIENTE', 'ENTREGADA', 'ATRASADA']
    }
  },

  computed: {
    materiaOptions () {
      return (this.materias || []).map(m => ({ id: m.id, label: m.nombre }))
    },
    tareasFiltradas () {
      const base = this.tareas || []
      if (this.vista === 'prioritaria') {
        return base.filter(t => (t.prioridad || '').toUpperCase() === 'ALTA')
      }
      return base
    }
  },

  mounted () {
    this.cargarMaterias()
    this.listar()
  },

  methods: {
    colorEstado (estado) {
      switch ((estado || '').toUpperCase()) {
        case 'ENTREGADA': return 'positive'
        case 'ATRASADA': return 'negative'
        case 'PENDIENTE': return 'warning'
        default: return 'grey-7'
      }
    },
    colorPrioridad (prioridad) {
      const p = (prioridad || 'NORMAL').toUpperCase()
      if (p === 'ALTA') return 'deep-orange'
      return 'grey-7'
    },

    async cargarMaterias () {
      try {
        const res = await this.$axios.get('materias')
        // para filtro amigable
        this.materias = (res.data || []).map(m => ({
          ...m,
          label: m.nombre
        }))
      } catch (e) {
        this.$alert?.error?.(e.response?.data?.message || 'Error cargando materias')
      }
    },

    async listar () {
      this.loading = true
      try {
        const params = {
          q: this.filtro || undefined,
          materia_id: this.filtroMateriaId || undefined,
          estado: this.filtroEstado || undefined
        }
        const res = await this.$axios.get('tareas', { params })
        this.tareas = res.data
      } catch (e) {
        this.$alert?.error?.(e.response?.data?.message || 'Error al cargar tareas')
      } finally {
        this.loading = false
      }
    },

    nuevo () {
      this.form = {
        id: null,
        materia_id: this.filtroMateriaId || null,
        tipo_tarea: '',
        descripcion: '',
        fecha_entrega: '',
        fecha_creacion: new Date().toISOString().slice(0, 10),
        estado: this.filtroEstado || 'PENDIENTE',
        tiempo_estimado: null,
        prioridad: 'Normal',
        fecha_planificada: '',
        hora_inicio_plan: '',
        hora_fin_plan: '',
        descanso_duracion: 10,
        descanso_cada: 50
      }
      this.dialog = true
    },

    editar (row) {
      this.form = {
        id: row.id,
        materia_id: row.materia_id ?? row.materia?.id ?? null,
        tipo_tarea: row.tipo_tarea || '',
        descripcion: row.descripcion || '',
        fecha_entrega: row.fecha_entrega || '',
        fecha_creacion: row.fecha_creacion || '',
        estado: row.estado || 'PENDIENTE',
        tiempo_estimado: row.tiempo_estimado ?? null,
        prioridad: row.prioridad || 'Normal',
        fecha_planificada: row.fecha_planificada || '',
        hora_inicio_plan: row.hora_inicio_plan || '',
        hora_fin_plan: row.hora_fin_plan || '',
        descanso_duracion: row.descanso_duracion ?? 10,
        descanso_cada: row.descanso_cada ?? 50
      }
      this.dialog = true
    },

    onDialogHide () {
      this.form = {
        id: null,
        materia_id: null,
        tipo_tarea: '',
        descripcion: '',
        fecha_entrega: '',
        fecha_creacion: '',
        estado: '',
        tiempo_estimado: null,
        prioridad: 'Normal',
        fecha_planificada: '',
        hora_inicio_plan: '',
        hora_fin_plan: '',
        descanso_duracion: 10,
        descanso_cada: 50
      }
    },

    async guardar () {
      this.loading = true
      try {
        if (this.form.id) {
          await this.$axios.put(`tareas/${this.form.id}`, this.form)
          this.$alert?.success?.('Tarea actualizada correctamente')
        } else {
          await this.$axios.post('tareas', this.form)
          this.$alert?.success?.('Tarea creada correctamente')
        }
        this.dialog = false
        this.listar()
      } catch (e) {
        this.$alert?.error?.(e.response?.data?.message || 'No se pudo guardar la tarea')
      } finally {
        this.loading = false
      }
    },

    eliminar (id) {
      this.$alert?.dialog?.('¿Eliminar esta tarea?')
        ?.onOk(async () => {
          this.loading = true
          try {
            await this.$axios.delete(`tareas/${id}`)
            this.$alert?.success?.('Tarea eliminada')
            this.listar()
          } catch (e) {
            this.$alert?.error?.(e.response?.data?.message || 'No se pudo eliminar')
          } finally {
            this.loading = false
          }
        })
    }
  }
}
</script>
