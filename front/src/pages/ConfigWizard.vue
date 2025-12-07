<template>
  <q-page class="q-pa-md bg-grey-2 flex flex-center">
    <q-card style="max-width: 900px; width: 100%;" flat bordered>
      <!-- HEADER -->
      <q-card-section class="text-center">
        <div class="text-h6 text-weight-bold">Configuración</div>
        <div class="text-caption text-grey-7">
          Tómate unos minutos para configurar tu cuenta correctamente.
        </div>

        <div class="row justify-center q-mt-md q-gutter-xl">
          <div class="column items-center">
            <q-btn round flat :color="step >= 1 ? 'primary' : 'grey-5'" icon="person" />
            <div class="text-caption q-mt-xs">PERSONAL</div>
          </div>
          <div class="column items-center">
            <q-btn round flat :color="step >= 2 ? 'primary' : 'grey-5'" icon="schedule" />
            <div class="text-caption q-mt-xs">TIEMPO ESTUDIO</div>
          </div>
          <div class="column items-center">
            <q-btn round flat :color="step >= 3 ? 'primary' : 'grey-5'" icon="menu_book" />
            <div class="text-caption q-mt-xs">ASIGNATURAS</div>
          </div>
        </div>
      </q-card-section>

      <q-separator />

      <!-- CONTENIDO -->
      <q-card-section>
        <!-- PASO 1 -->
        <div v-if="step === 1">
          <div class="text-subtitle1 text-weight-bold q-mb-md">Información personal</div>
          <div class="row q-col-gutter-md">

            <div class="col-12 col-md-4">
              <q-input v-model="profile.nombre" label="Nombre" dense outlined />
            </div>

            <div class="col-12 col-md-4">
              <q-input v-model="profile.apellidos" label="Apellidos" dense outlined />
            </div>

            <div class="col-12 col-md-4">
              <q-input v-model="profile.username" label="Usuario" dense outlined />
            </div>

            <div class="col-12 col-md-4">
              <q-input v-model="profile.fecha_nacimiento" type="date" label="Fecha nacimiento" dense outlined />
            </div>

            <div class="col-12 col-md-4">
              <div class="text-caption text-grey-7 q-mb-xs">Género</div>
              <div class="row q-gutter-sm">
                <q-btn no-caps outline class="col"
                       :color="profile.genero === 'MUJER' ? 'primary' : 'grey-6'"
                       label="Mujer" @click="profile.genero = 'MUJER'" />
                <q-btn no-caps outline class="col"
                       :color="profile.genero === 'VARON' ? 'primary' : 'grey-6'"
                       label="Varón" @click="profile.genero = 'VARON'" />
              </div>
            </div>

            <div class="col-12 col-md-4">
              <q-input v-model="profile.pais" label="País" dense outlined />
            </div>

            <div class="col-12 col-md-4">
              <q-input v-model="profile.estado_provincia" label="Departamento" dense outlined />
            </div>

            <div class="col-12 col-md-4">
              <q-input v-model="profile.tipo_centro" label="Ciudad" dense outlined />
            </div>

            <div class="col-12 col-md-4">
              <q-input v-model="profile.nombre_centro" label="Nombre Universidad" dense outlined />
            </div>

            <div class="col-12 col-md-4">
              <q-input v-model="profile.etapa" label="Gestión Año/Semestre" dense outlined />
            </div>

            <div class="col-12 col-md-4">
              <q-input v-model="profile.curso" label="Curso" dense outlined />
            </div>

