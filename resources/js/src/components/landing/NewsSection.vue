<template>
  <section id="news" class="py-12 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-10">
        <h2 class="section-title">Info Seru & Promo Pedas!</h2>
        <p class="section-subtitle">Jangan lewatkan update terbaru dari Basreng AZ-2</p>
      </div>

      <!-- News Grid -->
      <div v-if="loading" class="flex justify-center py-12">
        <LoadingSpinner size="lg" text="Memuat berita..." />
      </div>

      <div v-else-if="news.length === 0" class="py-12">
        <EmptyState
          title="Belum ada berita"
          description="Berita dan promo terbaru akan segera hadir!"
          :icon="Newspaper"
        />
      </div>

      <div v-else>
        <BaseCarousel
          :items="news"
          :items-per-view-desktop="3"
          :items-per-view-mobile="1"
          :gap="24"
        >
          <template #default="{ item: item }">
            <article class="flex-shrink-0">
              <div class="px-2">
                <div class="card group cursor-pointer">
                  <div class="relative overflow-hidden rounded-xl">
                    <img
                      :src="item.image"
                      :alt="item.title"
                      class="w-full h-48 object-cover transform group-hover:scale-110 transition-transform duration-500"
                    />
                  </div>
                  <div class="p-5">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2 group-hover:text-primary transition-colors">
                      {{ item.title }}
                    </h3>
                    <p class="text-gray-600 text-sm line-clamp-2">{{ item.description }}</p>
                  </div>
                </div>
              </div>
            </article>
          </template>
        </BaseCarousel>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { Newspaper } from 'lucide-vue-next'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import EmptyState from '@/components/common/EmptyState.vue'
import BaseCarousel from '@/components/common/BaseCarousel.vue'
import { newsService } from '@/services/news.service'
import type { News } from '@/types'

const loading = ref(false)
const news = ref<News[]>([])

const fetchNews = async () => {
  loading.value = true
  try {
    const response = await newsService.getAll({ per_page: 6 })
    news.value = response.data
  } catch (err) {
    console.error('Fetch news error:', err)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchNews()
})
</script>
