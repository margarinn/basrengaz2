import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { dashboardService } from '@/services/dashboard.service'
import type { DashboardStats, OrderStat, TopProduct } from '@/types'

export const useDashboardStore = defineStore('dashboard', () => {
  // State
  const stats = ref<DashboardStats>({
    totalSales: 0,
    salesGrowth: 0,
    revenue: 0,
    newReviews: 0,
    totalCustomers: 0
  })
  const orderStats = ref<OrderStat[]>([])
  const topProducts = ref<TopProduct[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)

  // Getters
  const formattedRevenue = computed(() => {
    return new Intl.NumberFormat('id-ID', {
      style: 'currency',
      currency: 'IDR',
      minimumFractionDigits: 0,
      maximumFractionDigits: 0
    }).format(stats.value.revenue)
  })

  const formattedTotalSales = computed(() => {
    return new Intl.NumberFormat('id-ID').format(stats.value.totalSales)
  })

  const formattedTotalCustomers = computed(() => {
    return new Intl.NumberFormat('id-ID').format(stats.value.totalCustomers)
  })

  const salesGrowthPositive = computed(() => stats.value.salesGrowth >= 0)

  // Actions
  async function fetchStats() {
    loading.value = true
    error.value = null
    
    try {
      const response = await dashboardService.getStats()
      stats.value = response
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Gagal memuat statistik'
    } finally {
      loading.value = false
    }
  }

  async function fetchOrderStats(period: 'week' | 'month' | 'year' = 'month') {
    loading.value = true
    error.value = null
    
    try {
      const response = await dashboardService.getOrderStats(period)
      orderStats.value = response
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Gagal memuat statistik pesanan'
    } finally {
      loading.value = false
    }
  }

  async function fetchTopProducts(limit: number = 5) {
    loading.value = true
    error.value = null
    
    try {
      const response = await dashboardService.getTopProducts(limit)
      topProducts.value = response
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Gagal memuat produk terlaris'
    } finally {
      loading.value = false
    }
  }

  async function fetchAllDashboardData() {
    await Promise.all([
      fetchStats(),
      fetchOrderStats(),
      fetchTopProducts()
    ])
  }

  function clearError() {
    error.value = null
  }

  return {
    stats,
    orderStats,
    topProducts,
    loading,
    error,
    formattedRevenue,
    formattedTotalSales,
    formattedTotalCustomers,
    salesGrowthPositive,
    fetchStats,
    fetchOrderStats,
    fetchTopProducts,
    fetchAllDashboardData,
    clearError
  }
})
