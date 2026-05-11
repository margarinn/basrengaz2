<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Selamat Datang di Produk Kamu!</h1>
        <p class="text-gray-600">Kelola semua produk Basreng AZ-2.</p>
      </div>
      <BaseButton @click="openAddModal" class="self-start">
        <Plus class="w-5 h-5 mr-2" />
        Tambah Produk
      </BaseButton>
    </div>

    <!-- Filters -->
    <div class="flex flex-col sm:flex-row gap-4">
      <div class="relative flex-1">
        <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Cari produk..."
          class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500"
        />
      </div>
    </div>

    <!-- Table -->
    <div class="card overflow-hidden">
      <DataTable
        :columns="columns"
        :data="paginatedProducts"
        :loading="loading"
        :current-page="currentPage"
        :total-pages="totalPages"
        :per-page="perPage"
        :total-items="filteredProducts.length"
        empty-title="Belum ada produk"
        empty-description="Tambah produk pertama Anda"
        @page-change="onPageChange"
      >
        <template #cell-image="{ row }">
          <img
            :src="row.image"
            :alt="row.name"
            class="w-20 h-14 object-cover rounded-lg"
          />
        </template>
        <template #cell-price="{ row }">
          Rp {{ formatPrice(row.price) }}
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
    <ProductFormModal
      v-model="isModalOpen"
      :product="selectedProduct"
      :loading="isSubmitting"
      @submit="handleSubmit"
    />

    <!-- Delete Confirmation -->
    <DeleteConfirmModal
      v-model="isDeleteModalOpen"
      :item-name="selectedProduct?.name || ''"
      :loading="isSubmitting"
      @confirm="handleDelete"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import { Plus, Search } from 'lucide-vue-next'
import DataTable from '@/components/dashboard/DataTable.vue'
import ProductFormModal from '@/components/dashboard/ProductModal.vue'
import BaseButton from '@/components/common/BaseButton.vue'
import DeleteConfirmModal from '@/components/dashboard/DeleteConfirmModal.vue'
import { productService } from '@/services/product.service'
import type { Product, ProductFormData } from '@/types'

const columns = [
  { key: 'id', label: 'No' },
  { key: 'name', label: 'Nama' },
  { key: 'price', label: 'Harga' },
  { key: 'description', label: 'Deskripsi' },
  { key: 'image', label: 'Gambar' },
]

const loading = ref(false)
const products = ref<Product[]>([])
const searchQuery = ref('')
const isModalOpen = ref(false)
const isDeleteModalOpen = ref(false)
const isSubmitting = ref(false)
const selectedProduct = ref<Product | null>(null)
const currentPage = ref(1)
const perPage = ref(4)

const filteredProducts = computed(() => {
  if (!searchQuery.value) return products.value
  const query = searchQuery.value.toLowerCase()
  return products.value.filter(p => p.name.toLowerCase().includes(query))
})

const totalPages = computed(() =>
  Math.max(1, Math.ceil(filteredProducts.value.length / perPage.value))
)

const paginatedProducts = computed(() => {
  const start = (currentPage.value - 1) * perPage.value
  return filteredProducts.value.slice(start, start + perPage.value)
})

watch(searchQuery, () => { currentPage.value = 1 })

const onPageChange = (page: number) => { currentPage.value = page }
const formatPrice = (price: number) => new Intl.NumberFormat('id-ID').format(price)

const openAddModal = () => { selectedProduct.value = null; isModalOpen.value = true }
const openEditModal = (product: Product) => { selectedProduct.value = product; isModalOpen.value = true }
const openDeleteModal = (product: Product) => { selectedProduct.value = product; isDeleteModalOpen.value = true }

const handleSubmit = async (formData: ProductFormData) => {
  isSubmitting.value = true
  try {
    if (selectedProduct.value) {
      await productService.update(selectedProduct.value.id, formData)
    } else {
      await productService.create(formData)
    }
    isModalOpen.value = false
    await fetchProducts()
  } catch (err: any) {
    console.error('Product save error:', err)
    alert(err.response?.data?.message || 'Gagal menyimpan produk')
  } finally {
    isSubmitting.value = false
  }
}

const handleDelete = async () => {
  if (!selectedProduct.value) return
  isSubmitting.value = true
  try {
    await productService.delete(selectedProduct.value.id)
    isDeleteModalOpen.value = false
    await fetchProducts()
  } catch (err: any) {
    console.error('Product delete error:', err)
    alert(err.response?.data?.message || 'Gagal menghapus produk')
  } finally {
    isSubmitting.value = false
  }
}

const fetchProducts = async () => {
  loading.value = true
  try {
    const response = await productService.getAll({ per_page: 100 })
    products.value = response.data
  } catch (err) {
    console.error('Fetch products error:', err)
  } finally {
    loading.value = false
  }
}

onMounted(() => { fetchProducts() })
</script>