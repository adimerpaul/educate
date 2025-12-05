<template>
  <q-page class="q-pa-md bg-grey-2 flex flex-center">
    <q-card style="max-width: 980px; width: 100%;" flat bordered>

      <!-- HEADER -->
      <q-card-section class="row items-center">
        <div class="col">
          <div class="text-h6 text-weight-bold">Mi perfil y configuración de estudio</div>
          <div class="text-caption text-grey-7">
            Actualiza tus datos personales, organiza tu tiempo de estudio y gestiona tus asignaturas.
          </div>
        </div>
      </q-card-section>

      <q-separator />

      <!-- TABS -->
      <q-card-section class="q-pt-none">
        <q-tabs
          v-model="tab"
          dense
          active-color="primary"
          indicator-color="primary"
          align="left"
          class="q-mb-md"
        >
          <q-tab name="personal" icon="person" label="Datos personales" />
          <q-tab name="tiempo" icon="schedule" label="Tiempo de estudio" />
          <q-tab name="materias" icon="menu_book" label="Asignaturas" />
        </q-tabs>

        <q-separator />

        <q-tab-panels v-model="tab" animated>

          <!-- TAB 1: DATOS PERSONALES -->
          <q-tab-panel name="personal">
            <div class="text-subtitle1 text-weight-bold q-mb-md">Datos personales</div>

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
                <q-input
                  v-model="profile.fecha_nacimiento"
                  type="date"
                  label="Fecha de nacimiento"
                  dense
                  outlined
                />
              </div>

              <div class="col-12 col-md-4">
                <div class="text-caption text-grey-7 q-mb-xs">Género</div>
                <div class="row q-gutter-sm">
                  <q-btn
                    no-caps
                    outline
                    class="col"
                    :color="profile.genero === 'MUJER' ? 'primary' : 'grey-6'"
                    label="Mujer"
                    @click="profile.genero = 'MUJER'"
                  />
                  <q-btn
                    no-caps
                    outline
                    class="col"
                    :color="profile.genero === 'VARON' ? 'primary' : 'grey-6'"
                    label="Varón"
                    @click="profile.genero = 'VARON'"
                  />
                </div>
              </div>

              <div class="col-12 col-md-4">
                <q-input v-model="profile.pais" label="País" dense outlined />
              </div>

              <div class="col-12 col-md-4">
                <q-input v-model="profile.estado_provincia" label="Estado / Provincia" dense outlined />
              </div>

              <div class="col-12 col-md-4">
                <q-input v-model="profile.tipo_centro" label="Tipo de centro" dense outlined />
              </div>

              <div class="col-12 col-md-4">
                <q-input v-model="profile.nombre_centro" label="Nombre del centro" dense outlined />
              </div>

              <div class="col-12 col-md-4">
                <q-input v-model="profile.etapa" label="Etapa" dense outlined />
              </div>

              <div class="col-12 col-md-4">
                <q-input v-model="profile.curso" label="Curso" dense outlined />
              </div>

              <div class="col-12 col-md-4">
                <q-input v-model="profile.clase_letra" label="Clase / Letra" dense outlined />
              </div>

              <div class="col-12 col-md-4">
                <q-input
                  v-model.number="profile.minutos_descanso"
                  type="number"
                  dense
                  outlined
                  label="Descanso (minutos)"
                />
              </div>

              <div class="col-12 col-md-4">
                <q-input
                  v-model.number="profile.minutos_estudio_max"
                  type="number"
                  dense
                  outlined
                  label="Máximo estudio continuo (minutos)"
                />
              </div>

            </div>

            <div class="row justify-end q-mt-md">
              <q-btn
                color="primary"
                no-caps
                :loading="savingProfile"
                label="Guardar datos personales"
                @click="onGuardarPerfil"
              />
            </div>
          </q-tab-panel>

          <!-- TAB 2: TIEMPO DE ESTUDIO -->
          <q-tab-panel name="tiempo">
            <div class="text-subtitle1 text-weight-bold q-mb-md">Tiempo de estudio</div>

            <q-tabs
              v-model="diaActivo"
              dense
              active-color="primary"
              indicator-color="primary"
              align="justify"
            >
              <q-tab v-for="d in diasSemana" :key="d.value" :name="d.value" :label="d.label" />
            </q-tabs>

            <q-separator spaced />

            <div>
              <!-- HABITUAL -->
              <div class="text-caption text-grey-7 q-mb-xs">Tiempo habitual</div>

              <div
                v-for="(bloque, index) in horarios[diaActivo].habitual"
                :key="'h-'+index"
                class="row items-center q-col-gutter-sm q-mb-xs"
              >
                <div class="col-5 col-md-3">
                  <q-input
                    v-model="bloque.hora_inicio"
                    dense
                    outlined
                    label="De"
                    mask="##:##"
                    hint="HH:MM"
                  />
                </div>
                <div class="col-5 col-md-3">
                  <q-input
                    v-model="bloque.hora_fin"
                    dense
                    outlined
                    label="A"
                    mask="##:##"
                    hint="HH:MM"
                  />
                </div>
                <div class="col-auto">
                  <q-btn
                    dense
                    flat
                    round
                    icon="remove"
                    color="negative"
                    @click="removeBloque(diaActivo, 'habitual', index)"
                  />
                </div>
              </div>

              <q-btn
                outline
                color="primary"
                icon="add"
                dense
                no-caps
                class="q-mt-xs"
                label="Añadir intervalo"
                @click="addBloque(diaActivo, 'habitual')"
              />

              <q-separator spaced />

              <!-- EXTRA -->
              <div class="text-caption text-grey-7 q-mb-xs">Tiempo extra</div>

              <div
                v-for="(bloque, index) in horarios[diaActivo].extra"
                :key="'e-'+index"
                class="row items-center q-col-gutter-sm q-mb-xs"
              >
                <div class="col-5 col-md-3">
                  <q-input
                    v-model="bloque.hora_inicio"
                    dense
                    outlined
                    label="De"
                    mask="##:##"
                    hint="HH:MM"
                  />
                </div>
                <div class="col-5 col-md-3">
                  <q-input
                    v-model="bloque.hora_fin"
                    dense
                    outlined
                    label="A"
                    mask="##:##"
                    hint="HH:MM"
                  />
                </div>
                <div class="col-auto">
                  <q-btn
                    dense
                    flat
                    round
                    icon="remove"
                    color="negative"
                    @click="removeBloque(diaActivo, 'extra', index)"
                  />
                </div>
              </div>

              <q-btn
                outline
                color="primary"
                icon="add"
                dense
                no-caps
                class="q-mt-xs"
                label="Añadir intervalo extra"
                @click="addBloque(diaActivo, 'extra')"
              />
            </div>

            <div class="row justify-end q-mt-md">
              <q-btn
                color="primary"
                no-caps
                :loading="savingHorarios"
                label="Guardar horarios de estudio"
                @click="onGuardarHorarios"
              />
            </div>
          </q-tab-panel>

          <!-- TAB 3: ASIGNATURAS -->
          <q-tab-panel name="materias">
            <div class="text-subtitle1 text-weight-bold q-mb-md">Asignaturas</div>

            <div class="row q-col-gutter-md items-end q-mb-md">
              <div class="col-12 col-md-4">
                <q-input v-model="materiaForm.nombre" label="Materia" dense outlined />
              </div>
              <div class="col-12 col-md-4">
                <q-input v-model="materiaForm.abreviatura" label="Abreviatura" dense outlined />
              </div>
              <div class="col-auto">
                <q-btn
                  color="primary"
                  no-caps
                  label="Añadir"
                  @click="agregarMateria"
                />
              </div>
            </div>

            <q-table
              :rows="materias"
              :columns="columnsMaterias"
              row-key="id"
              flat
              bordered
              dense
              hide-pagination
            >
              <template #body-cell-acciones="props">
                <q-td :props="props">
                  <q-btn
                    dense
                    flat
                    round
                    icon="delete"
                    color="negative"
                    @click="eliminarMateria(props.row.id)"
                  />
                </q-td>
              </template>
            </q-table>
          </q-tab-panel>

        </q-tab-panels>
      </q-card-section>

    </q-card>
  </q-page>
