<template>
  <div class="card group overflow-hidden rounded-xl">
    <div class="relative overflow-hidden rounded-t-xl m-4">
      <img
        :src="product.image"
        :alt="product.name"
        class="w-full h-64 object-cover transform group-hover:scale-110 transition-transform duration-500 rounded-lg"
      />
      <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
    </div>
    <div class="p-4 text-center">
      <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ product.name }}</h3>
      <p class="text-gray-600 text-sm line-clamp-2">{{ product.description }}</p>
    </div>
  </div>
</template>

<script setup lang="ts">

import type { Product } from '@/types'

interface Props {
  product: Product
}

defineProps<Props>()


</script>
