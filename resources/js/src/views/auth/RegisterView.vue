<template>
  <div class="h-screen flex md:px-48 md:py-12 md:bg-gradient-to-r md:from-[#F59E0B] md:to-[#E11D20]">
    <div class="w-full flex md:rounded-lg md:overflow-hidden md:shadow-2xl">
      <!-- Left Panel: Form -->
      <div class="w-full md:w-6/12 bg-white flex flex-col px-8 md:px-12 py-10 overflow-y-auto">
        <!-- Logo -->
        <div class="flex items-center gap-2 mb-12">
          <div class="w-9 h-9 bg-red-500 rounded-full flex items-center justify-center">
            <span class="text-white font-bold text-sm">AZ</span>
          </div>
          <span class="font-bold text-lg">
            <span class="text-primary">Basreng</span>
            <span class="text-accent-orange"> AZ-2</span>
          </span>
        </div>

        <!-- Heading -->
        <div class="mb-10">
          <h1 class="text-4xl font-extrabold text-gray-900 mb-2">Selamat Datang!</h1>
          <p class="text-gray-500">Daftarkan akun baru Anda.</p>
        </div>

        <!-- Error Alert -->
        <div
          v-if="authStore.error"
          class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg flex items-start gap-3"
        >
          <AlertCircle class="w-5 h-5 text-primary flex-shrink-0 mt-0.5" />
          <p class="text-sm text-red-600">{{ authStore.error }}</p>
        </div>

        <!-- Form -->
        <form @submit.prevent="handleSubmit" class="space-y-5 max-w-md">
          <!-- Username -->
          <div>
            <label class="block text-sm font-semibold text-gray-800 mb-1">Username</label>
            <BaseInput
              v-model="form.username"
              type="text"
              placeholder="Masukkan username"
              :icon="User"
              required
            />
          </div>

          <!-- Email -->
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

          <!-- Password -->
          <div>
            <label class="block text-sm font-semibold text-gray-800 mb-1">Password</label>
            <BaseInput
              v-model="form.password"
              :type="showPassword ? 'text' : 'password'"
              placeholder="Masukkan password"
              :icon="Lock"
              required
            />
          </div>

          <!-- Remember + Forgot -->
          <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 cursor-pointer">
              <input
                v-model="rememberMe"
                type="checkbox"
                class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-red-400"
              />
              <span class="text-sm text-gray-600">Ingat saya</span>
            </label>
          </div>

          <!-- Submit -->
          <BaseButton
            type="submit"
            :loading="authStore.loading"
            full-width
            class="!bg-[#DC2626] hover:!bg-red-600 text-white font-semibold py-3 rounded-xl w-full mt-2"
          >
            Daftar
          </BaseButton>
        </form>

        <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
          <router-link to="/" class="text-sm text-gray-500 hover:text-primary transition-colors">
            ← Kembali ke beranda
          </router-link>
          <router-link to="/login" class="text-sm text-primary hover:underline font-medium">
            Sudah punya akun?
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
import { Mail, Lock, AlertCircle, User } from 'lucide-vue-next'
import BaseInput from '@/components/common/BaseInput.vue'
import BaseButton from '@/components/common/BaseButton.vue'
import { useAuthStore } from '@/stores/auth'
import heroImg from '@/assets/images/gambar2.jpg'

const router = useRouter()
const authStore = useAuthStore()

const showPassword = ref(false)
const rememberMe = ref(false)

const form = ref({
    username: '',
    email: '',
    password: ''
})

const handleSubmit = async () => {
  const success = await authStore.register({
    username: form.value.username,
    email: form.value.email,
    password: form.value.password
  })

  if (success) {
    router.push('/login')
  }
}
</script>