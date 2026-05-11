<template>
  <header class="bg-white border-b border-gray-200 sticky top-0 z-50 w-full">
    <div class="relative h-16 px-4 sm:px-6 lg:px-8">
      <!-- Logo di pojok kiri -->
      <div class="absolute left-4 sm:left-6 lg:left-8 top-1/2 transform -translate-y-1/2 flex items-center gap-4">
        <button
          v-if="isMobile"
          @click="$emit('toggle-sidebar')"
          class="p-2 rounded-lg hover:bg-gray-100 lg:hidden"
        >
          <Menu class="w-6 h-6" />
        </button>
        <router-link to="/dashboard" class="flex items-center space-x-2">
          <div class="w-10 h-10 bg-primary rounded-full flex items-center justify-center">
            <span class="text-white font-bold text-lg">AZ</span>
          </div>
          <span class="text-xl font-bold text-primary">
            Basreng <span class="text-accent-orange">AZ-2</span>
          </span>
        </router-link>
      </div>

      <!-- Profile di pojok kanan -->
      <div class="absolute right-4 sm:right-6 lg:right-8 top-1/2 transform -translate-y-1/2 flex items-center gap-4">
        <div class="flex items-center gap-3">
          <div class="text-right hidden sm:block">
            <p class="text-sm font-medium text-gray-900">{{ userName }}</p>
            <p class="text-xs text-gray-500">Administrator</p>
          </div>
          <router-link
            to="/dashboard/profile"
            class="flex items-center gap-2 p-2 rounded-lg hover:bg-gray-100"
          >
            <div class="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center">
              <User class="w-5 h-5 text-primary" />
            </div>
          </router-link>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { Menu, User } from 'lucide-vue-next'
import { useAuthStore } from '@/stores/auth'

interface Props {
  title: string
  isMobile: boolean
}

defineProps<Props>()

const emit = defineEmits<{
  'toggle-sidebar': []
}>()

const authStore = useAuthStore()

const userName = computed(() => authStore.userName || 'Admin')
</script>
