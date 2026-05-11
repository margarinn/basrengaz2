<template>
  <div class="space-y-6">
    <!-- Page Title -->
    <div>
      <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
      <p class="text-gray-600">Selamat datang kembali, {{ userName }}!</p>
    </div>

    <!-- Loading State -->
    <div v-if="dashboardStore.loading" class="flex justify-center py-12">
      <LoadingSpinner size="lg" text="Memuat data dashboard..." />
    </div>

    <template v-else>
      <!-- Stats Cards -->
      <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <StatCard
          title="Total Penjualan"
          :value="dashboardStore.formattedTotalSales"
          :change="dashboardStore.stats.salesGrowth"
          :icon="ShoppingBag"
          icon-color="primary"
        />
        <StatCard
          title="Pendapatan"
          :value="dashboardStore.formattedRevenue"
          :icon="DollarSign"
          icon-color="secondary"
        />
        <StatCard
          title="Ulasan Baru"
          :value="String(dashboardStore.stats.newReviews)"
          :icon="MessageSquare"
          icon-color="warning"
        />
        <StatCard
          title="Total Pembeli"
          :value="dashboardStore.formattedTotalCustomers"
          :icon="Users"
          icon-color="success"
        />
      </div>

      <!-- Charts & Lists -->
      <div class="grid lg:grid-cols-3 gap-6">
        <!-- Order Chart -->
        <div class="lg:col-span-2 card">
          <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">Statistik Pemesanan</h2>
            <div class="flex items-center gap-2 text-sm text-gray-500">
              <Calendar class="w-4 h-4" />
              {{ currentDate }}
            </div>
          </div>
          <div class="p-6">
            <OrderChart :data="dashboardStore.orderStats" />
          </div>
        </div>

        <!-- Top Products -->
        <div class="card">
          <div class="p-6 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-900">Pesanan Terlaris</h2>
          </div>
          <div class="p-6">
            <div v-if="dashboardStore.topProducts.length === 0" class="text-center py-8">
              <p class="text-gray-500">Belum ada data</p>
            </div>
            <div v-else class="space-y-4">
              <div
                v-for="(product, index) in dashboardStore.topProducts"
                :key="product.name"
                class="flex items-center justify-between"
              >
                <div class="flex items-center gap-3">
                  <span
                    :class="[
                      'w-6 h-6 rounded-full flex items-center justify-center text-xs font-medium',
                      index < 3 ? 'bg-primary text-white' : 'bg-gray-100 text-gray-600'
                    ]"
                  >
                    {{ index + 1 }}
                  </span>
                  <span class="text-gray-700">{{ product.name }}</span>
                </div>
                <span class="font-semibold text-gray-900">{{ product.orders }}</span>
              </div>
            </div>
            <router-link
              to="/dashboard/products"
              class="block w-full mt-6 py-3 text-center border border-primary text-primary rounded-lg hover:bg-primary hover:text-white transition-colors"
            >
              Lihat Laporan Lengkap
            </router-link>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted } from 'vue'
import {
  ShoppingBag,
  DollarSign,
  MessageSquare,
  Users,
  Calendar
} from 'lucide-vue-next'
import { useDashboardStore, useAuthStore } from '@/stores'
import StatCard from '@/components/dashboard/StatCard.vue'
import OrderChart from '@/components/dashboard/OrderChart.vue'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'

const dashboardStore = useDashboardStore()
const authStore = useAuthStore()

const userName = computed(() => authStore.userName || 'Admin')

const currentDate = computed(() => {
  return new Date().toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  })
})

onMounted(() => {
  dashboardStore.fetchAllDashboardData()
})
</script>