<!--            <div class="col-12 col-md-4">-->
<!--              <q-input v-model="profile.clase_letra" label="Clase" dense outlined />-->
<!--            </div>-->

          </div>
        </div>

        <!-- PASO 2: TIEMPO -->
        <div v-else-if="step === 2">
          <div class="text-subtitle1 text-weight-bold q-mb-md">Tiempo de estudio</div>

          <q-tabs v-model="diaActivo" dense active-color="primary" indicator-color="primary" align="justify">
            <q-tab v-for="d in diasSemana" :key="d.value" :name="d.value" :label="d.label" />
          </q-tabs>

          <q-separator spaced />

          <!-- HABITUAL -->
          <div>
            <div class="text-caption text-grey-7 q-mb-xs">Tiempo habitual</div>

            <div v-for="(bloque, index) in horarios[diaActivo].habitual" :key="'h-'+index"
                 class="row items-center q-col-gutter-sm q-mb-xs">

              <div class="col-5 col-md-3">
                <q-input dense outlined v-model="bloque.hora_inicio" label="De" mask="##:##" />
              </div>

              <div class="col-5 col-md-3">
                <q-input dense outlined v-model="bloque.hora_fin" label="A" mask="##:##" />
              </div>

              <div class="col-auto">
                <q-btn dense flat round icon="remove" color="negative"
                       @click="removeBloque(diaActivo, 'habitual', index)" />
              </div>

            </div>

            <q-btn outline color="primary" icon="add" dense no-caps label="Añadir intervalo"
                   @click="addBloque(diaActivo, 'habitual')" />

            <q-separator spaced />

            <!-- EXTRA -->
            <div class="text-caption text-grey-7 q-mb-xs">Tiempo extra</div>

            <div v-for="(bloque, index) in horarios[diaActivo].extra" :key="'e-'+index"
                 class="row items-center q-col-gutter-sm q-mb-xs">

              <div class="col-5 col-md-3">
                <q-input dense outlined v-model="bloque.hora_inicio" label="De" mask="##:##" />
              </div>

              <div class="col-5 col-md-3">
                <q-input dense outlined v-model="bloque.hora_fin" label="A" mask="##:##" />
              </div>

              <div class="col-auto">
                <q-btn dense flat round icon="remove" color="negative"
                       @click="removeBloque(diaActivo, 'extra', index)" />
              </div>

            </div>

            <q-btn outline color="primary" icon="add" dense no-caps
                   label="Añadir intervalo extra"
                   @click="addBloque(diaActivo, 'extra')" />
          </div>
        </div>

        <!-- PASO 3 -->
        <div v-else>
          <div class="text-subtitle1 text-weight-bold q-mb-md">Asignaturas</div>

          <div class="row q-col-gutter-md items-end q-mb-md">
            <div class="col-12 col-md-4">
              <q-input v-model="materiaForm.nombre" label="Materia" dense outlined />
            </div>
            <div class="col-12 col-md-4">
              <q-input v-model="materiaForm.abreviatura" label="Abrev." dense outlined />
            </div>
            <div class="col-auto">
              <q-btn color="primary" no-caps label="Añadir" @click="agregarMateria" />
            </div>
          </div>

          <q-table
            :rows="materias"
            :columns="columnsMaterias"
            row-key="id"
            flat bordered dense hide-pagination
          >
            <template #body-cell-acciones="props">
              <q-td :props="props">
                <q-btn dense flat round icon="delete" color="negative"
                       @click="eliminarMateria(props.row.id)" />
              </q-td>
            </template>
          </q-table>

          <q-btn flat color="primary" class="q-mt-md" no-caps
                 label="Configurar descansos"
                 @click="dialogDescanso = true" />

          <q-dialog v-model="dialogDescanso" persistent>
            <q-card style="max-width: 400px;">
              <q-card-section>
                <div class="text-subtitle1 text-weight-bold">Importante</div>
                <div class="text-body2 q-mt-sm">
                  ¿Cada cuánto quieres descansar?
                </div>
                <div class="text-caption text-grey-7 q-mt-xs">
                  Recomendación: 10 minutos cada 50.
                </div>
              </q-card-section>

              <q-card-section>
                <q-input v-model.number="profile.minutos_descanso"
                         type="number" dense outlined
                         label="Descanso (minutos)" />
                <q-input v-model.number="profile.minutos_estudio_max"
                         type="number" dense outlined
                         label="Máximo estudio (minutos)" class="q-mt-sm" />
              </q-card-section>

              <q-card-actions align="right">
                <q-btn flat label="Cancelar" v-close-popup />
                <q-btn color="primary" label="Aceptar" v-close-popup />
              </q-card-actions>
            </q-card>
          </q-dialog>

        </div>
      </q-card-section>

      <q-separator />

      <!-- FOOTER -->
      <q-card-section class="row justify-between">
        <q-btn flat no-caps label="Paso anterior" :disable="step === 1" @click="step--" />
        <q-btn color="primary" no-caps
               :label="step === 3 ? 'Finalizar' : 'Siguiente paso'"
               :loading="saving"
               @click="onNext" />
      </q-card-section>

    </q-card>
  </q-page>
</template>