</template>

<script>
export default {
  name: 'ProfileSettingsPage',

  data () {
    return {
      tab: 'personal',

      savingProfile: false,
      savingHorarios: false,

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
        { name: 'nombre', label: 'Materia', field: 'nombre', align: 'left' },
        { name: 'abreviatura', label: 'Abrev.', field: 'abreviatura', align: 'left' },
        { name: 'acciones', label: '', align: 'right' }
      ]
    }
  },

  created () {
    // Inicializar estructura horarios sin this.$set (Vue 3)
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
    // -------- PERFIL --------
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

    onGuardarPerfil () {
      this.savingProfile = true
      this.guardarPerfil()
        .then(() => {
          this.$q.notify({ type: 'positive', message: 'Datos personales actualizados' })
        })
        .finally(() => {
          this.savingProfile = false
        })
    },

    // -------- HORARIOS --------
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

      // Estrategia simple: borrar todo y volver a crear
      return this.$axios.get('/study-schedules')
        .then(({ data }) => {
          const deletes = data.map(s => this.$axios.delete(`/study-schedules/${s.id}`))
          return Promise.all(deletes).then(() => {
            if (payload.length === 0) return
            return this.$axios.post('/study-schedules', payload)
          })
        })
    },

    onGuardarHorarios () {
      this.savingHorarios = true
      this.guardarHorarios()
        .then(() => {
          this.$q.notify({ type: 'positive', message: 'Horarios de estudio actualizados' })
        })
        .finally(() => {
          this.savingHorarios = false
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

    // -------- MATERIAS --------
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
          this.$q.notify({ type: 'positive', message: 'Asignatura añadida' })
        })
        .catch(() => {
          this.$q.notify({ type: 'negative', message: 'No se pudo crear la asignatura' })
        })
    },

    eliminarMateria (id) {
      this.$axios.delete(`/materias/${id}`)
        .then(() => {
          this.materias = this.materias.filter(m => m.id !== id)
          this.$q.notify({ type: 'positive', message: 'Asignatura eliminada' })
        })
        .catch(() => {
          this.$q.notify({ type: 'negative', message: 'No se pudo eliminar la asignatura' })
        })
    }
  }
}
</script>
