<template>
  <div class="overflow-x-auto">
    <table class="w-full">
      <thead class="bg-gray-100">
        <tr class="border-b border-gray-300">
          <th
            v-for="column in columns"
            :key="column.key"
            :class="[
              'py-3 px-4 text-left text-sm font-medium text-gray-800',
              column.sortable && 'cursor-pointer hover:text-gray-700'
            ]"
            @click="column.sortable && handleSort(column.key)"
          >
            <div class="flex items-center gap-1">
              {{ column.label }}
              <template v-if="column.sortable">
                <ArrowUp v-if="sortKey === column.key && sortOrder === 'asc'" class="w-4 h-4" />
                <ArrowDown v-else-if="sortKey === column.key && sortOrder === 'desc'" class="w-4 h-4" />
                <ArrowUpDown v-else class="w-4 h-4 text-gray-300" />
              </template>
            </div>
          </th>
          <th v-if="$slots.actions" class="py-3 px-4 text-center text-sm font-medium text-gray-800">
            Aksi
          </th>
        </tr>
      </thead>
      <tbody>
        <tr v-if="loading">
          <td :colspan="columns.length + ($slots.actions ? 1 : 0)" class="py-12">
            <LoadingSpinner centered text="Memuat data..." />
          </td>
        </tr>
        <tr v-else-if="data.length === 0">
          <td :colspan="columns.length + ($slots.actions ? 1 : 0)" class="py-12">
            <EmptyState
              :title="emptyTitle"
              :description="emptyDescription"
              :icon="emptyIcon"
            />
          </td>
        </tr>
        <tr
          v-else
          v-for="(row, index) in data"
          :key="getRowKey(row, index)"
          class="border-b border-gray-200 hover:bg-gray-50 transition-colors"
        >
          <td
            v-for="column in columns"
            :key="column.key"
            class="py-4 px-4 text-sm text-gray-700"
          >
            <slot
              :name="`cell-${column.key}`"
              :row="row"
              :value="getNestedValue(row, column.key)"
            >
              {{ formatValue(getNestedValue(row, column.key), column.format) }}
            </slot>
          </td>
          <td v-if="$slots.actions" class="py-4 px-4 text-center items-center">
            <slot name="actions" :row="row" :index="index" />
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  <!-- Pagination -->
  <div v-if="showPagination && data.length > 0" class="flex items-center justify-end px-4 py-4 bg-gray-100 border-t border-gray-200">
    <div class="flex items-center gap-2">
      <span class="text-sm text-gray-500">Halaman:</span>
      <select
        :value="currentPage"
        @change="$emit('page-change', Number(($event.target as HTMLSelectElement).value))"
        class="border border-gray-300 rounded-lg px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"
      >
        <option v-for="page in totalPages" :key="page" :value="page">
          {{ page }}
        </option>
      </select>
      <span class="text-sm text-gray-500">
        {{ paginationInfo }}
      </span>
    </div>
    <div class="flex gap-2 pl-8">
      <button
        :disabled="currentPage === 1"
        @click="$emit('page-change', currentPage - 1)"
        class="p-2 rounded-lg border border-gray-300 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
      >
        <ChevronLeft class="w-4 h-4" />
      </button>
      <button
        :disabled="currentPage === totalPages"
        @click="$emit('page-change', currentPage + 1)"
        class="p-2 rounded-lg border border-gray-300 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
      >
        <ChevronRight class="w-4 h-4" />
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import {
  ArrowUp,
  ArrowDown,
  ArrowUpDown,
  ChevronLeft,
  ChevronRight,
  Package
} from 'lucide-vue-next'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import EmptyState from '@/components/common/EmptyState.vue'
import type { Component } from 'vue'

interface Column {
  key: string
  label: string
  sortable?: boolean
  format?: 'currency' | 'date' | 'text'
}

interface Props {
  columns: Column[]
  data: any[]
  loading?: boolean
  rowKey?: string
  emptyTitle?: string
  emptyDescription?: string
  emptyIcon?: Component
  showPagination?: boolean
  currentPage?: number
  totalPages?: number
  perPage?: number
  totalItems?: number
  sortKey?: string
  sortOrder?: 'asc' | 'desc'
}

const props = withDefaults(defineProps<Props>(), {
  loading: false,
  emptyTitle: 'Tidak ada data',
  emptyDescription: 'Data tidak ditemukan',
  emptyIcon: () => Package,
  showPagination: true,
  currentPage: 1,
  totalPages: 1,
  perPage: 10,
  totalItems: 0,
  sortOrder: 'asc'
})

const emit = defineEmits<{
  'page-change': [page: number]
  'sort': [key: string, order: 'asc' | 'desc']
}>()

const paginationInfo = computed(() => {
  const start = (props.currentPage - 1) * props.perPage + 1
  const end = Math.min(props.currentPage * props.perPage, props.totalItems)
  return `${start} - ${end} dari ${props.totalItems}`
})

const getRowKey = (row: any, index: number): string => {
  return props.rowKey ? row[props.rowKey] : `row-${index}`
}

const getNestedValue = (obj: any, path: string): any => {
  return path.split('.').reduce((acc, part) => acc?.[part], obj)
}

const formatValue = (value: any, format?: string): string => {
  if (value == null) return '-'
  
  switch (format) {
    case 'currency':
      return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
      }).format(value)
    case 'date':
      return new Date(value).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
      })
    default:
      return String(value)
  }
}

const handleSort = (key: string) => {
  const newOrder = props.sortKey === key && props.sortOrder === 'asc' ? 'desc' : 'asc'
  emit('sort', key, newOrder)
}
</script>
