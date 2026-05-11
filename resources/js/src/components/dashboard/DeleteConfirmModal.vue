<template>
  <BaseModal
    v-model="isOpen"
    title="Konfirmasi Hapus"
    size="sm"
    @close="handleClose"
  >
    <div class="text-center py-4">
      <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
        <AlertTriangle class="w-8 h-8 text-red-500" />
      </div>
      <h3 class="text-lg font-semibold text-gray-900 mb-2">
        Apakah Anda yakin?
      </h3>
      <p class="text-gray-600">
        Data <strong>{{ itemName }}</strong> akan dihapus permanen. 
        Tindakan ini tidak dapat dibatalkan.
      </p>
    </div>

    <template #footer>
      <div class="flex justify-end gap-3">
        <BaseButton variant="ghost" @click="handleClose">
          Batal
        </BaseButton>
        <BaseButton
          variant="danger"
          :loading="loading"
          @click="handleConfirm"
        >
          Hapus
        </BaseButton>
      </div>
    </template>
  </BaseModal>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { AlertTriangle } from 'lucide-vue-next'
import BaseModal from '@/components/common/BaseModal.vue'
import BaseButton from '@/components/common/BaseButton.vue'

interface Props {
  modelValue: boolean
  itemName: string
  loading?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  loading: false
})

const emit = defineEmits<{
  'update:modelValue': [value: boolean]
  confirm: []
}>()

const isOpen = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
})

const handleConfirm = () => {
  emit('confirm')
}

const handleClose = () => {
  isOpen.value = false
}
</script>
