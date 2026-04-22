<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue'
import { getAuthUser } from '../utils/auth'

const slides = [
  {
    id: 1,
    title: 'Villa Azure Horizon',
    city: 'Saint-Raphael',
    image: 'https://images.unsplash.com/photo-1613977257363-707ba9348227?auto=format&fit=crop&w=2200&q=80',
  },
  {
    id: 2,
    title: 'Casa Terra Cotta',
    city: 'Marbella',
    image: 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=2200&q=80',
  },
  {
    id: 3,
    title: 'Lakewood Prestige',
    city: 'Annecy',
    image: 'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=2200&q=80',
  },
  {
    id: 4,
    title: 'Palma Coral Estate',
    city: 'Mallorca',
    image: 'https://images.unsplash.com/photo-1613490493576-7fde63acd811?auto=format&fit=crop&w=2200&q=80',
  },
]

const activeSlideIndex = ref(0)
let sliderTimer = null

const activeSlide = computed(() => slides[activeSlideIndex.value])
const isGuest = computed(() => !getAuthUser())

function goToSlide(index) {
  activeSlideIndex.value = index
}

function nextSlide() {
  activeSlideIndex.value = (activeSlideIndex.value + 1) % slides.length
}

onMounted(() => {
  sliderTimer = window.setInterval(nextSlide, 4500)
})

onUnmounted(() => {
  if (sliderTimer) window.clearInterval(sliderTimer)
})
</script>

<template>
  <section class="home-immersive" aria-label="Accueil premium">
    <TransitionGroup name="hero-fade" tag="div" class="hero-bg-stack">
      <img
        v-for="(slide, index) in slides"
        v-show="index === activeSlideIndex"
        :key="slide.id"
        class="hero-bg"
        :src="slide.image"
        :alt="`Photo de ${slide.title}`"
      />
    </TransitionGroup>

    <div class="hero-overlay"></div>

    <div class="hero-content container">
      <p class="hero-chip">Collection Signature</p>
      <h1>Des villas spectaculaires pour des sejours memorables.</h1>
      <p class="hero-subtitle">
        Une experience premium de reservation, avec un accueil visuel immersif et une navigation fluide.
      </p>

      <div class="hero-focus-card">
        <p class="focus-city">{{ activeSlide.city }}</p>
        <h2>{{ activeSlide.title }}</h2>
      </div>

      <div v-if="isGuest" class="hero-actions">
        <router-link to="/connexion?mode=login" class="hero-btn hero-btn-primary">Connexion</router-link>
        <router-link to="/connexion?mode=register" class="hero-btn hero-btn-secondary">Inscription</router-link>
      </div>

      <div class="hero-dots" role="tablist" aria-label="Choisir une image">
        <button
          v-for="(slide, index) in slides"
          :key="slide.id"
          class="hero-dot"
          :class="{ active: index === activeSlideIndex }"
          type="button"
          :aria-label="`Afficher ${slide.title}`"
          @click="goToSlide(index)"
        ></button>
      </div>
    </div>
  </section>
</template>

<style scoped>
.home-immersive {
  position: relative;
  margin-top: -2.4rem;
  margin-bottom: -3rem;
  min-height: calc(100vh - 92px);
  overflow: hidden;
  isolation: isolate;
}

.hero-bg-stack {
  position: absolute;
  inset: 0;
}

.hero-bg {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  transform: scale(1.03);
}

.hero-overlay {
  position: absolute;
  inset: 0;
  background:
    linear-gradient(105deg, rgba(8, 16, 28, 0.82) 15%, rgba(8, 16, 28, 0.52) 45%, rgba(8, 16, 28, 0.22) 100%),
    radial-gradient(circle at 85% 20%, rgba(12, 133, 182, 0.35), transparent 34%);
}

.hero-content {
  position: relative;
  z-index: 2;
  min-height: calc(100vh - 92px);
  display: flex;
  flex-direction: column;
  justify-content: center;
  color: #fff;
  padding-block: 2.5rem;
}

.hero-chip {
  width: fit-content;
  padding: 0.35rem 0.75rem;
  border-radius: 999px;
  background: rgba(255, 255, 255, 0.16);
  border: 1px solid rgba(255, 255, 255, 0.28);
  letter-spacing: 0.08em;
  text-transform: uppercase;
  font-weight: 700;
  font-size: 0.74rem;
}

h1 {
  margin: 1rem 0 0.8rem;
  max-width: 12ch;
  font-size: clamp(2rem, 5.2vw, 4.2rem);
  line-height: 1.02;
  color: #fff;
}

.hero-subtitle {
  margin: 0;
  max-width: 54ch;
  color: rgba(255, 255, 255, 0.88);
  font-size: clamp(1rem, 2vw, 1.15rem);
}

.hero-focus-card {
  width: min(420px, 100%);
  margin-top: 1.3rem;
  border-radius: 16px;
  border: 1px solid rgba(255, 255, 255, 0.34);
  background: rgba(7, 16, 29, 0.42);
  backdrop-filter: blur(8px);
  padding: 1rem 1.1rem;
}

.focus-city {
  margin: 0;
  color: #9fe2ff;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  font-size: 0.76rem;
  font-weight: 700;
}

.hero-focus-card h2 {
  margin: 0.35rem 0 0;
  font-size: clamp(1.2rem, 2.2vw, 1.55rem);
}

.hero-actions {
  margin-top: 1.2rem;
  display: flex;
  gap: 0.7rem;
  flex-wrap: wrap;
}

.hero-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 140px;
  padding: 0.72rem 1rem;
  border-radius: 999px;
  font-size: 0.88rem;
  font-weight: 700;
  letter-spacing: 0.01em;
  text-decoration: none;
  transition: transform 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
}

.hero-btn:hover {
  transform: translateY(-1px);
}

.hero-btn-primary {
  background: #ffffff;
  color: #0f172a;
  box-shadow: 0 10px 24px rgba(15, 23, 42, 0.28);
}

.hero-btn-secondary {
  background: rgba(255, 255, 255, 0.14);
  border: 1px solid rgba(255, 255, 255, 0.42);
  color: #ffffff;
  backdrop-filter: blur(6px);
}

.hero-ghost {
  color: #0f172a;
  background: #fff;
}

.hero-dots {
  margin-top: 1.45rem;
  display: flex;
  gap: 0.55rem;
}

.hero-dot {
  width: 11px;
  height: 11px;
  border-radius: 50%;
  border: 0;
  background: rgba(255, 255, 255, 0.48);
  cursor: pointer;
  transition: transform 0.2s ease, background 0.2s ease;
}

.hero-dot.active {
  transform: scale(1.18);
  background: #fff;
}

.hero-fade-enter-active,
.hero-fade-leave-active {
  transition: opacity 0.8s ease;
}

.hero-fade-enter-from,
.hero-fade-leave-to {
  opacity: 0;
}

@media (max-width: 768px) {
  .home-immersive {
    min-height: calc(100vh - 102px);
  }

  .hero-content {
    min-height: calc(100vh - 102px);
    justify-content: flex-end;
    padding-bottom: 2rem;
  }

  h1 {
    max-width: 16ch;
  }
}
</style>
