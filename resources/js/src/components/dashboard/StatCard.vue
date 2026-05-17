<template>
  <div class="card p-4">
    <div class="flex items-start gap-4">
      <div
        :class="[
          'w-16 h-16 rounded-xl flex items-center justify-center',
          iconBgClass
        ]"
      >
        <component :is="icon" class="w-8 h-8" :class="iconColorClass" />
      </div>
      <div class="flex-1 min-w-0">
        <div class="flex items-center gap-1">
          <h3 class="text-2xl font-bold text-gray-900 truncate" :title="value">{{ value }}</h3>
          <span
            v-if="change !== undefined"
            :class="[
              'text-sm font-medium flex items-center',
              change >= 0 ? 'text-green-600' : 'text-red-600'
            ]"
          >
            <TrendingUp v-if="change >= 0" class="w-4 h-4 mr-0.5" />
            <TrendingDown v-else class="w-4 h-4 mr-0.5" />
            {{ Math.abs(change) }}%
          </span>
        </div>
        <p class="text-sm font-medium text-gray-500">{{ title }}</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { TrendingUp, TrendingDown } from 'lucide-vue-next'
import type { Component } from 'vue'

interface Props {
  title: string
  value: string
  subtitle?: string
  change?: number
  icon: Component
  iconColor?: 'primary' | 'secondary' | 'success' | 'warning' | 'danger'
}

const props = withDefaults(defineProps<Props>(), {
  iconColor: 'primary'
})

const iconBgClass = computed(() => {
  const classes = {
    primary: 'bg-primary/10',
    secondary: 'bg-secondary/10',
    success: 'bg-green-100',
    warning: 'bg-yellow-100',
    danger: 'bg-red-100'
  }
  return classes[props.iconColor]
})

const iconColorClass = computed(() => {
  const classes = {
    primary: 'text-primary',
    secondary: 'text-secondary',
    success: 'text-green-600',
    warning: 'text-yellow-600',
    danger: 'text-red-600'
  }
  return classes[props.iconColor]
})
</script>
