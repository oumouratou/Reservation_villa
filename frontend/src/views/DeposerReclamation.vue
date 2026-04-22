<template>
  <div class="container reclamation-form-view">
    <h1 class="view-title">Déposer une réclamation</h1>
    <p class="view-subtitle">Un problème avec une réservation ? Faites-le nous savoir.</p>

    <div v-if="loading" class="loading-state">Chargement...</div>
    <div v-if="error" class="error-state">{{ error }}</div>

    <form v-if="!loading" @submit.prevent="submitReclamation" class="reclamation-form">
      <label>
        Réservation concernée
        <select v-model="form.reservation_id" required>
          <option disabled value="">-- Choisissez une réservation --</option>
          <option v-for="res in reservations" :key="res.id" :value="res.id">
            {{ res.villa.name }} ({{ formatDate(res.start_date) }} - {{ formatDate(res.end_date) }})
          </option>
        </select>
      </label>

      <label>
        Sujet
        <input v-model="form.sujet" type="text" placeholder="Ex: Problème de propreté" required>
      </label>

      <label>
        Description
        <textarea v-model="form.description" rows="6" placeholder="Veuillez décrire le problème en détail." required></textarea>
      </label>

      <button type="submit" class="primary-btn" :disabled="submitLoading">
        {{ submitLoading ? 'Envoi en cours...' : 'Envoyer la réclamation' }}
      </button>

      <p v-if="successMessage" class="success-message">{{ successMessage }}</p>
      <p v-if="submitError" class="error-message">{{ submitError }}</p>
    </form>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { useApi } from '@/utils/api';

const api = useApi();

const form = reactive({
  reservation_id: '',
  sujet: '',
  description: '',
});

const reservations = ref([]);
const loading = ref(true);
const error = ref(null);

const submitLoading = ref(false);
const successMessage = ref('');
const submitError = ref('');

async function fetchUserReservations() {
  loading.value = true;
  error.value = null;
  try {
    const response = await api.get('/client/reservations');
    // On ne garde que les réservations confirmées ou passées pour les réclamations
    reservations.value = response.data.filter(r => r.status === 'confirmed' || new Date(r.end_date) < new Date());
  } catch (e) {
    console.error("Erreur fetch reservations:", e);
    error.value = "Impossible de charger vos réservations pour le moment.";
  } finally {
    loading.value = false;
  }
}

async function submitReclamation() {
  submitLoading.value = true;
  successMessage.value = '';
  submitError.value = '';
  try {
    await api.post('/client/reclamations', form);
    successMessage.value = 'Votre réclamation a été envoyée avec succès.';
    // Reset form
    form.reservation_id = '';
    form.sujet = '';
    form.description = '';
  } catch (e) {
    console.error("Erreur submit reclamation:", e);
    submitError.value = e.response?.data?.message || "Une erreur est survenue.";
  } finally {
    submitLoading.value = false;
  }
}

function formatDate(dateString) {
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  return new Date(dateString).toLocaleDateString('fr-FR', options);
}

onMounted(fetchUserReservations);
</script>

<style scoped>
.reclamation-form-view {
  max-width: 700px;
  margin: 2rem auto;
  padding: 2rem;
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.view-title { text-align: center; margin-bottom: 0.5rem; }
.view-subtitle { text-align: center; color: #666; margin-bottom: 2rem; }

.reclamation-form { display: flex; flex-direction: column; gap: 1.5rem; }
.success-message { color: green; }
.error-message { color: red; }
</style>
