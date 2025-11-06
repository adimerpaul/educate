const routes = [
  {
    path: '/',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      { path: '', component: () => import('pages/IndexPage.vue') },
    //   http://localhost:9000/auth/callback?token=1|cbFHzKZ2FH19xL0qZhIVVoNf7S9uzab6YWkyy3gC8d7f0da3
      { path: 'auth/callback', component: () => import('pages/AuthCallback.vue') }
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
