import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { authService } from '@/services/auth.service'
import type { User, LoginCredentials, RegisterPayload, ForgotPasswordPayload } from '@/types'

export const useAuthStore = defineStore('auth', () => {
  // State
  const user = ref<User | null>(null)
  const loading = ref(false)
  const error = ref<string | null>(null)
  const initialized = ref(false)

  // Getters
  const isAuthenticated = computed(() => !!user.value)
  const isAdmin = computed(() => user.value?.role === 'admin')
  const userName = computed(() => user.value?.name || '')
  const userAvatar = computed(() => user.value?.avatar || '')
  const token = computed(() => isAuthenticated.value ? 'session' : '')

  // Actions
  async function register(payload: RegisterPayload) {
    loading.value = true
    error.value = null

    try {
      const response = await authService.register(payload)
      user.value = response.data?.user || null
      return true
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Registrasi gagal. Silakan coba lagi.'
      return false
    } finally {
      loading.value = false
    }
  }

  async function login(credentials: LoginCredentials) {
    loading.value = true
    error.value = null

    try {
      const response = await authService.login(credentials)
      user.value = response.user
      return true
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Login gagal. Silakan coba lagi.'
      return false
    } finally {
      loading.value = false
    }
  }

  async function logout() {
    try {
      await authService.logout()
    } catch (err) {
      console.error('Logout error:', err)
    } finally {
      user.value = null
    }
  }

  async function fetchProfile() {
    try {
      const response = await authService.getProfile()
      user.value = response
    } catch (err) {
      user.value = null
    } finally {
      initialized.value = true
    }
  }

  function initializeAuth() {
    if (!initialized.value) {
      return fetchProfile()
    }
    return Promise.resolve()
  }

  async function updateProfile(data: Partial<User>) {
    loading.value = true
    try {
      const updatedUser = await authService.updateProfile(data)
      user.value = updatedUser
      return true
    } catch (err) {
      console.error('Update profile error:', err)
      return false
    } finally {
      loading.value = false
    }
  }

  async function forgotPassword(payload: ForgotPasswordPayload) {
    loading.value = true
    error.value = null
    try {
      await authService.forgotPassword(payload)
      return true
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Gagal mereset password.'
      return false
    } finally {
      loading.value = false
    }
  }

  return {
    user,
    token,
    loading,
    error,
    isAuthenticated,
    isAdmin,
    userName,
    userAvatar,
    register,
    login,
    logout,
    fetchProfile,
    initializeAuth,
    updateProfile,
    forgotPassword
  }
})
