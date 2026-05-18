<template>
  <div class="space-y-6">
    <!-- Header -->
    <h1 class="text-2xl font-bold text-gray-900">Selamat Datang di Profile Kamu!</h1>

    <!-- Main Profile Card -->
    <BaseCard padding="lg">
      <!-- Info atas + tombol Edit -->
      <div class="flex items-start justify-between mb-8">
        <div>
          <h2 class="text-lg font-bold text-gray-900">{{ profile.name }}</h2>
          <p class="text-sm text-gray-500 mt-0.5">{{ profile.email }}</p>
        </div>
        <BaseButton
          v-if="!isEditing"
          variant="danger"
          size="sm"
          @click="startEdit"
        >
          Edit
        </BaseButton>
      </div>

      <!-- Form Grid -->
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

      <!-- Footer Buttons -->
      <div class="mt-8 flex items-center gap-3">
        <BaseButton
          v-if="isEditing"
          variant="danger"
          :loading="isSaving"
          @click="saveEdit"
        >
          Simpan
        </BaseButton>
        <BaseButton
            v-if="!isEditing"
            variant="danger"
            @click="handleBack"
        >
          Back
        </BaseButton>
        <BaseButton
          v-if="isEditing"
          variant="ghost"
          @click="cancelEdit"
        >
          Batal
        </BaseButton>
      </div>
    </BaseCard>

    <!-- Change Password Card -->
    <BaseCard padding="lg" title="Ganti Password">
      <form @submit.prevent="handleChangePassword" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <BaseInput
            v-model="passwordForm.current_password"
            label="Password Saat Ini"
            type="password"
            required
          />
          <BaseInput
            v-model="passwordForm.password"
            label="Password Baru"
            type="password"
            required
          />
          <BaseInput
            v-model="passwordForm.password_confirmation"
            label="Konfirmasi Password Baru"
            type="password"
            required
          />
        </div>
        <div class="flex justify-end">
          <BaseButton type="submit" variant="danger" :loading="isChangingPassword">
            Update Password
          </BaseButton>
        </div>
      </form>
    </BaseCard>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
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
  role: 'Admin',
  gender: '',
})

const form = reactive({
  name: '',
  email: '',
  phone: '',
  address: '',
  role: 'Admin',
  gender: '',
})

const passwordForm = reactive({
  current_password: '',
  password: '',
  password_confirmation: ''
})

const isEditing = ref(false)
const isSaving = ref(false)
const isChangingPassword = ref(false)

const genderOptions = [
  { value: 'male', label: 'Laki-laki' },
  { value: 'female', label: 'Perempuan' },
]

const syncProfile = () => {
  const user = authStore.user
  if (user) {
    profile.name = user.name
    profile.email = user.email
    profile.phone = user.phone || ''
    profile.role = user.role === 'admin' ? 'Admin' : 'Pengguna'
    Object.assign(form, profile)
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
  const success = await authStore.updateProfile({
    name: form.name,
    email: form.email,
    phone: form.phone
  })

  if (success) {
    Object.assign(profile, form)
    isEditing.value = false
  } else {
    alert('Gagal menyimpan profil.')
  }
  isSaving.value = false
}

const handleChangePassword = async () => {
  if (passwordForm.password !== passwordForm.password_confirmation) {
    alert('Konfirmasi password tidak cocok.')
    return
  }

  isChangingPassword.value = true
  const result = await authStore.updatePassword(passwordForm)
  
  if (result.success) {
    alert('Password berhasil diperbarui.')
    passwordForm.current_password = ''
    passwordForm.password = ''
    passwordForm.password_confirmation = ''
  } else {
    alert(result.message)
  }
  isChangingPassword.value = false
}

const cancelEdit = () => {
  Object.assign(form, profile)
  isEditing.value = false
}

const handleBack = () => {
  router.back()
}

onMounted(async () => {
  if (!authStore.user) {
    await authStore.fetchProfile()
  }
  syncProfile()
})
</script>
