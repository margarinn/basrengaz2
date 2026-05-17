import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

// Landing Page Routes
const landingRoutes = [
  {
    path: '/',
    name: 'Home',
    component: () => import('@/views/landing/HomeView.vue'),
    meta: { layout: 'landing' }
  },
  {
    path: '/profile',
    name: 'UserProfile',
    component: () => import('@/views/landing/ProfileView.vue'),
    meta: { layout: 'landing', requiresAuth: true }
  }
]

// Auth Routes
const authRoutes = [
  {
    path: '/login',
    name: 'Login',
    component: () => import('@/views/auth/LoginView.vue'),
    meta: { layout: 'auth' }
  },
  {
    path: '/register',
    name: 'Register',
    component: () => import('@/views/auth/RegisterView.vue'),
    meta: { layout: 'auth' }
  },
  {
    path: '/forgot-password',
    name: 'ForgotPassword',
    component: () => import('@/views/auth/ForgotPasswordView.vue'),
    meta: { layout: 'auth' }
  }
]

// Dashboard Routes
const dashboardRoutes = [
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: () => import('@/views/dashboard/DashboardView.vue'),
    meta: { layout: 'dashboard' }
  },
  {
    path: '/dashboard/produk',
    name: 'DashboardProducts',
    component: () => import('@/views/dashboard/ProductsView.vue'),
    meta: { layout: 'dashboard' }
  },
  {
    path: '/dashboard/berita',
    name: 'DashboardNews',
    component: () => import('@/views/dashboard/NewsView.vue'),
    meta: { layout: 'dashboard' }
  },
  {
    path: '/dashboard/rating',
    name: 'DashboardRatings',
    component: () => import('@/views/dashboard/RatingsView.vue'),
    meta: { layout: 'dashboard' }
  },
  {
    path: '/dashboard/keuangan',
    name: 'DashboardFinance',
    component: () => import('@/views/dashboard/FinanceView.vue'),
    meta: { layout: 'dashboard' }
  },
  {
    path: '/dashboard/profile',
    name: 'DashboardProfile',
    component: () => import('@/views/dashboard/ProfileView.vue'),
    meta: { layout: 'dashboard' }
  }
]

const router = createRouter({
  history: createWebHistory('/'),
  routes: [
    ...landingRoutes,
    ...authRoutes,
    ...dashboardRoutes,
    {
      path: '/:pathMatch(.*)*',
      name: 'NotFound',
      component: () => import('@/views/NotFoundView.vue'),
      meta: { layout: 'landing' }
    }
  ],
  scrollBehavior(to, _from, savedPosition) {
    if (savedPosition) {
      return savedPosition
    }
    if (to.hash) {
      return {
        el: to.hash,
        behavior: 'smooth'
      }
    }
    return { top: 0 }
  }
})

// Navigation Guards
router.beforeEach((to, _from, next) => {
  const authStore = useAuthStore()

  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next({ name: 'Login', query: { redirect: to.fullPath } })
    return
  }

  next()
})

export default router
