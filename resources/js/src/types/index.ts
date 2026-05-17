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
  description: string
  price: number
  formatted_price: string
  image: string
  average_rating: number
  rating_count: number
  is_active: boolean
  created_at?: string
  updated_at?: string
}

export interface ProductFormData {
  name: string
  price: number
  description: string
  image: string | File
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
  revenue: number | string
  expenses: number | string
  description: string
}

// Dashboard Stats Types
export interface DashboardStats {
  grossRevenue: number
  netRevenue: number
  reviewCount: number
  userCount: number
}

export interface OrderStat {
  date: string
  orders: number
}

export interface TopProduct {
  name: string
  orders: number
}

export interface FinanceOverviewStat {
  income: number
  expense: number
  period: string
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
