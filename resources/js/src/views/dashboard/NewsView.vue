<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Selamat Datang di Berita Kamu!</h1>
        <p class="text-gray-600">Kelola berita seputar Basreng AZ-2.</p>
      </div>
      <BaseButton @click="openAddModal" class="self-start">
        <Plus class="w-5 h-5 mr-2" />
        Tambah Berita
      </BaseButton>
    </div>

    <!-- Filters -->
    <div class="flex flex-col sm:flex-row gap-4">
      <div class="relative flex-1">
        <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Cari berita..."
          class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500"
        />
      </div>
    </div>

    <!-- Table -->
    <div class="card overflow-hidden">
      <DataTable
        :columns="columns"
        :data="paginatedNews"
        :loading="loading"
        :current-page="currentPage"
        :total-pages="totalPages"
        :per-page="perPage"
        :total-items="filteredNews.length"
        empty-title="Belum ada berita"
        empty-description="Tambah berita pertama Anda"
        @page-change="onPageChange"
      >
        <template #cell-image="{ row }">
          <img
            :src="row.image"
            :alt="row.title"
            class="w-20 h-14 object-cover rounded-lg"
          />
        </template>
        <template #cell-description="{ value }">
          <div class="max-w-[220px] line-clamp-2 break-words hover:line-clamp-none transition-all">
            {{ value }}
          </div>
        </template>
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
    <NewsModal
      v-model="isModalOpen"
      :news="selectedNews"
      :loading="isSubmitting"
      @submit="handleSubmit"
    />

    <!-- Delete Confirmation -->
    <DeleteConfirmModal
      v-model="isDeleteModalOpen"
      :item-name="selectedNews?.title || ''"
      :loading="loading"
      @confirm="handleDelete"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import { Plus, Search } from 'lucide-vue-next'
import DataTable from '@/components/dashboard/DataTable.vue'
import DeleteConfirmModal from '@/components/dashboard/DeleteConfirmModal.vue'
import BaseButton from '@/components/common/BaseButton.vue'
import NewsModal from '@/components/dashboard/NewsModal.vue'
import { newsService } from '@/services/news.service'
import type { News, NewsFormData } from '@/types'

const columns = [
  { key: 'id', label: 'No' },
  { key: 'title', label: 'Judul' },
  { key: 'image', label: 'Gambar' },
  { key: 'description', label: 'Deskripsi' },
]

const loading = ref(false)
const news = ref<News[]>([])
const searchQuery = ref('')
const isModalOpen = ref(false)
const isDeleteModalOpen = ref(false)
const isSubmitting = ref(false)
const selectedNews = ref<News | null>(null)
const currentPage = ref(1)
const perPage = ref(4)

const filteredNews = computed(() => {
  if (!searchQuery.value) return news.value
  const query = searchQuery.value.toLowerCase()
  return news.value.filter(n => n.title.toLowerCase().includes(query))
})

const totalPages = computed(() =>
  Math.max(1, Math.ceil(filteredNews.value.length / perPage.value))
)

const paginatedNews = computed(() => {
  const start = (currentPage.value - 1) * perPage.value
  return filteredNews.value.slice(start, start + perPage.value)
})

watch(searchQuery, () => { currentPage.value = 1 })
const onPageChange = (page: number) => { currentPage.value = page }

const openAddModal = () => { selectedNews.value = null; isModalOpen.value = true }
const openEditModal = (item: News) => { selectedNews.value = item; isModalOpen.value = true }
const openDeleteModal = (item: News) => { selectedNews.value = item; isDeleteModalOpen.value = true }

const handleSubmit = async (formData: NewsFormData) => {
  isSubmitting.value = true
  try {
    const fd = new FormData()
    fd.append('title', formData.title)
    fd.append('body', formData.description)
    fd.append('is_published', 'true')
    if (formData.image instanceof File) {
      fd.append('image', formData.image)
    }
    if (selectedNews.value) {
      await newsService.update(selectedNews.value.id, fd)
    } else {
      await newsService.create(fd)
    }
    isModalOpen.value = false
    await fetchNews()
  } catch (err: any) {
    console.error('News save error:', err)
    alert(err.response?.data?.message || 'Gagal menyimpan berita')
  } finally {
    isSubmitting.value = false
  }
}

const handleDelete = async () => {
  if (!selectedNews.value) return
  isSubmitting.value = true
  try {
    await newsService.delete(selectedNews.value.id)
    isDeleteModalOpen.value = false
    await fetchNews()
  } catch (err: any) {
    console.error('News delete error:', err)
    alert(err.response?.data?.message || 'Gagal menghapus berita')
  } finally {
    isSubmitting.value = false
  }
}

const fetchNews = async () => {
  loading.value = true
  try {
    const response = await newsService.getAll({ per_page: 100 })
    news.value = response.data
  } catch (err) {
    console.error('Fetch news error:', err)
  } finally {
    loading.value = false
  }
}

onMounted(() => { fetchNews() })
</script>

