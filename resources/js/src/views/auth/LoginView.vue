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
          <h1 class="text-4xl font-extrabold text-gray-900 mb-2">Selamat Datang!</h1>
          <p class="text-gray-500">Silakan masukan akun anda</p>
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
            <label class="block text-sm font-semibold text-gray-800 mb-1">Password</label>
            <BaseInput
              v-model="form.password"
              :type="showPassword ? 'text' : 'password'"
              placeholder="Masukkan password"
              :icon="Lock"
              required
            />
          </div>

          <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 cursor-pointer">
              <input
                v-model="rememberMe"
                type="checkbox"
                class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-red-400"
              />
              <span class="text-sm text-gray-600">Ingat saya</span>
            </label>
            <router-link to="/forgot-password" class="text-sm text-primary hover:underline font-medium">Lupa password?</router-link>
          </div>

          <BaseButton
            type="submit"
            :loading="authStore.loading"
            full-width
            class="!bg-[#DC2626] hover:!bg-red-600 text-white font-semibold py-3 rounded-xl w-full mt-2"
          >
            Masuk
          </BaseButton>
        </form>

        <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
          <router-link to="/" class="text-sm text-gray-500 hover:text-primary transition-colors">
            ← Kembali ke beranda
          </router-link>
          <router-link to="/register" class="text-sm text-primary hover:underline font-medium">
            Belum punya akun?
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
import { useRouter, useRoute } from 'vue-router'
import { Mail, Lock, AlertCircle } from 'lucide-vue-next'
import BaseInput from '@/components/common/BaseInput.vue'
import BaseButton from '@/components/common/BaseButton.vue'
import { useAuthStore } from '@/stores/auth'
import heroImg from '@/assets/images/gambar2.jpg'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()

const showPassword = ref(false)
const rememberMe = ref(false)

const form = ref({
  email: '',
  password: ''
})

const handleSubmit = async () => {
  const success = await authStore.login({
    email: form.value.email,
    password: form.value.password
  })

  if (success) {
    const redirect = route.query.redirect as string
    
    // Jika ada redirect query, gunakan itu
    if (redirect) {
      router.push(redirect)
    } else {
      // Jika admin, ke dashboard; jika user biasa, ke landing page
      const targetRoute = authStore.isAdmin ? '/dashboard' : '/'
      router.push(targetRoute)
    }
  }
}
</script>