<template>
  <BaseModal
    v-model="isOpen"
    :title="isEdit ? 'Edit Produk' : 'Tambah Produk'"
    size="lg"
    @close="handleClose"
  >
    <form @submit.prevent="handleSubmit" class="space-y-4">
      <!-- Nama Produk -->
      <BaseInput
        v-model="form.name"
        label="Nama Produk"
        placeholder="Masukkan nama produk"
        required
        :error="errors.name"
      />

      <!-- Harga -->
      <BaseInput
        v-model.number="form.price"
        label="Harga"
        type="number"
        placeholder="Masukkan harga"
        required
        suffix="IDR"
        :error="errors.price"
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

      <!-- Upload Gambar -->
      <div>
        <label class="block text-sm font-medium text-gray-700">Gambar Produk</label>
        <div
          @click="triggerFileInput"
          @dragover.prevent
          @drop.prevent="onDrop"
          class="mt-1 border-2 border-dashed border-gray-300 rounded-lg p-4 text-center cursor-pointer hover:border-red-400 transition-colors"
        >
          <img
            v-if="previewImage"
            :src="previewImage"
            class="w-24 h-24 object-cover rounded-lg mx-auto mb-2"
          />
          <div v-else class="text-gray-400">
            <svg class="w-10 h-10 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <p class="text-sm">Klik atau drag & drop gambar di sini</p>
            <p class="text-xs text-gray-400 mt-1">PNG, JPG, JPEG (maks. 2MB)</p>
          </div>
          <p v-if="previewImage" class="text-xs text-gray-500 mt-1">Klik untuk ganti gambar</p>
        </div>
        <input
          ref="fileInput"
          type="file"
          accept="image/png,image/jpg,image/jpeg"
          class="hidden"
          @change="handleFileChange"
        />
      </div>
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
          <span v-else>{{ isEdit ? 'Simpan Perubahan' : 'Tambah Produk' }}</span>
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
import type { Product, ProductFormData } from '@/types'

interface Props {
  modelValue: boolean
  product?: Product | null
  loading?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  loading: false
})

const emit = defineEmits<{
  'update:modelValue': [value: boolean]
  submit: [data: ProductFormData]
}>()

// ─── State ─────────────────────────────────────────────────────────
const fileInput = ref<HTMLInputElement | null>(null)
const previewImage = ref<string>('')
const errors = ref<Record<string, string>>({})

const isOpen = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
})

const isEdit = computed(() => !!props.product)

const form = ref<ProductFormData>({
  name: '',
  price: 0,
  description: '',
  image: '',
})

const resetForm = () => {
  form.value = { name: '', price: 0, description: '', image: '' } // ← null → ''
  previewImage.value = ''
  errors.value = {}
}

// ─── Sync form saat product prop berubah ───────────────────────────
watch(() => props.product, (newProduct) => {
  if (newProduct) {
    form.value = {
      name: newProduct.name,
      price: newProduct.price,
      description: newProduct.description,
      image: newProduct.image, // ← langsung pakai string, tidak dikosongkan
    }
    previewImage.value = newProduct.image
  } else {
    resetForm()
  }
}, { immediate: true })

// ─── Upload gambar ─────────────────────────────────────────────────
const triggerFileInput = () => fileInput.value?.click()

const handleFileChange = (e: Event) => {
  const file = (e.target as HTMLInputElement).files?.[0]
  if (file) loadImage(file)
}

const onDrop = (e: DragEvent) => {
  const file = e.dataTransfer?.files?.[0]
  if (file) loadImage(file)
}

const loadImage = (file: File) => {
  const reader = new FileReader()
  reader.onload = (e) => {
    const base64 = e.target?.result as string
    form.value.image = base64  // ← simpan sebagai base64 string, bukan File
    previewImage.value = base64
  }
  reader.readAsDataURL(file)
}

// ─── Validasi ──────────────────────────────────────────────────────
const validateForm = (): boolean => {
  errors.value = {}
  if (!form.value.name.trim()) errors.value.name = 'Nama produk wajib diisi'
  if (!form.value.price || form.value.price <= 0) errors.value.price = 'Harga harus lebih dari 0'
  if (!form.value.description.trim()) errors.value.description = 'Deskripsi wajib diisi'
  return Object.keys(errors.value).length === 0
}

// ─── Actions ───────────────────────────────────────────────────────
const handleSubmit = () => {
  if (!validateForm()) return
  emit('submit', { ...form.value, price: Number(form.value.price) })
}

const handleClose = () => {
  isOpen.value = false
  resetForm()
}

</script>