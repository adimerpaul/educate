<template>
  <q-page class="q-pa-md">

    <!-- Toolbar -->
    <div class="row items-center q-gutter-sm q-mb-md">
      <q-btn color="positive" no-caps icon="add_circle_outline" label="Nuevo" @click="nuevo" :loading="loading"/>
      <q-btn color="primary"  no-caps icon="refresh" label="Actualizar" @click="listar" :loading="loading"/>
      <q-space />

      <q-select
        v-model="filtroMateriaId" :options="materias" emit-value map-options
        dense outlined clearable style="min-width: 220px" option-value="id" option-label="label"
        placeholder="Filtrar por materia" @update:model-value="listar"
      />
      <q-select
        v-model="filtroEstado" :options="estadoOptions" dense outlined clearable style="min-width:180px"
        placeholder="Estado" @update:model-value="listar"
      />
      <q-input v-model="filtro" dense outlined placeholder="Buscar... (tipo, desc, estado)"
               @keyup.enter="listar" style="min-width:260px">
        <template #append><q-icon name="search" @click="listar" class="cursor-pointer"/></template>
      </q-input>
    </div>

    <!-- Tabla -->
    <q-markup-table flat bordered dense wrap-cells>
      <thead>
      <tr>
        <th class="text-left">Acciones</th>
        <th class="text-left">ID</th>
        <th class="text-left">Materia</th>
        <th class="text-left">Tipo</th>
        <th class="text-left">Descripción</th>
        <th class="text-left">F. creación</th>
        <th class="text-left">F. entrega</th>
        <th class="text-left">Estado</th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="t in tareas" :key="t.id">
        <td>
          <q-btn size="sm" dense color="primary" icon="edit" flat round @click="editar(t)">
            <q-tooltip>Editar</q-tooltip>
          </q-btn>
          <q-btn size="sm" dense color="negative" icon="delete" flat round @click="eliminar(t.id)">
            <q-tooltip>Eliminar</q-tooltip>
          </q-btn>
        </td>
        <td>{{ t.id }}</td>
        <td>{{ t.materia?.nombre || '—' }}</td>
        <td>{{ t.tipo_tarea || '—' }}</td>
        <td class="ellipsis">{{ t.descripcion || '—' }}</td>
        <td>{{ t.fecha_creacion || '—' }}</td>
        <td>{{ t.fecha_entrega || '—' }}</td>
        <td>
          <q-chip dense :label="t.estado || '—'" :color="colorEstado(t.estado)" text-color="white" />
        </td>
      </tr>
      <tr v-if="!tareas.length">
        <td colspan="8" class="text-center text-grey">Sin datos</td>
      </tr>
      </tbody>
    </q-markup-table>

    <!-- Diálogo Crear/Editar -->
    <q-dialog v-model="dialog" persistent @hide="onDialogHide">
      <q-card style="width: 560px; max-width: 92vw">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-subtitle1">{{ form.id ? 'Editar tarea' : 'Nueva tarea' }}</div>
          <q-space/><q-btn icon="close" flat round dense v-close-popup />
        </q-card-section>

        <q-card-section class="q-pt-sm">
          <!-- Sin validación -->
          <q-form @submit.prevent="guardar">
            <div class="row q-col-gutter-sm">
              <div class="col-12 col-sm-6">
                <q-select
                  v-model="form.materia_id"
                  :options="materias" emit-value map-options
                  option-value="id" option-label="nombre"
                  dense outlined label="Materia"
                  clearable
                />
<!--                <pre>{{form.materia_id}}</pre>-->
              </div>
              <div class="col-12 col-sm-6">
                <q-input v-model="form.tipo_tarea" dense outlined label="Tipo de tarea" />
              </div>
              <div class="col-12">
                <q-input v-model="form.descripcion" type="textarea" autogrow dense outlined label="Descripción" />
              </div>
              <div class="col-12 col-sm-6">
                <q-input v-model="form.fecha_creacion" type="date" dense outlined label="Fecha de creación" />
              </div>
              <div class="col-12 col-sm-6">
                <q-input v-model="form.fecha_entrega" type="date" dense outlined label="Fecha de entrega" />
              </div>
              <div class="col-12 col-sm-6">
                <q-select v-model="form.estado" :options="estadoOptions" dense outlined label="Estado" />
              </div>
            </div>
          </q-form>
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat color="negative" no-caps label="Cancelar" @click="dialog=false" :disable="loading"/>
          <q-btn color="primary" no-caps :label="form.id ? 'Actualizar' : 'Guardar'" @click="guardar" :loading="loading"/>
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
        // user_id lo pone backend en store()
      },
      estadoOptions: ['PENDIENTE', 'ENTREGADA', 'ATRASADA']
    }
  },
  computed: {
    materiaOptions () {
      return (this.materias || []).map(m => ({ id: m.id, label: m.nombre }))
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
        case 'ATRASADA':  return 'negative'
        case 'PENDIENTE': return 'warning'
        default:          return 'grey-7'
      }
    },
    async cargarMaterias () {
      try {
        const res = await this.$axios.get('materias') // para el select
        this.materias = res.data
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
        fecha_creacion: '',
        estado: this.filtroEstado || ''
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
        estado: row.estado || ''
      }
      this.dialog = true
    },
    onDialogHide () {
      this.form = {
        id: null, materia_id: null, tipo_tarea: '', descripcion: '',
        fecha_entrega: '', fecha_creacion: '', estado: ''
      }
    },
    async guardar () {
      this.loading = true
      try {
        if (this.form.id) {
          await this.$axios.put(`tareas/${this.form.id}`, this.form)
          this.$alert?.success?.('Tarea actualizada')
        } else {
          await this.$axios.post('tareas', this.form)
          this.$alert?.success?.('Tarea creada')
        }
        this.dialog = false
        this.listar()
      } catch (e) {
        this.$alert?.error?.(e.response?.data?.message || 'No se pudo guardar')
      } finally {
        this.loading = false
      }
    },
    eliminar (id) {
      this.$alert?.dialog?.('¿Eliminar registro?')
        ?.onOk(async () => {
          this.loading = true
          try {
            await this.$axios.delete(`tareas/${id}`)
            this.$alert?.success?.('Eliminado')
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
