<template>
  <div class="flex flex-col items-center justify-center py-12 px-4 text-center">
    <div v-if="icon" class="mb-4">
      <component :is="icon" class="w-16 h-16 text-gray-300" />
    </div>
    <div v-else-if="image" class="mb-4">
      <img :src="image" :alt="title" class="w-32 h-32 object-contain" />
    </div>
    <h3 class="text-lg font-medium text-gray-900 mb-2">{{ title }}</h3>
    <p v-if="description" class="text-gray-500 max-w-sm mb-6">{{ description }}</p>
    <slot name="action" />
  </div>
</template>

<script setup lang="ts">
import type { Component } from 'vue'

interface Props {
  title: string
  description?: string
  icon?: Component
  image?: string
}

defineProps<Props>()
</script>
