<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Selamat Datang di Keuangan Kamu!</h1>
        <p class="text-gray-600">Kelola semua laporan keuangan.</p>
      </div>
      <BaseButton @click="openAddModal" class="self-start">
        <Plus class="w-5 h-5 mr-2" />
        Tambah Laporan
      </BaseButton>
    </div>

    <!-- Filters -->
    <div class="flex flex-col sm:flex-row gap-4">
      <div class="relative flex-1">
        <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Cari laporan..."
          class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500"
        />
      </div>
    </div>

    <!-- Table -->
    <div class="card overflow-hidden">
      <DataTable
        :columns="columns"
        :data="paginatedFinance"
        :loading="loading"
        :current-page="currentPage"
        :total-pages="totalPages"
        :per-page="perPage"
        :total-items="filteredFinance.length"
        empty-title="Belum ada laporan keuangan"
        empty-description="Tambah laporan keuangan pertama Anda"
        @page-change="onPageChange"
      >
        <template #cell-revenue="{ row }">
          Rp {{ formatPrice(row.revenue) }}
        </template>
        <template #cell-expenses="{ row }">
          Rp {{ formatPrice(row.expenses) }}
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
    <FinanceFormModal
      v-model="isModalOpen"
      :finance="selectedFinance"
      :loading="isSubmitting"
      @submit="handleSubmit"
    />

    <!-- Delete Confirmation -->
    <DeleteConfirmModal
      v-model="isDeleteModalOpen"
      :item-name="selectedFinance?.week || ''"
      :loading="isSubmitting"
      @confirm="handleDelete"
    />
    
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed, watch } from 'vue'
import { Plus, Search } from 'lucide-vue-next'
import FinanceFormModal from '@/components/dashboard/FinanceModal.vue'
import DataTable from '@/components/dashboard/DataTable.vue'
import BaseButton from '@/components/common/BaseButton.vue'
import DeleteConfirmModal from '@/components/dashboard/DeleteConfirmModal.vue'
import { financeService } from '@/services/finance.service'
import type { Finance, FinanceFormData } from '@/types'

const columns = [
  { key: 'id', label: 'No' },
  { key: 'week', label: 'Minggu' },
  { key: 'revenue', label: 'Pemasukan' },
  { key: 'expenses', label: 'Pengeluaran' },
  { key: 'description', label: 'Deskripsi' }
]

const loading = ref(false)
const finances = ref<Finance[]>([])
const searchQuery = ref('')
const isModalOpen = ref(false)
const isDeleteModalOpen = ref(false)
const isSubmitting = ref(false)
const selectedFinance = ref<Finance | null>(null)
const currentPage = ref(1)
const perPage = ref(4)

const filteredFinance = computed(() => {
  if (!searchQuery.value) return finances.value
  const query = searchQuery.value.toLowerCase()
  return finances.value.filter(f =>
    f.week.toLowerCase().includes(query) || f.description.toLowerCase().includes(query)
  )
})

const totalPages = computed(() =>
  Math.max(1, Math.ceil(filteredFinance.value.length / perPage.value))
)

const paginatedFinance = computed(() => {
  const start = (currentPage.value - 1) * perPage.value
  return filteredFinance.value.slice(start, start + perPage.value)
})

watch(searchQuery, () => { currentPage.value = 1 })
const onPageChange = (page: number) => { currentPage.value = page }
const formatPrice = (price: number) => new Intl.NumberFormat('id-ID').format(price)

const openAddModal = () => { selectedFinance.value = null; isModalOpen.value = true }
const openEditModal = (finance: Finance) => { selectedFinance.value = finance; isModalOpen.value = true }
const openDeleteModal = (finance: Finance) => { selectedFinance.value = finance; isDeleteModalOpen.value = true }

const handleSubmit = async (formData: FinanceFormData) => {
  isSubmitting.value = true
  try {
    if (selectedFinance.value) {
      await financeService.update(selectedFinance.value.id, formData)
    } else {
      await financeService.create(formData)
    }
    isModalOpen.value = false
    await fetchFinances()
  } catch (err: any) {
    console.error('Finance save error:', err)
    alert(err.response?.data?.message || 'Gagal menyimpan data keuangan')
  } finally {
    isSubmitting.value = false
  }
}

const handleDelete = async () => {
  if (!selectedFinance.value) return
  isSubmitting.value = true
  try {
    await financeService.delete(selectedFinance.value.id)
    isDeleteModalOpen.value = false
    await fetchFinances()
  } catch (err: any) {
    console.error('Finance delete error:', err)
    alert(err.response?.data?.message || 'Gagal menghapus data keuangan')
  } finally {
    isSubmitting.value = false
  }
}

const fetchFinances = async () => {
  loading.value = true
  try {
    const response = await financeService.getAll({ per_page: 100 })
    finances.value = response.data
  } catch (err) {
    console.error('Fetch finances error:', err)
  } finally {
    loading.value = false
  }
}

onMounted(() => { fetchFinances() })
</script>

