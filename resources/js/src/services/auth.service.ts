import api from './api'
import axios from 'axios'
import type { RegisterPayload, LoginCredentials, User, ForgotPasswordPayload } from '@/types'

export const authService = {
  async register(payload: RegisterPayload) {
    await axios.get('/sanctum/csrf-cookie', { withCredentials: true })
    const response = await api.post('/register', payload)
    return response.data
  },

  async login(credentials: LoginCredentials): Promise<{ user: User }> {
    await axios.get('/sanctum/csrf-cookie', { withCredentials: true })
    const response = await api.post('/login', credentials)
    return response.data.data
  },

  async logout(): Promise<void> {
    await api.post('/logout')
  },

  async forgotPassword(payload: ForgotPasswordPayload): Promise<void> {
    await api.post('/forgot-password', payload)
  },

  async getProfile(): Promise<User> {
    const response = await api.get('/profile')
    return response.data.data
  },

  async updateProfile(data: Partial<User>): Promise<User> {
    const response = await api.put('/profile', data)
    return response.data.data
  }
}
