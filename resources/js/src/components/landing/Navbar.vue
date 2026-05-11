<template>
  <nav :class="navbarClasses">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-16">
        <!-- Logo -->
        <router-link to="/" class="flex items-center space-x-2">
          <div class="w-10 h-10 bg-primary rounded-full flex items-center justify-center">
            <span class="text-white font-bold text-lg">AZ</span>
          </div>
          <span class="text-xl font-bold text-primary">
            BASRENG <span class="text-accent-orange">AZ-2</span>
          </span>
        </router-link>

        <!-- Desktop Menu -->
        <div class="hidden md:flex items-center space-x-8">
          <button
            v-for="item in menuItems"
            :key="item.name"
            @click="scrollToSection(item.id)"
            :class="linkClasses(item.id)"
          >
            {{ item.name }}
          </button>
        </div>

        <!-- User Icon -->
        <div class="hidden md:flex items-center">
          <router-link
            to="/profile"
            class="p-2 rounded-full hover:bg-gray-100 transition-colors"
          >
            <User class="w-6 h-6 text-gray-600" />
          </router-link>
        </div>

        <!-- Mobile Menu Button -->
        <button
          class="md:hidden p-2 rounded-lg hover:bg-gray-100"
          @click="isMobileMenuOpen = !isMobileMenuOpen"
        >
          <Menu v-if="!isMobileMenuOpen" class="w-6 h-6" />
          <X v-else class="w-6 h-6" />
        </button>
      </div>
    </div>

    <!-- Mobile Menu -->
    <Transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="transform -translate-y-4 opacity-0"
      enter-to-class="transform translate-y-0 opacity-100"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="transform translate-y-0 opacity-100"
      leave-to-class="transform -translate-y-4 opacity-0"
    >
      <div
        v-if="isMobileMenuOpen"
        class="md:hidden bg-white border-t border-gray-100 shadow-lg"
      >
        <div class="px-4 py-3 space-y-2">
          <button
            v-for="item in menuItems"
            :key="item.name"
            @click="handleMobileClick(item.id)"
            class="block w-full text-left px-4 py-2 rounded-lg text-gray-700 hover:bg-gray-50 hover:text-primary"
          >
            {{ item.name }}
          </button>
          <router-link
            to="/profile"
            class="block px-4 py-2 rounded-lg text-primary font-medium hover:bg-primary-50"
            @click="isMobileMenuOpen = false"
          >
            Profile
          </router-link>
        </div>
      </div>
    </Transition>
  </nav>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { User, Menu, X } from 'lucide-vue-next'

const activeSection = ref('home')
const isMobileMenuOpen = ref(false)
const isScrolled = ref(false)

const menuItems = [
  { name: 'Home', id: 'home' },
  { name: 'About', id: 'about' },
  { name: 'Produk', id: 'products' },
  { name: 'Berita', id: 'news' },
  { name: 'Kontak', id: 'contact' }
]

const scrollToSection = (id: string) => {
  const el = document.getElementById(id)
  if (el) {
    el.scrollIntoView({ behavior: 'smooth' })
  }
}

const handleMobileClick = (id: string) => {
  scrollToSection(id)
  isMobileMenuOpen.value = false
}

/* ======================
   Scroll Spy + Navbar Effect
====================== */
const handleScroll = () => {

  for (const item of menuItems) {
    const section = document.getElementById(item.id)
    if (section) {
      if (
        window.scrollY + 120 >= section.offsetTop &&
        window.scrollY < section.offsetTop + section.offsetHeight
      ) {
        activeSection.value = item.id
      }
    }
  }

  if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 10) {
    activeSection.value = 'contact'
  }

  isScrolled.value = window.scrollY > 20
}

onMounted(() => {
  window.addEventListener('scroll', handleScroll)
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
})

const navbarClasses = computed(() => {
  return [
    'fixed top-0 left-0 right-0 z-50 transition-all duration-300',
    isScrolled.value
      ? 'bg-white shadow-md'
      : 'bg-white/95 backdrop-blur-sm'
  ].join(' ')
})

const linkClasses = (id: string) => {
  const isActive = activeSection.value === id

  return [
    'text-sm font-medium transition-colors duration-200',
    isActive
      ? 'text-primary'
      : 'text-gray-700 hover:text-primary'
  ].join(' ')
}
</script>
