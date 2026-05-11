<template>
  <Component :is="layout">
    <router-view />
  </Component>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import LandingLayout from '@/layouts/LandingLayout.vue'
import DashboardLayout from '@/layouts/DashboardLayout.vue'
import AuthLayout from '@/layouts/AuthLayout.vue'

const route = useRoute()

const layout = computed(() => {
  const layoutName = route.meta.layout as string
  switch (layoutName) {
    case 'dashboard':
      return DashboardLayout
    case 'auth':
      return AuthLayout
    case 'landing':
    default:
      return LandingLayout
  }
})
</script>
