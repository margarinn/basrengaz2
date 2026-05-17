<template>
  <BaseModal
    v-model="isOpen"
    title="Login Diperlukan"
    size="md"
    @close="handleClose"
  >
    <div class="space-y-6 text-center py-4">
      <div class="mx-auto w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mb-4">
        <Lock class="w-8 h-8 text-red-600" />
      </div>
      
      <p class="text-gray-600">
        Silahkan masuk atau daftar akun baru untuk dapat memberikan ulasan pada produk kami.
      </p>

      <div class="flex flex-col gap-3 pt-4">
        <button
          @click="handleLogin"
          class="w-full px-4 py-3 text-sm font-semibold text-white bg-red-600 rounded-xl hover:bg-red-700 transition-colors"
        >
          Masuk ke Akun
        </button>
        <button
          @click="handleRegister"
          class="w-full px-4 py-3 text-sm font-semibold text-red-600 bg-red-50 rounded-xl hover:bg-red-100 transition-colors"
        >
          Daftar Akun Baru
        </button>
      </div>
    </div>
  </BaseModal>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { Lock } from 'lucide-vue-next'
import BaseModal from '@/components/common/BaseModal.vue'

interface Props {
  modelValue: boolean
}

const props = defineProps<Props>()
const emit = defineEmits<{
  'update:modelValue': [value: boolean]
}>()

const router = useRouter()

const isOpen = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
})

const handleClose = () => {
  isOpen.value = false
}

const handleLogin = () => {
  isOpen.value = false
  router.push('/login?redirect=/?action=ulasan')
}

const handleRegister = () => {
  isOpen.value = false
  router.push('/register?redirect=/?action=ulasan')
}
</script>
