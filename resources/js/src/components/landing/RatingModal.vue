<template>
  <BaseModal
    v-model="isOpen"
    :title="isEdit ? 'Edit Ulasan' : 'Tambah Ulasan'"
    size="lg"
    @close="handleClose"
  >
    <form @submit.prevent="handleSubmit" class="space-y-5">
      <!-- Nama -->
      <div>
        <label class="block text-sm font-semibold text-red-500 mb-1">Nama</label>
        <input
          v-model="form.name"
          type="text"
          class="w-full px-4 py-3 bg-gray-100 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-400 transition-all"
          :class="{ 'ring-2 ring-red-400': errors.name }"
        />
        <p v-if="errors.name" class="mt-1 text-xs text-red-500">{{ errors.name }}</p>
      </div>

      <!-- Rating -->
      <div>
        <label class="block text-sm font-semibold text-red-500 mb-1">Rating</label>
        <div class="flex items-center gap-1" @mouseleave="hoverRating = 0">
          <div 
            v-for="i in 5" 
            :key="i"
            class="relative cursor-pointer w-8 h-8"
            @mousemove="handleStarMove($event, i)"
            @click="handleStarClick($event, i)"
          >
            <!-- Background star (gray) -->
            <Star class="absolute inset-0 w-8 h-8 text-gray-300" />
            <!-- Foreground star (filled, yellow) -->
            <div 
              class="absolute inset-0 overflow-hidden"
              :style="{ width: getStarFillWidth(i) }"
            >
              <Star class="w-8 h-8 text-yellow-400 fill-current" />
            </div>
          </div>
          <span class="ml-2 text-sm font-medium text-gray-600">{{ hoverRating || form.rating }} / 5</span>
        </div>
        <p v-if="errors.rating" class="mt-1 text-xs text-red-500">{{ errors.rating }}</p>
      </div>

      <!-- Ulasan -->
      <div>
        <label class="block text-sm font-semibold text-red-500 mb-1">Ulasan</label>
        <textarea
          v-model="form.review"
          rows="3"
          class="w-full px-4 py-3 bg-gray-100 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-400 transition-all resize-none"
          :class="{ 'ring-2 ring-red-400': errors.review }"
        ></textarea>
        <p v-if="errors.review" class="mt-1 text-xs text-red-500">{{ errors.review }}</p>
      </div>
    </form>

    <template #footer>
      <div class="flex justify-end gap-2">
        <button
          @click="handleSubmit"
          :disabled="props.loading"
          class="px-6 py-2 text-sm font-semibold text-white bg-green-500 rounded-md hover:bg-green-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
        >
          <span v-if="props.loading">Menyimpan...</span>
          <span v-else>{{ isEdit ? 'Simpan' : 'Add' }}</span>
        </button>
        <button
          @click="emit('delete')"
          class="px-6 py-2 text-sm font-semibold text-white bg-red-500 rounded-md hover:bg-red-600 transition-colors"
        >
          Delete
        </button>
      </div>
    </template>
  </BaseModal>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import BaseModal from '@/components/common/BaseModal.vue'
import { Star } from 'lucide-vue-next'
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
  delete: []
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

const hoverRating = ref(0)

const getStarFillWidth = (starIndex: number) => {
  const currentVal = hoverRating.value || form.value.rating
  if (currentVal >= starIndex) return '100%'
  if (currentVal >= starIndex - 0.5) return '50%'
  return '0%'
}

const handleStarMove = (e: MouseEvent, starIndex: number) => {
  const rect = (e.currentTarget as HTMLElement).getBoundingClientRect()
  const isLeftHalf = (e.clientX - rect.left) < (rect.width / 2)
  hoverRating.value = isLeftHalf ? starIndex - 0.5 : starIndex
}

const handleStarClick = (e: MouseEvent, starIndex: number) => {
  const rect = (e.currentTarget as HTMLElement).getBoundingClientRect()
  const isLeftHalf = (e.clientX - rect.left) < (rect.width / 2)
  form.value.rating = isLeftHalf ? starIndex - 0.5 : starIndex
}

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
  emit('submit', { ...form.value })
}

const handleClose = () => {
  isOpen.value = false
  resetForm()
}
</script>