<template>
  <div class="container reservations-view">
    <h1 class="view-title">Mes Réservations</h1>
    <p class="view-subtitle">Suivez l'état de toutes vos demandes de réservation passées et à venir.</p>

    <div v-if="loading" class="loading-state">
      <p>Chargement de vos réservations...</p>
    </div>

    <div v-if="error" class="error-state">
      <p>{{ error }}</p>
    </div>

    <div v-if="!loading && reservations.length === 0" class="empty-state">
      <p>Vous n'avez aucune réservation pour le moment.</p>
      <router-link :to="{ name: 'biens-list' }" class="primary-btn">Découvrir nos biens</router-link>
    </div>

    <div v-if="!loading && reservations.length > 0" class="reservations-list">
      <div v-for="reservation in reservations" :key="reservation.id" class="reservation-card">
        <div class="card-header">
          <h3>{{ reservation.villa.name }}</h3>
          <span :class="['status-badge', getStatusClass(reservation.status)]">{{ formatStatus(reservation.status) }}</span>
        </div>
        <div class="card-body">
          <p><strong>Lieu :</strong> {{ reservation.villa.address }}, {{ reservation.villa.city }}</p>
          <p><strong>Dates :</strong> Du {{ formatDate(reservation.start_date) }} au {{ formatDate(reservation.end_date) }}</p>
          <div v-if="reservation.commentaire_agent" class="agent-comment">
            <strong>Commentaire de l'agent :</strong>
            <p>{{ reservation.commentaire_agent }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useApi } from '@/utils/api';

const api = useApi();
const reservations = ref([]);
const loading = ref(true);
const error = ref(null);

async function fetchReservations() {
  loading.value = true;
  error.value = null;
  try {
    const response = await api.get('/client/reservations');
    reservations.value = response.data;
  } catch (e) {
    console.error("Erreur lors de la récupération des réservations:", e);
    error.value = "Impossible de charger vos réservations.";
  } finally {
    loading.value = false;
  }
}

function getStatusClass(status) {
  switch (status) {
    case 'pending': return 'status-pending';
    case 'confirmed': return 'status-confirmed';
    case 'rejected': return 'status-rejected';
    default: return '';
  }
}

function formatStatus(status) {
    switch (status) {
        case 'pending': return 'En attente';
        case 'confirmed': return 'Confirmée';
        case 'rejected': return 'Refusée';
        default: return status;
    }
}

function formatDate(dateString) {
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  return new Date(dateString).toLocaleDateString('fr-FR', options);
}

onMounted(fetchReservations);
</script>

<style scoped>
.reservations-view { padding: 2rem 0; }
.view-title { text-align: center; font-size: 2.5rem; margin-bottom: 0.5rem; }
.view-subtitle { text-align: center; color: #666; margin-bottom: 3rem; }

.reservations-list { display: flex; flex-direction: column; gap: 1.5rem; max-width: 800px; margin: auto; }
.reservation-card { background: #fff; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.08); }
.card-header { display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.5rem; border-bottom: 1px solid #eee; }
.card-header h3 { margin: 0; font-size: 1.4rem; }
.card-body { padding: 1.5rem; }
.card-body p { margin: 0 0 0.8rem 0; }

.status-badge { padding: 0.3rem 0.8rem; border-radius: 15px; font-weight: bold; color: #fff; }
.status-pending { background-color: #ffc107; }
.status-confirmed { background-color: #28a745; }
.status-rejected { background-color: #dc3545; }

.agent-comment { margin-top: 1rem; padding: 1rem; background: #f8f9fa; border-radius: 6px; }
.agent-comment p { margin: 0.5rem 0 0 0; }

.loading-state, .error-state, .empty-state { text-align: center; padding: 4rem 0; font-size: 1.2rem; }
.empty-state .primary-btn { margin-top: 1.5rem; }
</style>
