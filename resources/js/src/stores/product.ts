import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { productService } from '@/services/product.service'
import type { Product, ProductFormData, PaginationParams } from '@/types'

export const useProductStore = defineStore('product', () => {
  // State
  const products = ref<Product[]>([])
  const currentProduct = ref<Product | null>(null)
  const loading = ref(false)
  const error = ref<string | null>(null)
  const meta = ref({
    current_page: 1,
    last_page: 1,
    per_page: 10,
    total: 0
  })

  // Getters
  const productList = computed(() => products.value)
  const totalProducts = computed(() => meta.value.total)
  const currentPage = computed(() => meta.value.current_page)
  const lastPage = computed(() => meta.value.last_page)
  const hasMorePages = computed(() => meta.value.current_page < meta.value.last_page)

  // Actions
  async function fetchProducts(params?: PaginationParams) {
    loading.value = true
    error.value = null
    
    try {
      const response = await productService.getAll(params)
      products.value = response.data
      if (response.meta) {
        meta.value = response.meta
      }
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Gagal memuat produk'
    } finally {
      loading.value = false
    }
  }

  async function fetchProductById(id: number) {
    loading.value = true
    error.value = null
    
    try {
      const product = await productService.getById(id)
      currentProduct.value = product
      return product
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Gagal memuat produk'
      return null
    } finally {
      loading.value = false
    }
  }

  async function createProduct(data: ProductFormData) {
    loading.value = true
    error.value = null
    
    try {
      const product = await productService.create(data)
      products.value.unshift(product)
      meta.value.total++
      return product
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Gagal membuat produk'
      return null
    } finally {
      loading.value = false
    }
  }

  async function updateProduct(id: number, data: ProductFormData) {
    loading.value = true
    error.value = null
    
    try {
      const product = await productService.update(id, data)
      const index = products.value.findIndex(p => p.id === id)
      if (index !== -1) {
        products.value[index] = product
      }
      return product
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Gagal mengupdate produk'
      return null
    } finally {
      loading.value = false
    }
  }

  async function deleteProduct(id: number) {
    loading.value = true
    error.value = null
    
    try {
      await productService.delete(id)
      products.value = products.value.filter(p => p.id !== id)
      meta.value.total--
      return true
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Gagal menghapus produk'
      return false
    } finally {
      loading.value = false
    }
  }

  function setCurrentProduct(product: Product | null) {
    currentProduct.value = product
  }

  function clearError() {
    error.value = null
  }

  return {
    products,
    currentProduct,
    loading,
    error,
    meta,
    productList,
    totalProducts,
    currentPage,
    lastPage,
    hasMorePages,
    fetchProducts,
    fetchProductById,
    createProduct,
    updateProduct,
    deleteProduct,
    setCurrentProduct,
    clearError
  }
})
