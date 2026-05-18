import axios, { type AxiosInstance, type AxiosResponse, type AxiosError } from 'axios'

// Create axios instance for same-origin API calls with Sanctum cookie auth
const api: AxiosInstance = axios.create({
  baseURL: '/api',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
  },
  withCredentials: true,
  withXSRFToken: true,
  timeout: 10000
})

// Response interceptor
api.interceptors.response.use(
  (response: AxiosResponse) => response,
  (error: AxiosError) => {
    const currentPath = window.location.pathname

    // If 401, redirect to login, UNLESS it was just the initial profile check
    if (error.response?.status === 401) {
      const isProfileCheck = error.config?.url === '/profile'
      if (!isProfileCheck && currentPath !== '/login' && currentPath !== '/register') {
        window.location.href = '/login'
      }
    }

    // If 403, redirect to home page instead of looping in dashboard
    if (error.response?.status === 403) {
      if (currentPath.startsWith('/dashboard')) {
        window.location.href = '/'
      }
    }

    return Promise.reject(error)
  }
)

export default api
