<template>
  <q-layout view="lHh Lpr lFf">
    <q-page-container class="bg-grey-2">
      <q-page class="flex flex-center" :style="bgStyle">
      <q-card
        class="q-pa-lg q-px-xl q-mx-auto"
        style="max-width: 520px; width: 92vw"
        bordered
        flat
      >
        <!-- Encabezado -->
        <div class="column items-center q-mb-md">
          <q-img src="logo.png" ratio="1" style="width:110px" class="q-mb-sm" />
          <q-chip
            color="grey-3"
            text-color="grey-8"
            class="q-mt-xs"
            square
            dense
            icon="schedule"
          >
            <span class="text-subtitle2"><b>REGULATE</b> · Web</span>
          </q-chip>
        </div>

        <q-separator spaced />

        <!-- Cuerpo -->
        <div>
          <div class="text-h6 text-weight-bold q-mb-xs">Iniciar sesión</div>
          <div class="text-body2 text-grey-7 q-mb-md">
            Accede al panel de administración con tu cuenta de Google.
          </div>

          <!-- Botón Google -->
          <q-btn
            unelevated
            no-caps
            color="red-6"
            class="full-width q-py-sm q-mb-sm"
            :loading="loading"
            @click="loginGoogle"
          >
            <div class="row items-center justify-center q-gutter-sm">
              <q-icon name="fab fa-google" />
              <span class="text-subtitle2">Iniciar sesión con Google</span>
            </div>
          </q-btn>

          <!-- Opcional: recordar sesión / ayuda -->
          <div class="row items-center q-mt-sm q-mb-md">
            <q-checkbox v-model="rememberMe" label="Recuérdame" color="primary" dense />
            <q-space />
            <q-btn flat dense no-caps class="text-weight-medium text-grey-7" label="¿Necesitas ayuda?" />
          </div>

          <!-- Aviso -->
          <q-banner dense class="bg-grey-2 text-grey-8 q-pa-sm rounded-borders">
            <div class="row items-center q-gutter-sm">
              <q-icon name="info" size="18px" />
              <span>Usa tu correo institucional si tu organización lo requiere.</span>
            </div>
          </q-banner>
        </div>

        <q-separator spaced />

        <!-- Footer -->
        <div class="text-center text-caption text-grey-7">
          © {{ year }} REGULATE · Web. Todos los derechos reservados.
        </div>
      </q-card>
    </q-page>
    </q-page-container>
  </q-layout>
</template>

<script>
export default {
  name: 'LoginREGULATE',
  data () {
    return {
      loading: false,
      rememberMe: true,
      year: new Date().getFullYear(),
      // Ajusta tu base URL aquí o vía .env (QUASAR_*)
      // API_URL: process.env.API_URL || 'http://localhost:8000/api'
    }
  },
  computed: {
    // Fondo con blur suave sin CSS extra (solo inline style)
    bgStyle () {
      return {
        backgroundImage: "url('./../bg.jpg')",
        backgroundSize: 'cover',
        backgroundPosition: 'center',
        minHeight: '100vh',
        backdropFilter: 'blur(2px)'
      }
    }
  },
  methods: {
    loginGoogle () {
      this.loading = true
      // Tu backend ya tiene /auth/google/redirect → /auth/callback?token=...
      window.location.href = `${this.$url}/auth/google/redirect`
    }
  }
}
</script>

<style scoped>
/* Solo un toque de radios sin abusar de CSS */
.rounded-borders { border-radius: 12px; }
</style>
