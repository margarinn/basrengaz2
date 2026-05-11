import api from './api'
import type { Finance, ApiResponse, PaginationParams } from '@/types'

export const financeService = {
  async getAll(params?: PaginationParams): Promise<ApiResponse<Finance[]>> {
    const response = await api.get<ApiResponse<Finance[]>>('/keuangan', { params })
    return response.data
  },

  async create(data: any): Promise<Finance> {
    const response = await api.post<ApiResponse<Finance>>('/keuangan', data)
    return response.data.data
  },

  async update(id: number, data: any): Promise<Finance> {
    const response = await api.put<ApiResponse<Finance>>(`/keuangan/${id}`, data)
    return response.data.data
  },

  async delete(id: number): Promise<void> {
    await api.delete(`/keuangan/${id}`)
  }
}
