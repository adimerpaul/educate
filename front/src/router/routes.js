const routes = [
  {
    path: '/',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      { path: '', component: () => import('pages/IndexPage.vue'), meta: { requiresAuth: true } },
      { path: 'auth/callback', component: () => import('pages/AuthCallback.vue') },
      { path: 'configuracion', component: () => import('pages/ConfigWizard.vue'), meta: { requiresAuth: true } },
      { path: '/materias', component: () => import('pages/materias/Materias.vue') },
      { path: '/tareas', component: () => import('pages/tareas/Tareas.vue') },
      {
        path: 'calendario',
        component: () => import('pages/calendar/CalendarPage.vue'),
        meta: { requiresAuth: true }
      }
      // {
      //   path: '/configuracion',
      //   component: () => import('pages/ProfileSettingsPage.vue'),
      //   meta: { requiresAuth: true }
      // }
    ]
  },
  {
    path: '/login',
    component: () => import('layouts/Login.vue')
  },
  {
    path: '/:catchAll(.*)*',
    component: () => import('pages/ErrorNotFound.vue')
  }
]

export default routes
