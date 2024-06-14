import {createRouter, createWebHistory} from 'vue-router';

const routes = [
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
        meta: {requiresGuest: true},
    },
    {
        path: '/register',
        name: 'register',
        component: () => import('../views/Auth/Register/Register.vue'),
        meta: {requiresGuest: true},
    },
    {
        path: '/admin',
        name: 'admin',
        component: () => import('../views/Admin/Dashboard/Admin.vue'),
        meta: {requiresAdmin: true},
    },
    {
        path: '/profile',
        name: 'profile',
        component: () => import('../views/Auth/Profile/Profile.vue'),
        meta: {requiresAuth: true}
    },
    {
        path: '/profile/edit',
        name: 'profile/edit',
        component: () => import('../views/Auth/EditData/EditData.vue'),
        meta: {requiresAuth: true}
    },
    {
        path: '/profile/change-password',
        name: 'profile/change-password',
        component: () => import('../views/Auth/ChangePassword/ChangePassword.vue'),
        meta: {requiresAuth: true}
    },
    {
        path: '/admin/brands',
        name: 'admin/brands',
        component: () => import('../views/Admin/Brands/Brands.vue'),
        meta: {requiresAdmin: true}
    },
    {
        path: '/admin/brands/create',
        name: 'admin/brands/create',
        component: () => import('../views/Admin/Brands/Create.vue'),
        meta: {requiresAdmin: true}
    },
    {
        path: '/admin/brands/:id/edit',
        name: 'admin/brands/edit',
        component: () => import('../views/Admin/Brands/Edit.vue'),
        meta: {requiresAdmin: true}
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
        meta: {requiresAdmin: true}
    },
    {
        path: '/admin/items/create',
        name: 'admin/items/create',
        component: () => import('../views/Admin/Items/Create.vue'),
        meta: {requiresAdmin: true}
    },
    {
        path: '/admin/items/:id/edit',
        name: 'admin/items/edit',
        component: () => import('../views/Admin/Items/Edit.vue'),
        meta: {requiresAdmin: true}
    },
    {
        path: '/admin/categories',
        name: 'admin/categories',
        component: () => import('../views/Admin/categories/categories.vue'),
        meta: {requiresAdmin: true}
    },
    {
        path: '/admin/categories/create',
        name: 'admin/categories/create',
        component: () => import('../views/Admin/categories/create-categories.vue'),
        meta: {requiresAdmin: true}
    },
    {
        path: '/admin/categories/:id/edit',
        name: 'admin/categories/edit',
        component: () => import('../views/Admin/categories/EditCategories.vue'),
        meta: {requiresAdmin: true}
    },
    {
        path: '/admin/purchases',
        name: 'admin/purchases',
        component: () => import('../views/Admin/Purchases/Purchases.vue'),
        meta: {requiresAdmin: true}
    },
    {
        path: '/favorites',
        name: 'favorites',
        component: () => import('../views/Favorites/Favorites.vue'),
        meta: {requiresAuth: true}
    },
    {
        path: '/cart',
        name: 'cart',
        component: () => import('../views/Cart/Cart.vue'),
        meta: {requiresAuth: true}
    },
    {
        path: '/thank-you',
        name: 'thank-you',
        component: () => import('../views/ThankYouPage/ThankYouPage.vue'),
        meta: {requiresAuth: true}
    },
    {
        path: '/admin/inventory',
        name: '/admin/inventory',
        component: () => import('../views/admin/Inventory/Inventory.vue'),
        meta: {requiresAdmin: true}
    },
    {
        path: '/admin/inventory-edit/:id/edit',
        name: '/admin/inventory-edit',
        component: () => import('../views/admin/Inventory/InventoryEdit.vue'),
        meta: {requiresAdmin: true}
    },
    {
        path: '/terms-of-service',
        name: '/terms-of-service',
        component: () => import('../views/Tos/Tos.vue'),
        meta: {requiresAuth: true}
    },
    {
        path: '/admin/specification-titles/:id/edit',
        name: '/admin/specification-titles/:id/edit',
        component: () => import('../views/Admin/categories/EditSpecifiationType.vue'),
        meta: {requiresAdmin: true}
    },
    {
        path: '/admin/specification-titles/add',
        name: '/admin/specification-titles/add',
        component: () => import('../views/Admin/categories/AddSpecificationType.vue'),
        meta: {requiresAdmin: true}
    },
];

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes,
});

router.beforeEach((to, from, next) => {
    const isLoggedIn = !!localStorage.getItem('access_token');
    const user = JSON.parse(localStorage.getItem('user'));

    if (to.matched.some(record => record.meta.requiresAuth) && !isLoggedIn) {
        next({name: 'login'});
    } else if (to.matched.some(record => record.meta.requiresAdmin) && (!user || user.admin !== 1)) {
        next({name: 'login'});
    } else if (to.matched.some(record => record.meta.requiresGuest) && isLoggedIn) {
        next({name: 'home'});
    } else {
        next();
    }
});

export default router;
