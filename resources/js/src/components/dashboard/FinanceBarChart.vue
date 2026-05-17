<template>
  <div class="h-64 flex flex-col justify-center">
    <BarChart
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
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale
} from 'chart.js'
import { Bar as BarChart } from 'vue-chartjs'

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)

interface Props {
  income: number
  expense: number
}

const props = defineProps<Props>()

const hasData = computed(() => props.income > 0 || props.expense > 0)

const chartData = computed(() => ({
  labels: [''],
  datasets: [
    {
      label: 'Pendapatan',
      data: [props.income],
      backgroundColor: '#10B981', // emerald-500
      hoverBackgroundColor: '#059669', // emerald-600
      borderRadius: 4
    },
    {
      label: 'Pengeluaran',
      data: [props.expense],
      backgroundColor: '#f43f5e', // rose-500
      hoverBackgroundColor: '#e11d48', // rose-600
      borderRadius: 4
    }
  ]
}))

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  scales: {
    y: {
      beginAtZero: true,
      ticks: {
        callback: function(value: any) {
          if (value >= 1000000) {
            return 'Rp ' + (value / 1000000) + 'jt'
          }
          if (value >= 1000) {
            return 'Rp ' + (value / 1000) + 'k'
          }
          return 'Rp ' + value
        }
      }
    }
  },
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
          return ` ${context.dataset.label}: ${value}`
        }
      }
    }
  }
}
</script>
