import api from './api'
import type { Product, ProductFormData, ApiResponse, PaginationParams } from '@/types'

export const productService = {
  async getAll(params?: PaginationParams): Promise<ApiResponse<Product[]>> {
    const response = await api.get<ApiResponse<Product[]>>('/products', { params })
    return response.data
  },

  async getById(id: number): Promise<Product> {
    const response = await api.get<ApiResponse<Product>>(`/products/${id}`)
    return response.data.data
  },

  async create(data: ProductFormData): Promise<Product> {
    const formData = new FormData()
    formData.append('name', data.name)
    formData.append('price', data.price.toString())
    formData.append('description', data.description)
    formData.append('type', data.type)
    if (data.image instanceof File) {
      formData.append('image', data.image)
    }
    if (data.category) {
      formData.append('category', data.category)
    }
    if (data.stock) {
      formData.append('stock', data.stock.toString())
    }

    const response = await api.post<ApiResponse<Product>>('/products', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })
    return response.data.data
  },

  async update(id: number, data: ProductFormData): Promise<Product> {
    const formData = new FormData()
    formData.append('name', data.name)
    formData.append('price', data.price.toString())
    formData.append('description', data.description)
    formData.append('type', data.type)
    if (data.image instanceof File) {
      formData.append('image', data.image)
    }
    if (data.category) {
      formData.append('category', data.category)
    }
    if (data.stock) {
      formData.append('stock', data.stock.toString())
    }
    formData.append('_method', 'PUT')

    const response = await api.post<ApiResponse<Product>>(`/products/${id}`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })
    return response.data.data
  },

  async delete(id: number): Promise<void> {
    await api.delete(`/products/${id}`)
  }
}
