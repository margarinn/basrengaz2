<template>
  <div class="space-y-6">
    <!-- Header -->
    <h1 class="text-2xl font-bold text-gray-900">Selamat Datang di Profile Kamu!</h1>

    <!-- Card -->
    <BaseCard padding="lg">
      <!-- Info atas + tombol Edit -->
      <div class="flex items-start justify-between mb-8">
        <div>
          <h2 class="text-lg font-bold text-gray-900">{{ isEditing ? form.username : profile.username }}</h2>
          <p class="text-sm text-gray-500 mt-0.5">{{ isEditing ? form.email : profile.email }}</p>
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
          v-model="form.username"
          label="Username"
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
          label="Phone"
          type="tel"
          :disabled="!isEditing"
        />
        <BaseInput
          v-model="form.address"
          label="Address"
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
  </div>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import BaseCard from '@/components/common/BaseCard.vue'
import BaseInput from '@/components/common/BaseInput.vue'
import BaseSelect from '@/components/common/BaseSelect.vue'
import BaseButton from '@/components/common/BaseButton.vue'

const router = useRouter()

const profile = reactive({
  username: 'admin',
  email: 'admin1@gmail.com',
  phone: '',
  address: '',
  role: 'Administrator',
  gender: '',
})

const isEditing = ref(false)
const isSaving = ref(false)
const form = reactive({ ...profile })

const genderOptions = [
  { value: 'male', label: 'Laki-laki' },
  { value: 'female', label: 'Perempuan' },
]

const startEdit = () => {
  Object.assign(form, profile)
  isEditing.value = true
}

const saveEdit = async () => {
  isSaving.value = true
  await new Promise(resolve => setTimeout(resolve, 500))
  Object.assign(profile, form)
  isEditing.value = false
  isSaving.value = false
}

const cancelEdit = () => {
  Object.assign(form, profile)
  isEditing.value = false
}

const handleBack = () => {
  router.back()
}
</script>