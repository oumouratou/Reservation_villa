<template>
  <div class="container biens-list-view">
    <h1 class="view-title">Découvrez nos biens d'exception</h1>
    <p class="view-subtitle">Parcourez notre catalogue de villas de luxe disponibles à la location.</p>

    <div v-if="loading" class="loading-state">
      <p>Chargement des biens...</p>
    </div>

    <div v-if="error" class="error-state">
      <p>{{ error }}</p>
    </div>

    <div v-if="!loading && !error" class="biens-grid">
      <div v-for="bien in biens" :key="bien.id" class="bien-card" @click="goToBienDetail(bien.id)">
        <img :src="bien.photos[0]?.url || '/placeholder.jpg'" :alt="`Photo de ${bien.name}`" class="bien-image">
        <div class="bien-info">
          <h3 class="bien-name">{{ bien.name }}</h3>
          <p class="bien-location">{{ bien.address }}, {{ bien.city }}</p>
          <p class="bien-price">{{ bien.price_per_night }} € / nuit</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useApi } from '@/utils/api';

const api = useApi();
const router = useRouter();

const biens = ref([]);
const loading = ref(true);
const error = ref(null);

async function fetchBiens() {
  loading.value = true;
  error.value = null;
  try {
    // On suppose que l'endpoint /villas?statut=disponible existe
    const response = await api.get('/villas', { params: { statut: 'disponible' } });
    biens.value = response.data.data; // La réponse est probablement paginée
  } catch (e) {
    console.error("Erreur lors de la récupération des biens:", e);
    error.value = "Impossible de charger les biens pour le moment. Veuillez réessayer plus tard.";
  } finally {
    loading.value = false;
  }
}

function goToBienDetail(id) {
  router.push({ name: 'bien-detail', params: { id } });
}

onMounted(fetchBiens);
</script>

<style scoped>
.biens-list-view {
  padding: 2rem 0;
}

.view-title {
  font-size: 2.5rem;
  text-align: center;
  margin-bottom: 0.5rem;
}

.view-subtitle {
  font-size: 1.2rem;
  text-align: center;
  color: #666;
  margin-bottom: 3rem;
}

.biens-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 2rem;
}

.bien-card {
  background-color: #fff;
  border-radius: 8px;
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
  overflow: hidden;
  cursor: pointer;
  transition: transform 0.2s, box-shadow 0.2s;
}

.bien-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 16px rgba(0,0,0,0.15);
}

.bien-image {
  width: 100%;
  height: 200px;
  object-fit: cover;
}

.bien-info {
  padding: 1.5rem;
}

.bien-name {
  font-size: 1.4rem;
  margin: 0 0 0.5rem 0;
}

.bien-location {
  color: #555;
  margin-bottom: 1rem;
}

.bien-price {
  font-size: 1.2rem;
  font-weight: bold;
  color: #007BFF;
}

.loading-state, .error-state {
  text-align: center;
  padding: 4rem 0;
  font-size: 1.2rem;
}
</style>
