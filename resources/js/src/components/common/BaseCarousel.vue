<template>
  <div class="relative">
    <!-- Tombol Prev -->
    <button
      @click="prev"
      :disabled="currentIndex === 0"
      class="hidden md:flex absolute -left-12 top-1/2 -translate-y-1/2 z-10 w-10 h-10 bg-white rounded-full shadow-md items-center justify-center hover:bg-gray-50 disabled:opacity-30 disabled:cursor-not-allowed transition-all"
    >
      <ChevronLeft class="w-5 h-5 text-gray-600" />
    </button>

    <!-- Track -->
    <div class="overflow-hidden py-8 px-1">
      <div
        ref="trackRef"
        class="flex select-none will-change-transform"
        :style="{
          gap: `${gap}px`,
          transform: `translateX(${currentTranslate}px)`,
          transition: isAnimating ? 'transform 0.45s cubic-bezier(0.25, 0.46, 0.45, 0.94)' : 'none'
        }"
        @mousedown="onMouseDown"
        @touchstart.passive="onTouchStart"
        @touchmove.passive="onTouchMove"
        @touchend="onTouchEnd"
      >
        <div
          v-for="(_, i) in items"
          :key="i"
          class="flex-shrink-0"
          :style="{ width: cardWidthPx }"
        >
          <slot :item="items[i]" :index="i" />
        </div>
      </div>
    </div>

    <!-- Tombol Next -->
    <button
      @click="next"
      :disabled="currentIndex >= maxIndex"
      class="hidden md:flex absolute -right-12 top-1/2 -translate-y-1/2 z-10 w-10 h-10 bg-white rounded-full shadow-md items-center justify-center hover:bg-gray-50 disabled:opacity-30 disabled:cursor-not-allowed transition-all"
    >
      <ChevronRight class="w-5 h-5 text-gray-600" />
    </button>

    <!-- Dots -->
    <div class="flex justify-center gap-2 mt-4">
      <template v-for="dot in visibleDots" :key="dot.index">
        <button
          @click="goTo(dot.index)"
          class="rounded-full transition-all duration-300"
          :class="[
            currentIndex === dot.index
              ? 'bg-primary w-4 h-2'
              : dot.isEdge
                ? 'bg-gray-300 w-1.5 h-1.5'
                : 'bg-gray-300 w-2 h-2'
          ]"
        />
      </template>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { ChevronLeft, ChevronRight } from 'lucide-vue-next'

interface Props {
  items: any[]
  itemsPerViewDesktop?: number  // berapa item tampil di desktop
  itemsPerViewMobile?: number   // berapa item tampil di mobile
  gap?: number                  // jarak antar card (px)
  mobileBreakpoint?: number     // lebar layar untuk switch mobile (px)
}

const props = withDefaults(defineProps<Props>(), {
  itemsPerViewDesktop: 3,
  itemsPerViewMobile: 1,
  gap: 24,
  mobileBreakpoint: 768,
})

// ─── State ─────────────────────────────────────────────────────────
const trackRef = ref<HTMLElement | null>(null)
const currentIndex = ref(0)
const itemsPerView = ref(props.itemsPerViewDesktop)
const isAnimating = ref(false)
const currentTranslate = ref(0)

const updateItemsPerView = () => {
  itemsPerView.value = window.innerWidth < props.mobileBreakpoint
    ? props.itemsPerViewMobile
    : props.itemsPerViewDesktop
  snapToIndex(currentIndex.value, false)
}

// ─── Computed ──────────────────────────────────────────────────────
const cardWidthPx = computed(() => {
  if (!trackRef.value) return '280px'
  const container = trackRef.value.parentElement?.clientWidth ?? 0
  if (itemsPerView.value === 1) return '280px'
  const w = (container - props.gap * (itemsPerView.value - 1)) / itemsPerView.value
  return `${w}px`
})

const cardWidthNum = computed(() => parseFloat(cardWidthPx.value))
const maxIndex = computed(() => Math.max(0, props.items.length - itemsPerView.value))

const getTranslateForIndex = (index: number) => -(index * (cardWidthNum.value + props.gap))

