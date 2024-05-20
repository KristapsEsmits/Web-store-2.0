import {createRouter, createWebHistory} from 'vue-router'

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {
            path: '/',
            name: 'home',
            component: () => import('../views/HomeView/HomeView.vue'),
            meta: {tabName: 'Home'},
        },
        {
            path: '/login',
            name: 'login',
            component: () => import('../views/Auth/Login/Login.vue'),
        },
        {
            path: '/register',
            name: 'register',
            component: () => import('../views/Auth/Register/Register.vue'),
        },
        {
            path: '/admin',
            name: 'admin',
            component: () => import('../views/Admin/Dashboard/Admin.vue'),
        },
        {
            path: '/profile',
            name: 'profile',
            component: () => import('../views/Auth/Profile/Profile.vue'),
        },
        {
            path: '/profile/edit',
            name: 'profile/edit',
            component: () => import('../views/Auth/EditData/EditData.vue'),
        },
        {
            path: '/profile/change-password',
            name: 'profile/change-password',
            component: () => import('../views/Auth/ChangePassword/ChangePassword.vue'),
        },
        {
            path: '/admin/brands',
            name: 'admin/brands',
            component: () => import('../views/Admin/Brands/Brands.vue'),
        },
        {
            path: '/admin/brands/create',
            name: 'admin/brands/create',
            component: () => import('../views/Admin/Brands/Create.vue'),
        },
        {
            path: '/admin/brands/:id/edit',
            name: 'admin/brands/edit',
            component: () => import('../views/Admin/Brands/Edit.vue'),
        },
        {
            path: '/brands',
            name: 'brands',
            component: () => import('../views/Brands/Brands.vue'),
        },
        {
            path: '/brand/:id/products',
            name: 'brand-products',
            component: () => import('../views/BrandProducts/BrandProducts.vue'),
        },
        {
            path: '/products',
            name: 'products',
            component: () => import('../views/Products/Products.vue'),
        },
        {
            path: '/product/:id',
            name: 'product',
            component: () => import('../views/ProductView/ProductView.vue'),
        },
        {
            path: '/admin/items',
            name: 'admin/items',
            component: () => import('../views/Admin/Items/Items.vue'),
        },
        {
            path: '/admin/items/create',
            name: 'admin/items/create',
            component: () => import('../views/Admin/Items/Create.vue'),
        },
        {
            path: '/admin/categories',
            name: '/admin/categories',
            component: () => import('../views/Admin/categories/categories-table/categories.vue'),
        },
        {
            path: '/admin/categories/create',
            name: '/admin/categories/create',
            component: () => import('../views/Admin/categories/create-categories/create-categories.vue'),
        },
        {
            path: '/admin/categories/:id/edit',
            name: '/admin/categories/:id/edit',
            component: () => import('../views/Admin/categories/edit-categories/edit-categories.vue'),
        },
        {
            path: '/favorites',
            name: '/favorites',
            component: () => import('../views/Favorites/Favorites.vue'),
        },
    ]
})

export default router
