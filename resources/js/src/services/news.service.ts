import api from './api'
import type { News, ApiResponse, PaginationParams } from '@/types'

export const newsService = {
  async getAll(params?: PaginationParams): Promise<ApiResponse<News[]>> {
    const response = await api.get<ApiResponse<News[]>>('/berita', { params })
    return response.data
  },

  async getById(id: number): Promise<News> {
    const response = await api.get<ApiResponse<News>>(`/berita/${id}`)
    return response.data.data
  },

  async create(data: FormData): Promise<News> {
    const response = await api.post<ApiResponse<News>>('/berita', data, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })
    return response.data.data
  },

  async update(id: number, data: FormData): Promise<News> {
    data.append('_method', 'PUT')
    const response = await api.post<ApiResponse<News>>(`/berita/${id}`, data, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })
    return response.data.data
  },

  async delete(id: number): Promise<void> {
    await api.delete(`/berita/${id}`)
  }
}
