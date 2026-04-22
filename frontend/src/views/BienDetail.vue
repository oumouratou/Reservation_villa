<template>
  <div class="container bien-detail-view">
    <div v-if="loading" class="loading-state">Chargement du bien...</div>
    <div v-if="error" class="error-state">{{ error }}</div>

    <div v-if="bien" class="bien-content">
      <h1 class="bien-name">{{ bien.name }}</h1>
      <p class="bien-location">{{ bien.address }}, {{ bien.city }}</p>

      <!-- Photo Gallery -->
      <div class="gallery">
        <img :src="mainImage" alt="Image principale" class="main-image">
        <div class="thumbnail-grid">
          <img v-for="photo in bien.photos" :key="photo.id" :src="photo.url" :alt="`Photo de ${bien.name}`"
               @click="mainImage = photo.url" :class="{ active: mainImage === photo.url }" class="thumbnail">
        </div>
      </div>

      <div class="details-layout">
        <!-- Left Column: Description & Amenities -->
        <div class="details-main">
          <h2>Description</h2>
          <p class="bien-description">{{ bien.description }}</p>

          <h2>Équipements</h2>
          <ul class="amenities-list">
            <li v-for="amenity in bien.amenities" :key="amenity.id">{{ amenity.name }}</li>
          </ul>
        </div>

        <!-- Right Column: Reservation Form -->
        <div class="details-sidebar">
          <div class="reservation-box">
            <h3 class="reservation-price">{{ bien.price_per_night }} € <span>/ nuit</span></h3>
            <!-- Reservation form will be implemented in the next step -->
            <form @submit.prevent="createReservation" class="reservation-form">
              <div class="date-inputs">
                <label>
                  Arrivée
                  <input type="date" v-model="reservation.date_debut" required>
                </label>
                <label>
                  Départ
                  <input type="date" v-model="reservation.date_fin" required>
                </label>
              </div>
              <button type="submit" class="primary-btn" :disabled="reservationLoading">
                {{ reservationLoading ? 'Envoi...' : 'Réserver' }}
              </button>
            </form>
             <p v-if="reservationSuccess" class="success-message">{{ reservationSuccess }}</p>
             <p v-if="reservationError" class="error-message">{{ reservationError }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue';
import { useRoute } from 'vue-router';
import { useApi } from '@/utils/api';

const props = defineProps({
  id: {
    type: String,
    required: true
  }
});

const api = useApi();
const route = useRoute();

const bien = ref(null);
const loading = ref(true);
const error = ref(null);
const mainImage = ref('');

const reservation = reactive({
  bien_id: props.id,
  date_debut: '',
  date_fin: '',
});
const reservationLoading = ref(false);
const reservationSuccess = ref('');
const reservationError = ref('');


async function fetchBienDetail() {
  loading.value = true;
  error.value = null;
  try {
    const response = await api.get(`/villas/${props.id}`);
    bien.value = response.data;
    if (bien.value.photos && bien.value.photos.length > 0) {
      mainImage.value = bien.value.photos[0].url;
    }
  } catch (e) {
    console.error("Erreur lors de la récupération du bien:", e);
    error.value = "Ce bien est introuvable ou une erreur est survenue.";
  } finally {
    loading.value = false;
  }
}

async function createReservation() {
  reservationLoading.value = true;
  reservationSuccess.value = '';
  reservationError.value = '';

  try {
    await api.post('/reservations', reservation);
    reservationSuccess.value = 'Votre demande de réservation a été envoyée avec succès !';
    // Clear form
    reservation.date_debut = '';
    reservation.date_fin = '';
  } catch (e) {
    console.error("Erreur lors de la création de la réservation:", e);
    reservationError.value = e.response?.data?.message || "Une erreur est survenue lors de la réservation.";
  } finally {
    reservationLoading.value = false;
  }
}


onMounted(fetchBienDetail);
</script>

<style scoped>
.bien-detail-view { padding: 2rem 0; }
.bien-name { font-size: 2.8rem; margin-bottom: 0.5rem; }
.bien-location { font-size: 1.2rem; color: #666; margin-bottom: 2rem; }

.gallery { margin-bottom: 3rem; }
.main-image {
  width: 100%;
  height: 500px;
  object-fit: cover;
  border-radius: 12px;
  margin-bottom: 1rem;
}
.thumbnail-grid { display: flex; gap: 1rem; }
.thumbnail {
  width: 100px;
  height: 75px;
  object-fit: cover;
  border-radius: 6px;
  cursor: pointer;
  border: 2px solid transparent;
  transition: border-color 0.2s;
}
.thumbnail.active { border-color: #007BFF; }

.details-layout { display: grid; grid-template-columns: 2fr 1fr; gap: 3rem; }
.bien-description { line-height: 1.8; font-size: 1.1rem; }
.amenities-list { list-style: none; padding: 0; display: flex; flex-wrap: wrap; gap: 1rem; }
.amenities-list li { background: #f1f1f1; padding: 0.5rem 1rem; border-radius: 20px; }

.reservation-box {
  background: #fff;
  padding: 2rem;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  position: sticky;
  top: 2rem;
}
.reservation-price { font-size: 1.8rem; margin: 0 0 1.5rem 0; }
.reservation-price span { font-size: 1rem; color: #666; }
.reservation-form { display: flex; flex-direction: column; gap: 1rem; }
.date-inputs { display: flex; gap: 1rem; }
.date-inputs input { width: 100%; }
.success-message { color: green; margin-top: 1rem; }
.error-message { color: red; margin-top: 1rem; }
</style>
