<template>
  <q-layout view="lHh Lpr lFf">
    <!-- HEADER -->
    <q-header class="bg-white text-black" bordered>
      <q-toolbar>
        <q-btn
          flat
          color="primary"
          :icon="leftDrawerOpen ? 'keyboard_double_arrow_left' : 'keyboard_double_arrow_right'"
          aria-label="Menu"
          @click="toggleLeftDrawer"
          unelevated
          dense
        />
        <div class="row items-center q-gutter-sm">
          <!-- TÍTULO CAMBIADO -->
          <div class="text-subtitle1 text-weight-medium" style="line-height: 0.9">
            REGULATE · Panel <br>
            <q-badge color="warning" text-color="black" v-if="roleText" class="text-bold">{{ roleText }}</q-badge>
          </div>
        </div>

        <q-space />

        <div class="row items-center q-gutter-sm">
          <q-btn-dropdown flat unelevated no-caps dropdown-icon="expand_more">
            <template v-slot:label>
              <div class="row items-center no-wrap q-gutter-sm">
                <q-avatar rounded>
                  <q-img :src="`${$url}/..${$store.user.avatar}`" width="40px" height="40px" v-if="$store.user.avatar"/>
                  <q-icon name="person" v-else />
                </q-avatar>
                <div class="text-left" style="line-height: 1">
                  <div class="ellipsis" style="max-width: 130px;">
                    {{ $store.user.username }}
                  </div>
                </div>
              </div>
            </template>

            <q-separator />

            <q-item clickable v-ripple @click="logout" v-close-popup>
              <q-item-section avatar>
                <q-icon name="logout" />
              </q-item-section>
              <q-item-section>
                <q-item-label>Salir</q-item-label>
              </q-item-section>
            </q-item>
          </q-btn-dropdown>
        </div>
      </q-toolbar>
    </q-header>

    <!-- DRAWER -->
    <q-drawer
      v-model="leftDrawerOpen"
      bordered
      show-if-above
      :width="240"
      :breakpoint="500"
      class="bg-primary text-white"
    >
      <q-list class="q-pb-none">
        <!-- CABECERA DEL DRAWER CAMBIADA A EDUCATE -->
        <q-item-label header class="text-center q-pa-none q-pt-md">
          <q-avatar size="64px" class="q-mb-sm bg-white" rounded>
            <q-img src="/logo.png" width="90px" />
          </q-avatar>
          <div class="text-weight-bold text-white">REGULATE</div>
          <div class="text-caption text-white">Sistema Educativo</div>
        </q-item-label>

        <q-item-label header class="q-px-md text-grey-3 q-mt-sm">
          Menú
        </q-item-label>

        <!-- SOLO ESTOS DOS MENÚS -->
<!--        home menu-->
        <q-item dense to="/" exact clickable class="menu-item" active-class="menu-active" v-close-popup>
          <q-item-section avatar>
            <q-icon name="home" class="text-white"/>
          </q-item-section>
          <q-item-section>
            <q-item-label class="text-white">Inicio</q-item-label>
          </q-item-section>
        </q-item>

        <q-item dense to="/materias" exact clickable class="menu-item" active-class="menu-active" v-close-popup>
          <q-item-section avatar>
            <q-icon name="menu_book" class="text-white"/>
          </q-item-section>
          <q-item-section>
            <q-item-label class="text-white">Materias</q-item-label>
          </q-item-section>
        </q-item>

        <q-item dense to="/tareas" exact clickable class="menu-item" active-class="menu-active" v-close-popup>
          <q-item-section avatar>
            <q-icon name="assignment_turned_in" class="text-white"/>
          </q-item-section>
          <q-item-section>
            <q-item-label class="text-white">Tareas</q-item-label>
          </q-item-section>
        </q-item>
<!--        <q-item clickable to="/configuracion">-->
<!--          <q-item-section avatar><q-icon name="settings" /></q-item-section>-->
<!--          <q-item-section>Configuración</q-item-section>-->
<!--        </q-item>-->
        <q-separator class="q-my-sm" />
        <q-item dense to="/configuracion" exact clickable class="menu-item" active-class="menu-active" v-close-popup>
          <q-item-section avatar>
            <q-icon name="person" class="text-white"/>
          </q-item-section>
          <q-item-section>
            <q-item-label class="text-white">Perfil</q-item-label>
          </q-item-section>
        </q-item>

        <!-- FOOTER DEL DRAWER CAMBIADO A EDUCATE -->
        <div class="q-pa-md">
          <div class="text-white-7 text-caption">
            REGULATE v{{ $version }}
          </div>
          <div class="text-white-7 text-caption">
            © {{ new Date().getFullYear() }} REGULATE · Web
          </div>
        </div>

        <q-item clickable class="text-white" @click="logout" v-close-popup>
          <q-item-section avatar>
            <q-icon name="logout" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Salir</q-item-label>
          </q-item-section>
        </q-item>
      </q-list>
    </q-drawer>

    <!-- PAGE -->
    <q-page-container class="bg-grey-2">
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script setup>
import { computed, getCurrentInstance, ref } from 'vue'
import { useCounterStore } from 'stores/example-store'

const { proxy } = getCurrentInstance()
const store = useCounterStore()

const leftDrawerOpen = ref(false)

// Helpers de permisos (no tocados)
function hasPerm (perm) {
  if (!perm) return true
  return store.permissions?.includes(perm)
}
function hasAnyPerm (perms = []) {
  return perms.some(p => hasPerm(p))
}

function toggleLeftDrawer () {
  leftDrawerOpen.value = !leftDrawerOpen.value
}

function logout () {
  proxy.$alert.dialog('¿Desea salir del sistema?')
    .onOk(() => {
      proxy.$axios.post('/logout')
        .then(() => {
          proxy.$store.isLogged = false
          proxy.$store.user = {}
          proxy.$store.permissions = []
          localStorage.removeItem('tokenEducate')
          proxy.$router.push('/login')
        })
        .catch(() => proxy.$alert.error('Error al cerrar sesión. Intente nuevamente.'))
    })
}

const roleText = computed(() => {
  const role = proxy.$store.user.role
  if (!role) return ''
  if (role === 'Administrador') return 'Administrador'
  return role
})
</script>

<style scoped>
.menu-item {
  border-radius: 10px;
  margin: 4px 8px;
  padding: 4px 6px;
}
.menu-active {
  background: rgba(255, 255, 255, 0.15);
  color: #fff !important;
  border-radius: 10px;
}
</style>
