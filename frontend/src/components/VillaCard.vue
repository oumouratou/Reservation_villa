<script setup>
defineProps({
  villa: {
    type: Object,
    required: true,
  },
})
</script>

<template>
  <article class="villa-card">
    <div class="villa-head">
      <h3>{{ villa.title }}</h3>
      <span class="price">{{ villa.price }} EUR / nuit</span>
    </div>
    <p class="location">{{ villa.city }}, {{ villa.country }}</p>
    <p class="desc">{{ villa.description }}</p>
    <div class="meta">
      <span>{{ villa.guests }} pers.</span>
      <span>{{ villa.bedrooms }} chambres</span>
      <span>Note {{ villa.rating }}</span>
    </div>
    <button class="primary-btn">Voir details</button>
  </article>
</template>
