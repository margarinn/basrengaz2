<template>
  <aside :class="sidebarClasses">
    <!-- Navigation -->
    <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
      <router-link
        v-for="item in menuItems"
        :key="item.name"
        :to="item.path"
        :class="navLinkClasses(item.path)"
        @click="handleLinkClick"
      >
        <component :is="item.icon" class="w-5 h-5" />
        <span>{{ item.name }}</span>
      </router-link>
    </nav>

    <!-- Logout Button -->
    <div class="p-4 border-t border-gray-100">
      <button
        @click="handleLogout"
        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-red-600 hover:bg-red-50 transition-colors"
      >
        <LogOut class="w-5 h-5" />
        <span>Keluar</span>
      </button>
    </div>
  </aside>

  <!-- Mobile Overlay -->
  <Transition
    enter-active-class="transition-opacity duration-300"
    enter-from-class="opacity-0"
    enter-to-class="opacity-100"
    leave-active-class="transition-opacity duration-200"
    leave-from-class="opacity-100"
    leave-to-class="opacity-0"
  >
    <div
      v-if="isOpen && isMobile"
      class="fixed inset-0 bg-black/50 z-30 lg:hidden"
      @click="$emit('close')"
    />
  </Transition>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import {
  LayoutDashboard,
  Package,
  Newspaper,
  Star,
  TrendingUp,
  LogOut
} from 'lucide-vue-next'
import { useAuthStore } from '@/stores/auth'

interface Props {
  isOpen: boolean
  isMobile: boolean
}

const props = defineProps<Props>()
const emit = defineEmits<{
  close: []
}>()

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()

const menuItems = [
  { name: 'Dashboard', path: '/dashboard', icon: LayoutDashboard },
  { name: 'Produk', path: '/dashboard/produk', icon: Package },
  { name: 'Berita', path: '/dashboard/berita', icon: Newspaper },
  { name: 'Rating', path: '/dashboard/rating', icon: Star },
  { name: 'Keuangan', path: '/dashboard/keuangan', icon: TrendingUp }
]

const sidebarClasses = computed(() => {
  const baseClasses = 'fixed lg:sticky left-0 z-40 w-64 bg-white shadow-sidebar flex flex-col transition-transform duration-300'
  const positionClasses = props.isMobile ? 'top-16 bottom-0' : 'top-16 h-[calc(100vh-4rem)]'
  const mobileClasses = props.isMobile
    ? props.isOpen
      ? 'translate-x-0'
      : '-translate-x-full'
    : 'translate-x-0'
  return [baseClasses, positionClasses, mobileClasses].join(' ')
})

const navLinkClasses = (path: string) => {
  const isActive = route.path === path
  return [
    'flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200',
    isActive
      ? 'bg-primary/10 text-primary font-medium'
      : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
  ].join(' ')
}

const handleLinkClick = () => {
  if (props.isMobile) {
    emit('close')
  }
}

const handleLogout = async () => {
  await authStore.logout()
  router.push('/login')
}
</script>
