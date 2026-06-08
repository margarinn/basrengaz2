<template>
  <div class="card group overflow-hidden rounded-xl relative z-0">
    <div class="relative overflow-hidden rounded-t-xl m-4 bg-gray-100 z-0">
      <img
        v-if="product.image"
        :src="product.image"
        :alt="product.name"
        class="w-full h-64 object-cover transform group-hover:scale-110 transition-transform duration-500 rounded-lg block transform-gpu"
      />
      <div v-else class="w-full h-64 flex items-center justify-center rounded-lg">
        <span class="text-gray-400">No Image</span>
      </div>
      <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none rounded-lg"></div>
    </div>
    <div class="p-4 text-center">
      <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ product.name }}</h3>
      <p class="text-primary font-semibold mb-2">{{ product.formatted_price || formatPrice(product.price) }}</p>
      <p class="text-gray-600 text-sm line-clamp-2">{{ product.description }}</p>
    </div>
  </div>
</template>

<script setup lang="ts">

import type { Product } from '@/types'

const formatPrice = (price: number) => new Intl.NumberFormat('id-ID').format(price)

interface Props {
  product: Product
}

defineProps<Props>()


</script>