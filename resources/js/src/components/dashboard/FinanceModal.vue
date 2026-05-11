<template>
  <BaseModal
    v-model="isOpen"
    :title="isEdit ? 'Edit Keuangan' : 'Tambah Keuangan'"
    size="lg"
    @close="handleClose"
  >
    <form @submit.prevent="handleSubmit" class="space-y-4">
      <!-- Minggu -->
      <BaseInput
        v-model="form.week"
        label="Minggu"
        placeholder="Masukkan minggu berapa"
        required
        :error="errors.week"
      />

      <!-- Pemasukan -->
      <BaseInput
        v-model.number="form.revenue"
        label="Pemasukan"
        type="number"
        placeholder="Masukkan pemasukan"
        required
        suffix="IDR"
        :error="errors.revenue"
      />

      <!-- Pengeluaran -->
      <BaseInput
        v-model.number="form.expenses"
        label="Pengeluaran"
        type="number"
        placeholder="Masukkan pengeluaran"
        required
        suffix="IDR"
        :error="errors.expenses"
      />

      <!-- Deskripsi -->
      <BaseTextarea
        v-model="form.description"
        label="Deskripsi"
        placeholder="Masukkan deskripsi produk"
        required
        :rows="3"
        :error="errors.description"
      />
    </form>

    <template #footer>
      <div class="flex justify-end gap-3">
        <button
          @click="handleClose"
          class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200"
        >
          Batal
        </button>
        <button
          @click="handleSubmit"
          :disabled="props.loading"
          class="px-4 py-2 text-sm font-medium text-white bg-red-500 rounded-md hover:bg-red-600 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          <span v-if="props.loading">Menyimpan...</span>
          <span v-else>{{ isEdit ? 'Simpan Perubahan' : 'Tambah Keuangan' }}</span>
        </button>
      </div>
    </template>
  </BaseModal>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import BaseModal from '@/components/common/BaseModal.vue'
import BaseInput from '@/components/common/BaseInput.vue'
import BaseTextarea from '@/components/common/BaseTextarea.vue'
import type { Finance, FinanceFormData } from '@/types'

interface Props {
  modelValue: boolean
  finance?: Finance | null
  loading?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  loading: false
})

const emit = defineEmits<{
  'update:modelValue': [value: boolean]
  submit: [data: FinanceFormData]
}>()

// ─── State ─────────────────────────────────────────────────────────
const errors = ref<Record<string, string>>({})

const isOpen = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
})

const isEdit = computed(() => !!props.finance)

const form = ref<FinanceFormData>({
  week: '',
  revenue: 0,
  expenses: 0,
  description: ''
})

const resetForm = () => {
  form.value = { week: '', revenue: 0, expenses: 0, description: '' }
  errors.value = {}
}

// ─── Sync form saat finance prop berubah ───────────────────────────
watch(() => props.finance, (newFinance) => {
  if (newFinance) {
    form.value = {
      week: newFinance.week,
      revenue: newFinance.revenue,
      expenses: newFinance.expenses,
      description: newFinance.description
    }
  } else {
    resetForm()
  }
}, { immediate: true })

// ─── Validasi ──────────────────────────────────────────────────────
const validateForm = (): boolean => {
  errors.value = {}
  if (!form.value.week.trim()) errors.value.week = 'Minggu wajib diisi'
  if (!form.value.revenue || form.value.revenue < 0) errors.value.revenue = 'Pemasukan harus lebih dari atau sama dengan 0'
  if (!form.value.expenses || form.value.expenses < 0) errors.value.expenses = 'Pengeluaran harus lebih dari atau sama dengan 0'
  if (!form.value.description.trim()) errors.value.description = 'Deskripsi wajib diisi'
  return Object.keys(errors.value).length === 0
}

// ─── Actions ───────────────────────────────────────────────────────
const handleSubmit = () => {
  if (!validateForm()) return
  emit('submit', { ...form.value, revenue: Number(form.value.revenue), expenses: Number(form.value.expenses) })
}

const handleClose = () => {
  isOpen.value = false
  resetForm()
}

</script>