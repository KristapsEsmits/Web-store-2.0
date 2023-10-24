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
      path: '/about',
      name: 'about',
      component: () => import('../views/AboutView.vue')
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
    
  ]
})

export default router
