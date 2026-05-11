// User Types
export interface User {
  id: number
  name: string
  email: string
  avatar?: string
  role: 'admin' | 'user'
}

// Product Types
export interface Product {
  id: number
  name: string
  price: number
  description: string
  image: string
  created_at?: string
  updated_at?: string
}

export interface ProductFormData {
  name: string
  price: number
  description: string
  image: string
}

// News/Berita Types
export interface News {
  id: number
  title: string
  image: string
  description: string
  created_at?: string
  updated_at?: string
}

export interface NewsFormData {
  title: string
  image: string
  description: string
}

// Rating Types
export interface Rating {
  id: number
  name: string
  rating: number
  review: string
  avatar?: string
  created_at?: string
  updated_at?: string
}

export interface RatingFormData {
  name: string
  rating: number
  review: string
}

export interface Finance {
  id: number,
  week: string,
  revenue: number,
  expenses: number,
  description: string,
  created_at?: string,
  updated_at?: string
}

export interface FinanceFormData {
  week: string,
  revenue: number,
  expenses: number,
  description: string
}

// Dashboard Stats Types
export interface DashboardStats {
  totalSales: number
  salesGrowth: number
  revenue: number
  newReviews: number
  totalCustomers: number
}

export interface OrderStat {
  date: string
  orders: number
}

export interface TopProduct {
  name: string
  orders: number
}

// API Response Types
export interface ApiResponse<T> {
  success: boolean
  data: T
  message?: string
  meta?: {
    current_page: number
    last_page: number
    per_page: number
    total: number
  }
}

export interface RegisterPayload {
  username: string
  email: string
  password: string
}

export interface LoginCredentials {
  email: string
  password: string
}

export interface AuthResponse {
  user: User
  token: string
}

// Pagination Types
export interface PaginationParams {
  page?: number
  per_page?: number
  search?: string
  sort_by?: string
  sort_order?: 'asc' | 'desc'
}
