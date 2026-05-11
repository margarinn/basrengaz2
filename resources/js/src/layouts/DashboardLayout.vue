<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <Header
      :title="pageTitle"
      :is-mobile="isMobile"
      @toggle-sidebar="isSidebarOpen = !isSidebarOpen"
    />

    <div class="flex">
      <!-- Sidebar -->
      <Sidebar
        :is-open="isSidebarOpen"
        :is-mobile="isMobile"
        @close="isSidebarOpen = false"
      />

      <!-- Main Content -->
      <main class="flex-1 flex flex-col min-w-0">
        <div class="flex-1 p-4 sm:p-6 lg:p-8 overflow-auto">
          <router-view />
        </div>
      </main>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRoute } from 'vue-router'
import Sidebar from '@/components/dashboard/Sidebar.vue'
import Header from '@/components/dashboard/Header.vue'

const route = useRoute()
const isSidebarOpen = ref(false)
const isMobile = ref(false)

const pageTitle = computed(() => {
  return (route.meta.title as string) || route.name?.toString() || 'Dashboard'
})

// Check screen size
const checkScreenSize = () => {
  isMobile.value = window.innerWidth < 1024
  if (!isMobile.value) {
    isSidebarOpen.value = true
  }
}

onMounted(() => {
  checkScreenSize()
  window.addEventListener('resize', checkScreenSize)
})

onUnmounted(() => {
  window.removeEventListener('resize', checkScreenSize)
})
</script>
