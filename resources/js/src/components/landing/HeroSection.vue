<template>
  <section class="relative min-h-screen flex items-center py-8 overflow-hidden">
    <!-- Background Gradient -->
    <div class="absolute inset-0 bg-gradient-to-r from-[#F59E0B] to-[#E11D20]">
      <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg%20width%3D%2260%22%20height%3D%2260%22%20viewBox%3D%220%200%2060%2060%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cg%20fill%3D%22none%22%20fill-rule%3D%22evenodd%22%3E%3Cg%20fill%3D%22%23ffffff%22%20fill-opacity%3D%220.05%22%3E%3Cpath%20d%3D%22M36%2034v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6%2034v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6%204V0H4v4H0v2h4v4h2V6h4V4H6z%22%2F%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E')] opacity-30"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
      <div class="grid lg:grid-cols-2 gap-12 items-center">
        <!-- Content -->
        <div class="text-white space-y-6">
          <h1 class="text-2xl md:text-3xl lg:text-4xl font-bold leading-tight">
            Kriuknya Nagih,<br />
            <span class="text-yellow-300">Pedasnya Bikin Balik Lagi!</span>
          </h1>
          <p class="text-base md:text-md text-white/90 max-w-lg">
            Nikmati sensasi basreng premium dengan bumbu rempah asli dan daun jeruk segar. 
            Camilan paling pas buat nemenin seru-seruan bareng temen!
          </p>
          <div class="flex flex-wrap gap-4 pt-4">
            <router-link to="#about" class="px-12 py-2 border border-white text-white rounded-2xl font-semibold hover:bg-white hover:text-primary transition">
              Jelajahi
            </router-link>
          </div>
        </div>

        <!-- Image -->
        <div class="relative">
          <div class="relative z-10 transform hover:scale-105 transition-transform duration-500">
            <img
              src="../../assets/images/gambar1.png"
              alt="Basreng AZ-2"
              class="rounded-2xl shadow-2xl w-full object-cover"
            />
          </div>
          <!-- Decorative Elements -->
          <div class="absolute -top-8 -right-8 w-32 h-32 bg-yellow-400 rounded-full opacity-20 blur-2xl"></div>
          <div class="absolute -bottom-8 -left-8 w-40 h-40 bg-orange-400 rounded-full opacity-20 blur-2xl"></div>
        </div>
      </div>
    </div>

    
  </section>
</template>

<script setup lang="ts">
// Hero section component
</script>
