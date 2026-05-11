<template>
  <div class="h-screen flex md:px-48 md:py-12 md:bg-gradient-to-r md:from-[#F59E0B] md:to-[#E11D20]">
    <div class="w-full flex md:rounded-lg md:overflow-hidden md:shadow-2xl">
      <!-- Left Panel: Form -->
      <div class="w-full md:w-6/12 bg-white flex flex-col px-8 md:px-12 py-10">
        <!-- Logo -->
        <div class="flex items-center gap-2 mb-16">
          <div class="w-9 h-9 bg-red-500 rounded-full flex items-center justify-center flex-shrink-0">
            <span class="text-white font-bold text-sm">AZ</span>
          </div>
          <span class="font-bold text-lg">
            <span class="text-primary">Basreng</span>
            <span class="text-accent-orange"> AZ-2</span>
          </span>
        </div>

        <!-- Heading -->
        <div class="mb-10">
          <h1 class="text-4xl font-extrabold text-gray-900 mb-2">Lupa Password?</h1>
          <p class="text-gray-500">Masukkan email, nomor telepon, dan password baru untuk mereset akun Anda.</p>
        </div>

        <div v-if="successMessage" class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">
          {{ successMessage }}
        </div>

        <form @submit.prevent="handleSubmit" class="space-y-5 w-full">
          <div>
            <label class="block text-sm font-semibold text-gray-800 mb-1">Email</label>
            <BaseInput
              v-model="form.email"
              type="email"
              placeholder="Masukkan email"
              :icon="Mail"
              required
            />
          </div>

          <div>
            <label class="block text-sm font-semibold text-gray-800 mb-1">Nomor Telepon</label>
            <BaseInput
              v-model="form.phone"
              type="tel"
              placeholder="Masukkan nomor telepon"
              :icon="Phone"
              required
            />
          </div>

          <div>
            <label class="block text-sm font-semibold text-gray-800 mb-1">Password Baru</label>
            <BaseInput
              v-model="form.password"
              :type="showPassword ? 'text' : 'password'"
              placeholder="Masukkan password baru"
              :icon="Lock"
              required
            />
          </div>

          <BaseButton
            type="submit"
            :loading="isSubmitting"
            full-width
            class="!bg-[#DC2626] hover:!bg-red-600 text-white font-semibold py-3 rounded-xl w-full mt-2"
          >
            Reset Password
          </BaseButton>
        </form>

        <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
          <router-link to="/login" class="text-sm text-gray-500 hover:text-primary transition-colors">
            ← Kembali ke Login
          </router-link>
          <router-link to="/register" class="text-sm text-primary hover:underline font-medium">
            Buat akun baru
          </router-link>
        </div>
      </div>

      <!-- Right Panel: Foto — hanya tampil di desktop -->
      <div class="hidden md:block md:w-7/12">
        <img
          :src="heroImg"
          alt="Basreng AZ-2"
          class="w-full h-screen object-cover"
        />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { Mail, Lock, Phone } from 'lucide-vue-next'
import BaseInput from '@/components/common/BaseInput.vue'
import BaseButton from '@/components/common/BaseButton.vue'
import heroImg from '@/assets/images/gambar2.jpg'

const router = useRouter()
const showPassword = ref(false)
const isSubmitting = ref(false)
const successMessage = ref('')

const form = ref({
  email: '',
  phone: '',
  password: ''
})

const handleSubmit = async () => {
  isSubmitting.value = true
  successMessage.value = ''

  await new Promise(resolve => setTimeout(resolve, 700))

  successMessage.value = 'Permintaan reset password berhasil dikirim. Silakan cek email Anda untuk instruksi selanjutnya.'
  isSubmitting.value = false
}
</script>