const snapToIndex = (index: number, animate = true) => {
  currentIndex.value = Math.min(Math.max(0, index), maxIndex.value)
  isAnimating.value = animate
  currentTranslate.value = getTranslateForIndex(currentIndex.value)
}

const prev = () => snapToIndex(currentIndex.value - 1)
const next = () => snapToIndex(currentIndex.value + 1)
const goTo = (i: number) => snapToIndex(i)

// ─── Dots ──────────────────────────────────────────────────────────
const visibleDots = computed(() => {
  const total = maxIndex.value + 1
  if (total <= 5) return Array.from({ length: total }, (_, i) => ({ index: i, isEdge: false }))
  const cur = currentIndex.value
  let start = Math.max(0, cur - 2)
  const end = Math.min(total - 1, start + 4)
  start = Math.max(0, end - 4)
  return Array.from({ length: end - start + 1 }, (_, i) => ({
    index: start + i,
    isEdge: (i === 0 && start > 0) || (i === end - start && end < total - 1)
  }))
})

// ─── Touch ─────────────────────────────────────────────────────────
const touchStartX = ref(0)
const touchLastX = ref(0)
const touchVelocity = ref(0)

const onTouchStart = (e: TouchEvent) => {
  touchStartX.value = e.touches[0].clientX
  touchLastX.value = e.touches[0].clientX
  touchVelocity.value = 0
  isAnimating.value = false
}

const onTouchMove = (e: TouchEvent) => {
  const x = e.touches[0].clientX
  touchVelocity.value = x - touchLastX.value
  touchLastX.value = x
  currentTranslate.value = getTranslateForIndex(currentIndex.value) + (x - touchStartX.value)
}

const onTouchEnd = () => {
  const delta = currentTranslate.value - getTranslateForIndex(currentIndex.value)
  const threshold = cardWidthNum.value * 0.2
  if (delta < -threshold || touchVelocity.value < -5) snapToIndex(currentIndex.value + 1)
  else if (delta > threshold || touchVelocity.value > 5) snapToIndex(currentIndex.value - 1)
  else snapToIndex(currentIndex.value)
}

// ─── Mouse drag ────────────────────────────────────────────────────
const isDragging = ref(false)
const mouseStartX = ref(0)
const mouseLastX = ref(0)
const mouseVelocity = ref(0)

const onMouseDown = (e: MouseEvent) => {
  if (e.button !== 0) return
  isDragging.value = false
  mouseStartX.value = e.clientX
  mouseLastX.value = e.clientX
  mouseVelocity.value = 0
  isAnimating.value = false

  const onMouseMove = (e: MouseEvent) => {
    mouseVelocity.value = e.clientX - mouseLastX.value
    mouseLastX.value = e.clientX
    const delta = e.clientX - mouseStartX.value
    if (Math.abs(delta) > 5) isDragging.value = true
    if (isDragging.value) {
      currentTranslate.value = getTranslateForIndex(currentIndex.value) + delta
    }
  }

  const onMouseUp = (e: MouseEvent) => {
    const delta = e.clientX - mouseStartX.value
    const threshold = cardWidthNum.value * 0.2
    if (isDragging.value) {
      if (delta < -threshold || mouseVelocity.value < -5) snapToIndex(currentIndex.value + 1)
      else if (delta > threshold || mouseVelocity.value > 5) snapToIndex(currentIndex.value - 1)
      else snapToIndex(currentIndex.value)
    }
    setTimeout(() => { isDragging.value = false }, 0)
    window.removeEventListener('mousemove', onMouseMove)
    window.removeEventListener('mouseup', onMouseUp)
  }

  window.addEventListener('mousemove', onMouseMove)
  window.addEventListener('mouseup', onMouseUp)
}

// Expose isDragging agar parent bisa cek sebelum handle click
defineExpose({ isDragging })

// ─── Lifecycle ─────────────────────────────────────────────────────
onMounted(() => {
  updateItemsPerView()
  window.addEventListener('resize', updateItemsPerView)
})

onUnmounted(() => {
  window.removeEventListener('resize', updateItemsPerView)
})
</script>