<script>
export default {
  name: 'ConfigWizard',

  data () {
    return {
      step: 1,
      saving: false,

      profile: {
        nombre: '',
        apellidos: '',
        username: '',
        fecha_nacimiento: '',
        genero: null,
        pais: '',
        estado_provincia: '',
        tipo_centro: '',
        nombre_centro: '',
        etapa: '',
        curso: '',
        clase_letra: '',
        minutos_descanso: 10,
        minutos_estudio_max: 50
      },

      diasSemana: [
        { value: '1', label: 'LUN' },
        { value: '2', label: 'MAR' },
        { value: '3', label: 'MIE' },
        { value: '4', label: 'JUE' },
        { value: '5', label: 'VIE' },
        { value: '6', label: 'SAB' },
        { value: '7', label: 'DOM' }
      ],

      diaActivo: '1',
      horarios: {},

      materias: [],
      materiaForm: {
        nombre: '',
        abreviatura: ''
      },

      columnsMaterias: [
        { name: 'nombre', label: 'Nombre Materia', field: 'nombre', align: 'left' },
        { name: 'abreviatura', label: 'Sigla.', field: 'abreviatura', align: 'left' },
        { name: 'acciones', label: '', align: 'right' }
      ],

      dialogDescanso: false
    }
  },

  created () {
    // Inicializar horarios sin $set (Vue 3 ya no lo usa)
    this.diasSemana.forEach(d => {
      this.horarios[d.value] = {
        habitual: [],
        extra: []
      }
    })

    this.cargarPerfil()
    this.cargarHorarios()
    this.cargarMaterias()
  },

  methods: {
    cargarPerfil () {
      this.$axios.get('/profile/me')
        .then(({ data }) => {
          this.profile = Object.assign(this.profile, data)
        })
        .catch(() => {})
    },

    guardarPerfil () {
      return this.$axios.post('/profile/save', this.profile)
    },

    cargarHorarios () {
      this.$axios.get('/study-schedules')
        .then(({ data }) => {
          data.forEach(item => {
            const key = String(item.dia_semana)
            const tipo = item.tipo === 'EXTRA' ? 'extra' : 'habitual'

            if (!this.horarios[key]) {
              this.horarios[key] = { habitual: [], extra: [] }
            }

            this.horarios[key][tipo].push({
              id: item.id,
              hora_inicio: item.hora_inicio.slice(0, 5),
              hora_fin: item.hora_fin.slice(0, 5)
            })
          })
        })
        .catch(() => {})
    },

    guardarHorarios () {
      const payload = []

      Object.keys(this.horarios).forEach(dia => {
        this.horarios[dia].habitual.forEach(b => {
          payload.push({
            id: b.id,
            dia_semana: parseInt(dia),
            tipo: 'HABITUAL',
            hora_inicio: b.hora_inicio + ':00',
            hora_fin: b.hora_fin + ':00'
          })
        })

        this.horarios[dia].extra.forEach(b => {
          payload.push({
            id: b.id,
            dia_semana: parseInt(dia),
            tipo: 'EXTRA',
            hora_inicio: b.hora_inicio + ':00',
            hora_fin: b.hora_fin + ':00'
          })
        })
      })

      return this.$axios.get('/study-schedules')
        .then(({ data }) => {
          const deletes = data.map(s => this.$axios.delete(`/study-schedules/${s.id}`))
          return Promise.all(deletes).then(() => {
            if (payload.length === 0) return
            return this.$axios.post('/study-schedules', payload)
          })
        })
    },

    addBloque (dia, tipo) {
      this.horarios[dia][tipo].push({
        hora_inicio: '00:00',
        hora_fin: '00:00'
      })
    },

    removeBloque (dia, tipo, index) {
      this.horarios[dia][tipo].splice(index, 1)
    },

    cargarMaterias () {
      this.$axios.get('/materias')
        .then(({ data }) => {
          this.materias = data
        })
        .catch(() => {})
    },

    agregarMateria () {
      if (!this.materiaForm.nombre) return

      this.$axios.post('/materias', this.materiaForm)
        .then(({ data }) => {
          this.materias.unshift(data)
          this.materiaForm.nombre = ''
          this.materiaForm.abreviatura = ''
        })
    },

    eliminarMateria (id) {
      this.$axios.delete(`/materias/${id}`)
        .then(() => {
          this.materias = this.materias.filter(m => m.id !== id)
        })
    },

    onNext () {
      this.saving = true
      let promise

      if (this.step === 1) {
        promise = this.guardarPerfil()
      } else if (this.step === 2) {
        promise = this.guardarHorarios()
      } else {
        promise = this.$axios.post('/onboarding/complete')
      }

      promise
        .then(() => {
          if (this.step < 3) {
            this.step++
          } else {
            this.$q.notify({ type: 'positive', message: 'Configuración completada' })
            this.$router.replace('/')
          }
        })
        .finally(() => {
          this.saving = false
        })
    }
  }
}
</script>
