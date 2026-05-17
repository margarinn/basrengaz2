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
          title="Pendapatan Kotor"
          :value="dashboardStore.formattedGrossRevenue"
          :icon="ShoppingBag"
          icon-color="primary"
        />
        <StatCard
          title="Pendapatan"
          :value="dashboardStore.formattedNetRevenue"
          :icon="DollarSign"
          icon-color="secondary"
        />
        <StatCard
          title="Ulasan"
          :value="String(dashboardStore.stats.reviewCount)"
          :icon="MessageSquare"
          icon-color="warning"
        />
        <StatCard
          title="Total Pengguna"
          :value="dashboardStore.formattedUserCount"
          :icon="Users"
          icon-color="success"
        />
      </div>

      <!-- Charts & Lists -->
      <div class="grid lg:grid-cols-3 gap-6">
        <!-- Top Products -->
        <div class="lg:col-span-3 card">
          <div class="p-6 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-900">Produk</h2>
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
                <span class="font-semibold text-gray-900">Rp {{ new Intl.NumberFormat('id-ID').format(product.orders) }}</span>
              </div>
            </div>
            <router-link
              to="/dashboard/produk"
              class="block w-full mt-6 py-3 text-center border border-primary text-primary rounded-lg hover:bg-primary hover:text-white transition-colors"
            >
              Lihat Produk Lengkap
            </router-link>
          </div>
        </div>
      </div>

      <!-- Finance Overview -->
      <div class="card mt-6">
        <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
          <h2 class="text-lg font-semibold text-gray-900">Ringkasan Keuangan</h2>
          <div class="flex flex-wrap gap-2">
            <button
              v-for="period in periods"
              :key="period.value"
              @click="setFinancePeriod(period.value)"
              :class="[
                'px-4 py-2 text-sm font-medium rounded-lg transition-colors',
                currentFinancePeriod === period.value
                  ? 'bg-primary text-white shadow-sm'
                  : 'bg-gray-50 text-gray-600 hover:bg-gray-100'
              ]"
            >
              {{ period.label }}
            </button>
          </div>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
          <FinancePieChart
            :income="dashboardStore.financeOverview.income"
            :expense="dashboardStore.financeOverview.expense"
          />
          <FinanceBarChart
            :income="dashboardStore.financeOverview.income"
            :expense="dashboardStore.financeOverview.expense"
          />
        </div>
      </div>
    </template>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import {
  ShoppingBag,
  DollarSign,
  MessageSquare,
  Users,
  Calendar
} from 'lucide-vue-next'
import { useDashboardStore, useAuthStore } from '@/stores'
import StatCard from '@/components/dashboard/StatCard.vue'
import FinancePieChart from '@/components/dashboard/FinancePieChart.vue'
import FinanceBarChart from '@/components/dashboard/FinanceBarChart.vue'
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

const periods = [
  { label: 'Minggu Ini', value: 'week' },
  { label: 'Bulan Ini', value: 'month' },
  { label: 'Tahun Ini', value: 'year' }
] as const

type Period = typeof periods[number]['value']
const currentFinancePeriod = ref<Period>('month')

const setFinancePeriod = async (period: Period) => {
  currentFinancePeriod.value = period
  await dashboardStore.fetchFinanceOverview(period)
}

onMounted(() => {
  dashboardStore.fetchAllDashboardData()
})
</script>
