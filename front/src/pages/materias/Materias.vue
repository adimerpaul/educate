<template>
  <q-page class="q-pa-md">

    <!-- Toolbar -->
    <div class="row items-center q-gutter-sm q-mb-md">
      <q-btn color="positive" no-caps icon="add_circle_outline" label="Nuevo" @click="nuevo" :loading="loading"/>
      <q-btn color="primary"  no-caps icon="refresh" label="Actualizar" @click="listar" :loading="loading"/>
      <q-space />
      <q-input v-model="filtro" dense outlined placeholder="Buscar... (nombre, docente, descripción)" @keyup.enter="listar" style="min-width:260px">
        <template #append><q-icon name="search" @click="listar" class="cursor-pointer"/></template>
      </q-input>
    </div>

    <!-- Tabla simple con QMarkupTable -->
    <q-markup-table flat bordered dense wrap-cells>
      <thead>
      <tr>
        <th class="text-left">Acciones</th>
        <th class="text-left">ID</th>
        <th class="text-left">Nombre</th>
        <th class="text-left">Docente</th>
        <th class="text-left">Descripción</th>
<!--        <th class="text-left">User ID</th>-->
      </tr>
      </thead>
      <tbody>
      <tr v-for="m in materias" :key="m.id">
        <td>
<!--          <q-btn size="sm" dense color="primary"  icon="edit"   flat round @click="editar(m)">-->
<!--            <q-tooltip>Editar</q-tooltip>-->
<!--          </q-btn>-->
<!--          <q-btn size="sm" dense color="negative" icon="delete" flat round @click="eliminar(m.id)">-->
<!--            <q-tooltip>Eliminar</q-tooltip>-->
<!--          </q-btn>-->
          <q-btn-dropdown label="Opciones" size="sm" dense color="primary" no-caps>
            <q-list>
              <q-item clickable v-ripple @click="editar(m)" v-close-popup>
                <q-item-section avatar>
                  <q-icon name="edit" />
                </q-item-section>
                <q-item-section>
                  <q-item-label>Editar</q-item-label>
                </q-item-section>
              </q-item>
              <q-item clickable v-ripple @click="eliminar(m.id)" v-close-popup>
                <q-item-section avatar>
                  <q-icon name="delete" />
                </q-item-section>
                <q-item-section>
                  <q-item-label>Eliminar</q-item-label>
                </q-item-section>
              </q-item>
            </q-list>
          </q-btn-dropdown>
        </td>
        <td>{{ m.id }}</td>
        <td :style="{ backgroundColor: m.color || 'transparent', color: m.color ? '#fff' : 'inherit' }">
          {{ m.nombre || '—' }}
<!--          <pre>{{m}}</pre>-->
<!--          {-->
<!--          "id": 1,-->
<!--          "nombre": "Actulizacion Tecnoligca",-->
<!--          "abreviatura": "SIS2420",-->
<!--          "docente": null,-->
<!--          "color": "#F44336",-->
<!--          "descripcion": null,-->
<!--          "user_id": 2-->
<!--          }-->
        </td>
        <td>{{ m.docente || '—' }}</td>
        <td class="ellipsis">{{ m.descripcion || '—' }}</td>
<!--        <td>{{ m.user_id ?? '—' }}</td>-->
      </tr>

      <tr v-if="!materias.length">
        <td colspan="6" class="text-center text-grey">Sin datos</td>
      </tr>
      </tbody>
    </q-markup-table>

    <!-- Dialogo Crear/Editar -->
    <q-dialog v-model="dialog" persistent @hide="onDialogHide">
      <q-card style="width: 520px; max-width: 92vw">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-subtitle1">{{ form.id ? 'Editar materia' : 'Nueva materia' }}</div>
          <q-space/>
          <q-btn icon="close" flat round dense v-close-popup />
        </q-card-section>

        <q-card-section class="q-pt-sm">
          <!-- sin validación -->
          <q-form @submit.prevent="guardar">
            <div class="row q-col-gutter-sm">
              <div class="col-12 col-sm-6">
                <q-input v-model="form.nombre" dense outlined label="Nombre"/>
              </div>
              <div class="col-12 col-sm-6">
                <q-input v-model="form.docente" dense outlined label="Docente"/>
              </div>
<!--              abreviatura-->
              <div class="col-12 col-sm-6">
                <q-input v-model="form.abreviatura" dense outlined label="Abreviatura"/>
              </div>
<!--              color-->
              <div class="col-12 col-sm-6">
                <q-input v-model="form.color" type="color" dense outlined label="Color"/>
<!--                <pre>{{form.color}}</pre>-->
              </div>
              <div class="col-12">
                <q-input v-model="form.descripcion" type="textarea" autogrow dense outlined label="Descripción"/>
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
  name: 'MateriasPage',
  data () {
    return {
      materias: [],
      filtro: '',
      loading: false,
      dialog: false,
      form: {
        id: null,
        nombre: '',
        docente: '',
        descripcion: '',
        // user_id lo asigna el backend en store()
      }
    }
  },
  mounted () { this.listar() },
  methods: {
    async listar () {
      this.loading = true
      try {
        const res = await this.$axios.get('materias', { params: { q: this.filtro || undefined } })
        this.materias = res.data
      } catch (e) {
        this.$alert?.error?.(e.response?.data?.message || 'Error al cargar materias')
      } finally {
        this.loading = false
      }
    },
    nuevo () {
      this.form = { id: null, nombre: '', docente: '', descripcion: '' }
      this.dialog = true
    },
    editar (row) {
      this.form = { ...row, docente: row.docente || '', descripcion: row.descripcion || '' }
      this.dialog = true
    },
    onDialogHide () {
      // limpiar formulario cuando se cierra
      this.form = { id: null, nombre: '', docente: '', descripcion: '' }
    },
    async guardar () {
      this.loading = true
      try {
        if (this.form.id) {
          await this.$axios.put(`materias/${this.form.id}`, this.form)
          this.$alert?.success?.('Materia actualizada')
        } else {
          await this.$axios.post('materias', this.form)
          this.$alert?.success?.('Materia creada')
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
            await this.$axios.delete(`materias/${id}`)
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
