<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Selamat Datang di Rating Kamu!</h1>
        <p class="text-gray-600">Kelola semua rating dan ulasan.</p>
      </div>
    </div>

    <!-- Filters -->
    <div class="flex flex-col sm:flex-row gap-4">
      <div class="relative flex-1">
        <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Cari ulasan..."
          class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500"
        />
      </div>
    </div>

    <!-- Table -->
    <div class="card overflow-hidden">
      <DataTable
        :columns="columns"
        :data="paginatedRatings"
        :loading="loading"
        :current-page="currentPage"
        :total-pages="totalPages"
        :per-page="perPage"
        :total-items="filteredRatings.length"
        empty-title="Belum ada ulasan"
        empty-description="Tambah ulasan pertama Anda"
        @page-change="onPageChange"
      >
        <template #actions="{ row }">
          <div class="flex justify-center gap-2">
            <button
              @click="openEditModal(row)"
              class="w-20 px-3 py-1.5 bg-secondary text-white text-sm font-medium rounded-lg hover:bg-secondary-600 transition-colors"
            >
              Edit
            </button>
            <button
              @click="openDeleteModal(row)"
              class="w-20 px-3 py-1.5 bg-red-500 text-white text-sm font-medium rounded-lg hover:bg-red-600 transition-colors"
            >
              Delete
            </button>
          </div>
        </template>
      </DataTable>
    </div>  

    <!-- Modal Add/Edit -->
    <RatingModal
      v-model="isModalOpen"
      :rating="selectedRating"
      :loading="isSubmitting"
      @submit="handleSubmit"
    />

    <!-- Delete Confirmation -->
    <DeleteConfirmModal
      v-model="isDeleteModalOpen"
      :item-name="selectedRating?.name || ''"
      :loading="loading"
      @confirm="handleDelete"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import { Search } from 'lucide-vue-next'
import DataTable from '@/components/dashboard/DataTable.vue'
import DeleteConfirmModal from '@/components/dashboard/DeleteConfirmModal.vue'
import RatingModal from '@/components/dashboard/RatingModal.vue'
import { ratingService } from '@/services/rating.service'
import type { Rating, RatingFormData } from '@/types'

const columns = [
  { key: 'id', label: 'No' },
  { key: 'name', label: 'Nama' },
  { key: 'rating', label: 'Rating' },
  { key: 'review', label: 'Ulasan' }
]

const loading = ref(false)
const ratings = ref<Rating[]>([])
const searchQuery = ref('')
const isModalOpen = ref(false)
const isDeleteModalOpen = ref(false)
const isSubmitting = ref(false)
const selectedRating = ref<Rating | null>(null)
const currentPage = ref(1)
const perPage = ref(4)

const filteredRatings = computed(() => {
  if (!searchQuery.value) return ratings.value
  const query = searchQuery.value.toLowerCase()
  return ratings.value.filter(r =>
    r.name.toLowerCase().includes(query) ||
    r.review.toLowerCase().includes(query) ||
    r.rating.toString().includes(query)
  )
})

const totalPages = computed(() =>
  Math.max(1, Math.ceil(filteredRatings.value.length / perPage.value))
)

const paginatedRatings = computed(() => {
  const start = (currentPage.value - 1) * perPage.value
  return filteredRatings.value.slice(start, start + perPage.value)
})

watch(searchQuery, () => { currentPage.value = 1 })
const onPageChange = (page: number) => { currentPage.value = page }

const openEditModal = (item: Rating) => { selectedRating.value = item; isModalOpen.value = true }
const openDeleteModal = (item: Rating) => { selectedRating.value = item; isDeleteModalOpen.value = true }

const handleSubmit = async (formData: RatingFormData) => {
  isSubmitting.value = true
  try {
    if (selectedRating.value) {
      await ratingService.update(selectedRating.value.id, formData)
    } else {
      await ratingService.create(formData)
    }
    isModalOpen.value = false
    await fetchRatings()
  } catch (err: any) {
    console.error('Rating save error:', err)
    alert(err.response?.data?.message || 'Gagal menyimpan ulasan')
  } finally {
    isSubmitting.value = false
  }
}

const handleDelete = async () => {
  if (!selectedRating.value) return
  isSubmitting.value = true
  try {
    await ratingService.delete(selectedRating.value.id)
    isDeleteModalOpen.value = false
    await fetchRatings()
  } catch (err: any) {
    console.error('Rating delete error:', err)
    alert(err.response?.data?.message || 'Gagal menghapus ulasan')
  } finally {
    isSubmitting.value = false
  }
}

const fetchRatings = async () => {
  loading.value = true
  try {
    const response = await ratingService.getAll({ per_page: 100 })
    ratings.value = response.data
  } catch (err) {
    console.error('Fetch ratings error:', err)
  } finally {
    loading.value = false
  }
}

onMounted(() => { fetchRatings() })
</script>

