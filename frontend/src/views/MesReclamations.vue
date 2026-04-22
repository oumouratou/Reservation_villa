<template>
  <div class="container reclamations-list-view">
    <div class="view-header">
      <div>
        <h1 class="view-title">Mes Réclamations</h1>
        <p class="view-subtitle">Historique de vos réclamations et de leurs réponses.</p>
      </div>
      <router-link :to="{ name: 'deposer-reclamation' }" class="primary-btn">
        Nouvelle réclamation
      </router-link>
    </div>

    <div v-if="loading" class="loading-state">
      <p>Chargement de vos réclamations...</p>
    </div>

    <div v-if="error" class="error-state">
      <p>{{ error }}</p>
    </div>

    <div v-if="!loading && reclamations.length === 0" class="empty-state">
      <p>Vous n'avez aucune réclamation pour le moment.</p>
    </div>

    <div v-if="!loading && reclamations.length > 0" class="reclamations-list">
      <div v-for="reclamation in reclamations" :key="reclamation.id" class="reclamation-card">
        <div class="card-header">
          <h3>{{ reclamation.sujet }}</h3>
          <span :class="['status-badge', getStatusClass(reclamation.statut)]">{{ formatStatus(reclamation.statut) }}</span>
        </div>
        <div class="card-body">
          <p><strong>Date de dépôt :</strong> {{ formatDate(reclamation.created_at) }}</p>
          <p><strong>Description :</strong> {{ reclamation.description }}</p>
          <div v-if="reclamation.reponse_agent" class="agent-response">
            <strong>Réponse de l'agent :</strong>
            <p>{{ reclamation.reponse_agent }}</p>
          </div>
           <div v-else class="agent-response-pending">
            <p>Un agent traitera votre demande prochainement.</p>
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
const reclamations = ref([]);
const loading = ref(true);
const error = ref(null);

async function fetchReclamations() {
  loading.value = true;
  error.value = null;
  try {
    const response = await api.get('/client/reclamations');
    reclamations.value = response.data;
  } catch (e) {
    console.error("Erreur lors de la récupération des réclamations:", e);
    error.value = "Impossible de charger vos réclamations.";
  } finally {
    loading.value = false;
  }
}

function getStatusClass(status) {
  switch (status) {
    case 'ouverte': return 'status-open';
    case 'en cours': return 'status-in-progress';
    case 'traitee': return 'status-closed';
    default: return '';
  }
}

function formatStatus(status) {
    return status.charAt(0).toUpperCase() + status.slice(1);
}

function formatDate(dateString) {
  const options = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
  return new Date(dateString).toLocaleDateString('fr-FR', options);
}

onMounted(fetchReclamations);
</script>

<style scoped>
.reclamations-list-view { padding: 2rem 0; }
.view-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 3rem; }
.view-title { font-size: 2.5rem; margin: 0; }
.view-subtitle { color: #666; margin: 0; }

.reclamations-list { display: flex; flex-direction: column; gap: 1.5rem; max-width: 900px; margin: auto; }
.reclamation-card { background: #fff; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.08); }
.card-header { display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.5rem; border-bottom: 1px solid #eee; }
.card-header h3 { margin: 0; font-size: 1.4rem; }
.card-body { padding: 1.5rem; }
.card-body p { margin: 0 0 0.8rem 0; }

.status-badge { padding: 0.3rem 0.8rem; border-radius: 15px; font-weight: bold; color: #fff; }
.status-open { background-color: #ffc107; }
.status-in-progress { background-color: #17a2b8; }
.status-closed { background-color: #28a745; }

.agent-response { margin-top: 1rem; padding: 1rem; background: #e9f7ef; border-left: 4px solid #28a745; border-radius: 4px; }
.agent-response p { margin: 0.5rem 0 0 0; }
.agent-response-pending { margin-top: 1rem; padding: 1rem; background: #f8f9fa; border-left: 4px solid #6c757d; border-radius: 4px; font-style: italic; }


.loading-state, .error-state, .empty-state { text-align: center; padding: 4rem 0; font-size: 1.2rem; }
</style>
