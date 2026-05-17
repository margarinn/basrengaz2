import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { dashboardService } from '@/services/dashboard.service'
import type { DashboardStats, OrderStat, TopProduct, FinanceOverviewStat } from '@/types'

export const useDashboardStore = defineStore('dashboard', () => {
  // State
  const stats = ref<DashboardStats>({
    grossRevenue: 0,
    netRevenue: 0,
    reviewCount: 0,
    userCount: 0
  })
  const orderStats = ref<OrderStat[]>([])
  const topProducts = ref<TopProduct[]>([])
  const financeOverview = ref<FinanceOverviewStat>({
    income: 0,
    expense: 0,
    period: 'month'
  })
  const loading = ref(false)
  const error = ref<string | null>(null)

  // Getters
  const formattedGrossRevenue = computed(() => {
    return new Intl.NumberFormat('id-ID', {
      style: 'currency',
      currency: 'IDR',
      minimumFractionDigits: 0,
      maximumFractionDigits: 0
    }).format(stats.value.grossRevenue)
  })

  const formattedNetRevenue = computed(() => {
    return new Intl.NumberFormat('id-ID', {
      style: 'currency',
      currency: 'IDR',
      minimumFractionDigits: 0,
      maximumFractionDigits: 0
    }).format(stats.value.netRevenue)
  })

  const formattedUserCount = computed(() => {
    return new Intl.NumberFormat('id-ID').format(stats.value.userCount)
  })

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

  async function fetchFinanceOverview(period: 'day' | 'week' | 'month' | 'year' = 'month') {
    loading.value = true
    error.value = null
    
    try {
      const response = await dashboardService.getFinanceOverview(period)
      financeOverview.value = response
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Gagal memuat ringkasan keuangan'
    } finally {
      loading.value = false
    }
  }

  async function fetchAllDashboardData() {
    await Promise.all([
      fetchStats(),
      fetchOrderStats(),
      fetchTopProducts(),
      fetchFinanceOverview()
    ])
  }

  function clearError() {
    error.value = null
  }

  return {
    stats,
    orderStats,
    topProducts,
    financeOverview,
    loading,
    error,
    formattedGrossRevenue,
    formattedNetRevenue,
    formattedUserCount,
    fetchStats,
    fetchOrderStats,
    fetchTopProducts,
    fetchFinanceOverview,
    fetchAllDashboardData,
    clearError
  }
})
