<template>
  <BaseModal
    v-model="isOpen"
    :title="isEdit ? 'Edit Rating' : 'Tambah Rating'"
    size="lg"
    @close="handleClose"
  >
    <form @submit.prevent="handleSubmit" class="space-y-4">
      <!-- Nama -->
      <BaseInput
        v-model="form.name"
        label="Nama"
        placeholder="Masukkan nama"
        required
        :error="errors.name"
      />

      <!-- Rating -->
      <BaseInput
        v-model.number="form.rating"
        label="Rating"
        type="number"
        placeholder="Masukkan rating (1-5)"
        required
        :error="errors.rating"
      />

      <!-- Ulasan -->
      <BaseTextarea
        v-model="form.review"
        label="Ulasan"
        placeholder="Masukkan ulasan"
        required
        :rows="3"
        :error="errors.review"
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
          <span v-else>{{ isEdit ? 'Simpan Perubahan' : 'Tambah Rating' }}</span>
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
import type { Rating, RatingFormData } from '@/types'

interface Props {
  modelValue: boolean
  rating?: Rating | null
  loading?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  loading: false
})

const emit = defineEmits<{
  'update:modelValue': [value: boolean]
  submit: [data: RatingFormData]
}>()

// ─── State ─────────────────────────────────────────────────────────
const errors = ref<Record<string, string>>({})

const isOpen = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
})

const isEdit = computed(() => !!props.rating)

const form = ref<RatingFormData>({
  name: '',
  rating: 0,
  review: '',
})

// ─── Reset sebelum watch ───────────────────────────────────────────
const resetForm = () => {
  form.value = { name: '', rating: 0, review: '' }
  errors.value = {}
}

// ─── Sync form saat rating prop berubah ───────────────────────────
watch(() => props.rating, (newRating) => {
  if (newRating) {
    form.value = {
      name: newRating.name,
      rating: newRating.rating,
      review: newRating.review,
    }
  } else {
    resetForm()
  }
}, { immediate: true })

// ─── Validasi ──────────────────────────────────────────────────────
const validateForm = (): boolean => {
  errors.value = {}
  if (!form.value.name.trim()) errors.value.name = 'Nama wajib diisi'
  if (!form.value.rating || form.value.rating < 1 || form.value.rating > 5)
    errors.value.rating = 'Rating harus antara 1 sampai 5'
  if (!form.value.review.trim()) errors.value.review = 'Ulasan wajib diisi'
  return Object.keys(errors.value).length === 0
}

// ─── Actions ───────────────────────────────────────────────────────
const handleSubmit = () => {
  if (!validateForm()) return
  emit('submit', { ...form.value, rating: Number(form.value.rating) })
}

const handleClose = () => {
  isOpen.value = false
  resetForm()
}
</script>