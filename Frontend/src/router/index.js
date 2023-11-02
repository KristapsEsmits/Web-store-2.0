import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView
    },
    {
      path: '/test',
      name: 'test',
      component: () => import('../views/Test/Test.vue')
    },
    {
      path: '/test/create',
      name: 'test/create',
      component: () => import('../views/Test/Create.vue')
    },
    {
      path: '/test/:id/edit',
      name: 'test/edit',
      component: () => import('../views/Test/Edit.vue')
    },
    {
      path: '/login',
      name: 'login',
      component: () => import('../views/Auth/Login.vue')
    },
    {
      path: '/register',
      name: 'register',
      component: () => import('../views/Auth/Register.vue')
    },
    {
      path: '/admin',
      name: 'admin',
      component: () => import('../views/Admin/Admin.vue')
    },
    {
      path: '/profile',
      name: 'profile',
      component: () => import('../views/Auth/Profile.vue')
    },
  ]
})

export default router
