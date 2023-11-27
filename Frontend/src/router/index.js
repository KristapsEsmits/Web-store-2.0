import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
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
      meta: { requiresAuth: true },
      component: () => import('../views/Auth/Profile.vue')
    },
    {
      path: '/brands',
      name: 'brands',
      component: () => import('../views/Admin/Brands/Brands.vue')
    },
    {
      path: '/brands/create',
      name: 'brands/create',
      component: () => import('../views/Admin/Brands/Create.vue')
    },
    {
      path: '/brands/:id/edit',
      name: 'brands/edit',
      component: () => import('../views/Admin/Brands/Edit.vue')
    },
  ]
})

export default router
