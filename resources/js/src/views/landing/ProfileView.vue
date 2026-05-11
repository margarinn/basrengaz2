<template>
  <div class="max-w-4xl mx-auto px-4 py-12">
    <div class="space-y-6">
      <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">Profil Saya</h1>
          <p class="text-sm text-gray-500">Kelola informasi akun Anda di sini.</p>
        </div>
        <BaseButton variant="danger" size="sm" @click="goBack">
          Kembali
        </BaseButton>
      </div>

      <BaseCard padding="lg">
        <div class="flex flex-col gap-8">
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
              <h2 class="text-xl font-semibold text-gray-900">{{ profile.name }}</h2>
              <p class="text-sm text-gray-500">{{ profile.email }}</p>
            </div>
            <div class="flex items-center gap-3">
              <div class="w-14 h-14 rounded-full bg-primary/10 flex items-center justify-center">
                <span class="text-primary font-bold text-lg">{{ profileInitial }}</span>
              </div>
              <span class="text-sm uppercase tracking-[0.2em] text-gray-500">{{ profile.role }}</span>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <BaseInput
              v-model="form.name"
              label="Nama"
              :disabled="!isEditing"
            />
            <BaseInput
              v-model="form.email"
              label="Email"
              type="email"
              :disabled="!isEditing"
            />
            <BaseInput
              v-model="form.phone"
              label="Telepon"
              type="tel"
              :disabled="!isEditing"
            />
            <BaseInput
              v-model="form.address"
              label="Alamat"
              :disabled="!isEditing"
            />
            <BaseInput
              v-model="form.role"
              label="Role"
              :disabled="true"
            />
            <BaseSelect
              v-model="form.gender"
              label="Gender"
              :options="genderOptions"
              placeholder="Pilih gender"
              :disabled="!isEditing"
            />
          </div>

          <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-end">
            <BaseButton v-if="!isEditing" variant="danger" @click="startEdit">
              Edit
            </BaseButton>
            <BaseButton v-if="isEditing" variant="danger" :loading="isSaving" @click="saveEdit">
              Simpan
            </BaseButton>
            <BaseButton v-if="isEditing" variant="ghost" @click="cancelEdit">
              Batal
            </BaseButton>
          </div>
        </div>
      </BaseCard>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import BaseCard from '@/components/common/BaseCard.vue'
import BaseInput from '@/components/common/BaseInput.vue'
import BaseSelect from '@/components/common/BaseSelect.vue'
import BaseButton from '@/components/common/BaseButton.vue'

const router = useRouter()
const authStore = useAuthStore()

const profile = reactive({
  name: '',
  email: '',
  phone: '',
  address: '',
  role: 'Pengguna',
  gender: ''
})

const form = reactive({
  name: '',
  email: '',
  phone: '',
  address: '',
  role: 'Pengguna',
  gender: ''
})

const isEditing = ref(false)
const isSaving = ref(false)

const genderOptions = [
  { value: 'male', label: 'Laki-laki' },
  { value: 'female', label: 'Perempuan' }
]

const profileInitial = computed(() => {
  return profile.name ? profile.name.charAt(0).toUpperCase() : 'U'
})

const syncProfile = () => {
  const user = authStore.user
  if (user) {
    profile.name = user.name
    profile.email = user.email
    profile.role = user.role === 'admin' ? 'Admin' : 'Pengguna'
    form.name = user.name
    form.email = user.email
    form.role = profile.role
    form.phone = ''
    form.address = ''
    form.gender = ''
  }
}

const startEdit = () => {
  Object.assign(form, profile)
  isEditing.value = true
}

const saveEdit = async () => {
  isSaving.value = true
  await new Promise(resolve => setTimeout(resolve, 500))
  Object.assign(profile, form)
  if (authStore.user) {
    authStore.user = {
      ...authStore.user,
      name: form.name,
      email: form.email
    }
  }
  isEditing.value = false
  isSaving.value = false
}

const cancelEdit = () => {
  Object.assign(form, profile)
  isEditing.value = false
}

const goBack = () => {
  router.push('/')
}

onMounted(async () => {
  if (!authStore.isAuthenticated) {
    router.push({ name: 'Login', query: { redirect: '/profile' } })
    return
  }

  if (!authStore.user) {
    await authStore.fetchProfile()
  }

  if (authStore.isAdmin) {
    router.push({ name: 'Dashboard' })
    return
  }

  syncProfile()
})
</script>
