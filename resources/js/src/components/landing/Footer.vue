<template>
  <footer id="contact" class="bg-dark text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <div class="grid md:grid-cols-3 gap-8">
        <!-- Brand -->
        <div class="space-y-4">
          <div class="flex items-center space-x-2">
            <div class="w-10 h-10 bg-primary rounded-full flex items-center justify-center">
              <span class="text-white font-bold text-lg">AZ</span>
            </div>
            <span class="text-xl font-bold text-primary">
              BASRENG <span class="text-accent-orange">AZ-2</span>
            </span>
          </div>
          <p class="text-gray-400 text-sm leading-relaxed">
            Pionir jajanan basreng kualitas premium dengan cita rasa nusantara yang menggoda.
          </p>
          <div class="flex gap-3">
            <a
              v-for="social in socials"
              :key="social.name"
              :href="social.url"
              target="_blank"
              class="w-10 h-10 bg-white/10 rounded-full flex items-center justify-center hover:bg-primary transition-colors"
            >
              <Icon :icon="social.icon" class="w-5 h-5" />
            </a>
          </div>
        </div>

        <!-- Contact -->
        <div>
          <h3 class="text-lg font-semibold mb-4">Hubungi Kami</h3>
          <ul class="space-y-3">
            <li class="flex items-start gap-3">
              <MapPin class="w-5 h-5 text-primary flex-shrink-0 mt-0.5" />
              <span class="text-gray-400 text-sm">Gg. Kenari No.9, RT.02/RW.03, Pasir Jaya, Kec. Bogor Bar., Kota Bogor, Jawa Barat 16119</span>
            </li>
            <li class="flex items-center gap-3">
              <Phone class="w-5 h-5 text-primary flex-shrink-0" />
              <span class="text-gray-400 text-sm">+62 857-1176-5237</span>
            </li>
            <li class="flex items-center gap-3">
              <Clock class="w-5 h-5 text-primary flex-shrink-0" />
              <span class="text-gray-400 text-sm">08:00 - 20:00 WIB</span>
            </li>
          </ul>
        </div>

        <!-- Location Map Placeholder -->
        <div>
          <h3 class="text-lg font-semibold mb-4">Lokasi Kami</h3>
          <div class="bg-gray-700 rounded-xl h-64 overflow-hidden shadow-lg">
            <iframe 
              src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3963.384598938078!2d106.7812014!3d-6.5990329!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69c52b3a597c19%3A0x994ee8c862941b67!2sKeripik%20Basreng%20AZ-2!5e0!3m2!1sen!2sid!4v1777275202878!5m2!1sen!2sid"
              class="w-full h-full border-0"
              loading="lazy"
              referrerpolicy="no-referrer-when-downgrade">
            </iframe>
          </div>
        </div>
      </div>

      <!-- Copyright -->
      <div class="border-t border-gray-700 mt-10 pt-6 text-center">
        <p class="text-gray-400 text-sm">
          © {{ currentYear }} Basreng AZ-2. All rights reserved.
        </p>
      </div>
    </div>
  </footer>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { Icon } from '@iconify/vue'
import { MapPin, Phone, Clock } from 'lucide-vue-next'

const currentYear = computed(() => new Date().getFullYear())

const socials = [
  { name: 'Instagram', icon: 'mdi:instagram', url: 'https://www.instagram.com/keripikbasrengaz2/' },
  { name: 'WhatsApp', icon: 'mdi:whatsapp', url: 'https://wa.me/6285711765237' },
  { name: 'Shopee', icon: 'simple-icons:shopee', url: 'https://shopee.co.id/keripikbasrengaz_2' },
  { name: 'TikTok', icon: 'simple-icons:tiktok', url: 'https://www.tiktok.com/@keripikbasrengaz2' },
]
</script>
