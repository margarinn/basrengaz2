import api from './api'
import type { DashboardStats, OrderStat, TopProduct, ApiResponse } from '@/types'

export const dashboardService = {
  async getStats(): Promise<DashboardStats> {
    const response = await api.get<ApiResponse<DashboardStats>>('/dashboard/stats')
    return response.data.data
  },

  async getOrderStats(period: 'week' | 'month' | 'year' = 'month'): Promise<OrderStat[]> {
    const response = await api.get<ApiResponse<OrderStat[]>>('/dashboard/order-stats', {
      params: { period }
    })
    return response.data.data
  },

  async getTopProducts(limit: number = 5): Promise<TopProduct[]> {
    const response = await api.get<ApiResponse<TopProduct[]>>('/dashboard/top-products', {
      params: { limit }
    })
    return response.data.data
  }
}
