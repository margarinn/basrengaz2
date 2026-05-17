<template>
  <section id="ulasan" class="py-12 bg-[#F5F5F5]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="text-center mb-16">
        <h2 class="section-title">Apa Kata Pecinta AZ-2?</h2>
      </div>

      <div v-if="loading" class="flex justify-center py-12">
        <LoadingSpinner size="lg" text="Memuat ulasan..." />
      </div>

      <div v-else-if="ratings.length === 0" class="py-12">
        <EmptyState
          title="Belum ada ulasan"
          description="Jadilah yang pertama memberikan ulasan!"
          :icon="MessageSquare"
        />
      </div>

      <div v-else>
        <BaseCarousel
          ref="carouselRef"
          :items="ratings"
          :items-per-view-desktop="3"
          :items-per-view-mobile="1"
          :gap="24"
        >
          <template #default="{ item: rating }">
            <div class="flex-shrink-0 card p-6 relative pt-12">
              <!-- Quote Icon -->
              <div class="absolute -top-5 left-4 w-12 h-12 bg-primary rounded-full flex items-center justify-center z-10 shadow-lg">
                <Quote class="w-5 h-5 text-white" />
              </div>

              <!-- Stars -->
              <div class="flex gap-1 mb-4">
                <Star
                  v-for="i in 5"
                  :key="i"
                  :class="['w-5 h-5', i <= rating.rating ? 'text-yellow-400 fill-yellow-400' : 'text-gray-300']"
                />
              </div>

              <!-- Review -->
              <p class="text-gray-600 mb-6 italic line-clamp-3">"{{ rating.review }}"</p>

              <!-- Author -->
              <div class="flex items-center gap-3">
                <img
                  :src="rating.avatar"
                  :alt="rating.name"
                  class="w-12 h-12 rounded-full object-cover flex-shrink-0"
                />
                <p class="font-semibold text-gray-900 truncate">{{ rating.name }}</p>
              </div>
            </div>
          </template>
        </BaseCarousel>
      </div>

      <div class="flex justify-center mt-8">
        <BaseButton @click="openAddModal" size="sm">
          Tambah Ulasan
        </BaseButton>
      </div>

      <RatingModal
        v-model="isRatingModalOpen"
        :rating="selectedRating"
        @submit="handleRatingSubmit"
        @delete="handleDeleteRating"
      />

      <AuthPromptModal
        v-model="isAuthPromptOpen"
      />
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { Star, Quote, MessageSquare } from 'lucide-vue-next'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import EmptyState from '@/components/common/EmptyState.vue'
import BaseCarousel from '@/components/common/BaseCarousel.vue'
import type { Rating, RatingFormData } from '@/types'
import BaseButton from '../common/BaseButton.vue'
import RatingModal from './RatingModal.vue'
import AuthPromptModal from './AuthPromptModal.vue'
import { ratingService } from '@/services/rating.service'
import { useAuthStore } from '@/stores/auth'

// ─── State ──────────────────────────────────────────────────────────
const authStore = useAuthStore()
const router = useRouter()
const route = useRoute()
const loading = ref(false)
const isRatingModalOpen = ref(false)
const isAuthPromptOpen = ref(false)
const selectedRating = ref<Rating | null>(null)
const ratings = ref<Rating[]>([])

// ─── Carousel ──────────────────────────────────────────────────────
const carouselRef = ref<any>(null)

// ─── Lifecycle ─────────────────────────────────────────────────────
onMounted(() => {
  fetchRatings()

  if (route.query.action === 'ulasan') {
    // Scroll automatically to the ulasan section
    document.getElementById('ulasan')?.scrollIntoView({ behavior: 'smooth' })
    
    if (authStore.isAuthenticated) {
      openAddModal()
    }
    // Remove the query param from URL so it doesn't trigger on refresh
    const newQuery = { ...route.query }
    delete newQuery.action
    router.replace({ query: newQuery })
  }
})

onUnmounted(() => {
})

// ─── Service calls ─────────────────────────────────────────────────
const fetchRatings = async () => {
  loading.value = true
  try {
    const response = await ratingService.getAll({ per_page: 20 })
    ratings.value = response.data.map((r: any) => ({
      ...r,
      avatar: r.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(r.name)}&background=DC2626&color=fff&size=100`
    }))
  } catch (err) {
    console.error('Fetch ratings error:', err)
  } finally {
    loading.value = false
  }
}

const openAddModal = () => {
  if (!authStore.isAuthenticated) {
    isAuthPromptOpen.value = true
    return
  }
  selectedRating.value = null
  isRatingModalOpen.value = true
}

const handleRatingSubmit = async (data: RatingFormData) => {
  try {
    await ratingService.create(data)
    isRatingModalOpen.value = false
    await fetchRatings()
  } catch (err: any) {
    alert(err.response?.data?.message || 'Gagal menyimpan ulasan. Pastikan sudah login.')
  }
}

// ─── No Delete Allowed on Landing Page ────────────────────────────────
const handleDeleteRating = () => {
  isRatingModalOpen.value = false
}
</script>