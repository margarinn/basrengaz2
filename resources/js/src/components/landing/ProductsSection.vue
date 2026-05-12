<template>
  <section id="products" class="py-12 bg-[#F5F5F5]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="text-center mb-12">
        <h2 class="section-title">Varian Rasa</h2>
        <p class="section-subtitle">Pilih varian favoritmu dan rasakan kriuknya!</p>
      </div>

      <!-- Products Grid -->
      <div v-if="loading" class="flex justify-center py-12">
        <LoadingSpinner size="lg" text="Memuat produk..." />
      </div>

      <div v-else-if="products.length === 0" class="py-12">
        <EmptyState
          title="Belum ada produk"
          description="Produk akan segera hadir. Nantikan update terbaru dari kami!"
          :icon="Package"
        />
      </div>

      <div v-else>
        <BaseCarousel
          :items="products"
          :items-per-view-desktop="4"
          :items-per-view-mobile="1"
          :gap="24"
        >
          <template #default="{ item: product }">
            <div class="flex-shrink-0">
              <div class="px-2">
                <ProductCard :product="product" />
              </div>
            </div>
          </template>
        </BaseCarousel>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">

import { ref, onMounted } from 'vue'
import { Package } from 'lucide-vue-next'
import ProductCard from './ProductCard.vue'
import BaseCarousel from '@/components/common/BaseCarousel.vue'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import EmptyState from '@/components/common/EmptyState.vue'
import { productService } from '@/services/product.service'
import type { Product } from '@/types'

const loading = ref(false)
const products = ref<Product[]>([])

const fetchProducts = async () => {
  loading.value = true
  try {
    const response = await productService.getAll({ per_page: 12 })
    products.value = response.data
  } catch (err) {
    console.error('Fetch products error:', err)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchProducts()
})
</script>
