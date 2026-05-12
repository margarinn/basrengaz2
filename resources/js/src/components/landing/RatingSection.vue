<template>
  <section class="py-12 bg-[#F5F5F5]">
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
            <div
              class="flex-shrink-0 card p-6 relative pt-12 cursor-pointer hover:shadow-lg transition-shadow"
              @click="onCardClick(rating)"
            >
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
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'
import { Star, Quote, MessageSquare } from 'lucide-vue-next'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import EmptyState from '@/components/common/EmptyState.vue'
import BaseCarousel from '@/components/common/BaseCarousel.vue'
import type { Rating, RatingFormData } from '@/types'
import BaseButton from '../common/BaseButton.vue'
import RatingModal from './RatingModal.vue'
import { ratingService } from '@/services/rating.service'

// ─── Data ──────────────────────────────────────────────────────────
const loading = ref(false)
const isRatingModalOpen = ref(false)
const selectedRating = ref<Rating | null>(null)
const ratings = ref<Rating[]>([])

// ─── Carousel ──────────────────────────────────────────────────────
const carouselRef = ref<any>(null)

const onCardClick = (rating: Rating) => {
  if (!carouselRef.value?.isDragging) openEditModal(rating)
}

// ─── Lifecycle ─────────────────────────────────────────────────────
onMounted(() => {
  fetchRatings()
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
  selectedRating.value = null
  isRatingModalOpen.value = true
}

const openEditModal = (rating: Rating) => {
  selectedRating.value = rating
  isRatingModalOpen.value = true
}

const handleRatingSubmit = async (data: RatingFormData) => {
  try {
    if (selectedRating.value) {
      await ratingService.update(selectedRating.value.id, data)
    } else {
      await ratingService.create(data)
    }
    selectedRating.value = null
    isRatingModalOpen.value = false
    await fetchRatings()
  } catch (err: any) {
    alert(err.response?.data?.message || 'Gagal menyimpan ulasan. Pastikan sudah login.')
  }
}

const handleDeleteRating = async () => {
  if (!selectedRating.value) return
  try {
    await ratingService.delete(selectedRating.value.id)
    selectedRating.value = null
    isRatingModalOpen.value = false
    await fetchRatings()
  } catch (err: any) {
    alert(err.response?.data?.message || 'Gagal menghapus ulasan.')
  }
}
</script>