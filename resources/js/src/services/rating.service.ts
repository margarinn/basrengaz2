import api from './api'
import type { Rating, ApiResponse, PaginationParams } from '@/types'

export const ratingService = {
  async getAll(params?: PaginationParams): Promise<ApiResponse<Rating[]>> {
    const response = await api.get<ApiResponse<Rating[]>>('/rating', { params })
    return response.data
  },

  async create(data: any): Promise<Rating> {
    const response = await api.post<ApiResponse<Rating>>('/rating', data)
    return response.data.data
  },

  async update(id: number, data: any): Promise<Rating> {
    const response = await api.put<ApiResponse<Rating>>(`/rating/${id}`, data)
    return response.data.data
  },

  async delete(id: number): Promise<void> {
    await api.delete(`/rating/${id}`)
  }
}
