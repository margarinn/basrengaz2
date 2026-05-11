<template>
  <div>
    <section id="home" class="reveal">
      <HeroSection />
    </section>

    <section id="about" class="reveal">
      <AboutSection />
    </section>

    <section id="products" class="reveal ">
      <ProductsSection />
    </section>

    <section id="news" class="reveal">
      <NewsSection />
    </section>

    <section id="ratings" class="reveal">
      <RatingSection />
    </section>
  </div>
</template>

<script setup lang="ts">
import HeroSection from '@/components/landing/HeroSection.vue'
import AboutSection from '@/components/landing/AboutSection.vue'
import ProductsSection from '@/components/landing/ProductsSection.vue'
import NewsSection from '@/components/landing/NewsSection.vue'
import RatingSection from '@/components/landing/RatingSection.vue'

import { onMounted } from 'vue'

onMounted(() => {
  const elements = document.querySelectorAll('.reveal')

  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('active')
        }
      })
    },
    {
      threshold: 0.2
    }
  )

  elements.forEach(el => observer.observe(el))
})
</script>
