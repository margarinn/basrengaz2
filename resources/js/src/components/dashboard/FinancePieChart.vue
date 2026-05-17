<template>
  <div class="h-64 flex flex-col justify-center">
    <PieChart
      v-if="hasData"
      :data="chartData"
      :options="chartOptions"
    />
    <div v-else class="h-full flex items-center justify-center">
      <p class="text-gray-500 text-sm">Tidak ada data keuangan untuk periode ini</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import {
  Chart as ChartJS,
  ArcElement,
  Tooltip,
  Legend
} from 'chart.js'
import { Pie as PieChart } from 'vue-chartjs'

ChartJS.register(ArcElement, Tooltip, Legend)

interface Props {
  income: number
  expense: number
}

const props = defineProps<Props>()

const hasData = computed(() => props.income > 0 || props.expense > 0)

const chartData = computed(() => ({
  labels: ['Pendapatan', 'Pengeluaran'],
  datasets: [
    {
      data: [props.income, props.expense],
      backgroundColor: ['#10B981', '#EF4444'], // emerald-500, red-500
      hoverBackgroundColor: ['#059669', '#DC2626'], // emerald-600, red-600
      borderWidth: 0,
      hoverOffset: 4
    }
  ]
}))

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      position: 'bottom' as const,
      labels: {
        usePointStyle: true,
        padding: 20,
        font: {
          size: 12
        }
      }
    },
    tooltip: {
      backgroundColor: '#1f2937',
      padding: 12,
      cornerRadius: 8,
      callbacks: {
        label: (context: any) => {
          const value = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
          }).format(context.raw)
          return ` ${context.label}: ${value}`
        }
      }
    }
  }
}
</script>
