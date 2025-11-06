const routes = [
  {
    path: '/',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      { path: '', component: () => import('pages/IndexPage.vue'), meta: { requiresAuth: true } },
      { path: 'auth/callback', component: () => import('pages/AuthCallback.vue') },
      { path: '/materias', component: () => import('pages/materias/Materias.vue') },
      { path: '/tareas',   component: () => import('pages/tareas/Tareas.vue') },
    ]
  },
  {
    path: '/login',
    component: () => import('layouts/Login.vue')
  },
  // Always leave this as last one,
  // but you can also remove it
  {
    path: '/:catchAll(.*)*',
    component: () => import('pages/ErrorNotFound.vue')
  }
]

export default routes
