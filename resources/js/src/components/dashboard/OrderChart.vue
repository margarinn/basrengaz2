<template>
  <div class="h-80">
    <LineChart
      v-if="chartData.labels.length > 0"
      :data="chartData"
      :options="chartOptions"
    />
    <div v-else class="h-full flex items-center justify-center">
      <p class="text-gray-500">Tidak ada data untuk ditampilkan</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
  Filler
} from 'chart.js'
import { Line as LineChart } from 'vue-chartjs'
import type { OrderStat } from '@/types'

ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, Title, Tooltip, Legend, Filler)

interface Props {
  data: OrderStat[]
}

const props = defineProps<Props>()

const chartData = computed(() => ({
  labels: props.data.map(item => {
    const date = new Date(item.date)
    return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' })
  }),
  datasets: [
    {
      label: 'Pesanan',
      data: props.data.map(item => item.orders),
      borderColor: '#E53935',
      backgroundColor: 'rgba(229, 57, 53, 0.1)',
      borderWidth: 2,
      pointBackgroundColor: '#E53935',
      pointBorderColor: '#fff',
      pointHoverBackgroundColor: '#fff',
      pointHoverBorderColor: '#E53935',
      fill: true,
      tension: 0.4
    }
  ]
}))

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      display: false
    },
    tooltip: {
      backgroundColor: '#1f2937',
      padding: 12,
      cornerRadius: 8,
      callbacks: {
        label: (context: any) => `${context.parsed.y} pesanan`
      }
    }
  },
  scales: {
    x: {
      grid: {
        display: false
      },
      ticks: {
        font: {
          size: 11
        }
      }
    },
    y: {
      beginAtZero: true,
      grid: {
        color: '#f3f4f6'
      },
      ticks: {
        font: {
          size: 11
        }
      }
    }
  }
}
</script>
