<template>
  <q-page class="flex flex-center">
    <div class="row items-center q-gutter-sm">
      <q-spinner size="32px"/>
      <div>Iniciando sesión…</div>
    </div>
  </q-page>
</template>

<script>
export default {
  name: 'AuthCallback',
  created () {
    const token = this.$route.query.token
    // const redirect = this.$route.query.redirect || '/'
    console.log('token recibido en callback:', token)
    // if (!token) {
    //   this.$q.notify({ type: 'negative', message: 'Token no presente en el callback' })
    //   return this.$router.replace('/login')
    // }
    this.$axios.get('/me', {
      headers: {
        Authorization: `Bearer ${token}`
      }
    })
      .then(({ data }) => {
        this.$store.isLogged = true
        this.$store.user = data || {}
        localStorage.setItem('tokenEducate', token)
        this.$axios.defaults.headers.common.Authorization = `Bearer ${token}`
        this.$alert.success(`¡Bienvenido, ${data.name || 'usuario'}!`)

        // SI NO HA COMPLETADO EL WIZARD → /configuracion
        if (!data.onboarding_completed) {
          this.$router.replace('/configuracion')
        } else {
          this.$router.replace('/')
        }
      })
      .catch(() => {
        // si /me falla igual seguimos (ya hay token)
        this.$store.isLogged = true
      })

    // Guardar token y preparar axios
    // try {
    //   localStorage.setItem('tokenEducate', token) // usa tu clave preferida
    //   this.$axios.defaults.headers.common.Authorization = `Bearer ${token}`
    // } catch (_) {}
    //
    // // (Opcional) Traer perfil para poblar el store
    // this.$axios.get('/me')
    //   .then(({ data }) => {
    //     this.$store.isLogged = true
    //     this.$store.user = data || {}
    //     this.$store.permissions = (data?.permissions || []).map(p => p.name || p)
    //     this.$q.notify({ type: 'positive', message: '¡Bienvenido!' })
    //   })
    //   .catch(() => {
    //     // si /me falla igual seguimos (ya hay token)
    //     this.$store.isLogged = true
    //   })
    //   .finally(() => {
    //     this.$router.replace(redirect)
    //   })
  }
}
</script>
