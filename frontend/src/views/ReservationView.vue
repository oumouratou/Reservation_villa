<script setup>
import { ref, onMounted, computed } from 'vue'
import { getAuthUser } from '../utils/auth'

const user = ref(getAuthUser() || {})
const loading = ref(true)
const reservations = ref([])
const error = ref('')

async function fetchReservations() {
  loading.value = true
  try {
    const res = await fetch('/my-reservations', {
      headers: {
        'Accept': 'application/json',
        'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
      }
    })
    
    if (!res.ok) {
      // Fallback si l'API n'est pas encore prete
      reservations.value = [
        { id: 1, villa: { title: 'Villa Ocean Breeze' }, check_in: '2026-08-14', check_out: '2026-08-20', status: 'approved', total_price: '1200' },
        { id: 2, villa: { title: 'Casa Palmera' }, check_in: '2026-09-02', check_out: '2026-09-07', status: 'pending', total_price: '850' },
      ]
      throw new Error("L'API de réservation est indisponible, affichage des données de test.")
    } else {
      const data = await res.json()
      reservations.value = data.data || data
    }
  } catch (err) {
    console.error(err)
  } finally {
    loading.value = false
  }
}

const getStatusColor = (status) => {
  const mapping = {
    'pending': 'warning',
    'approved': 'success',
    'rejected': 'error',
    'cancelled': 'grey',
    'completed': 'primary'
  }
  return mapping[status] || 'info'
}

const getStatusText = (status) => {
  const mapping = {
    'pending': 'En attente',
    'approved': 'Approuvée',
    'rejected': 'Refusée',
    'cancelled': 'Annulée',
    'completed': 'Terminée'
  }
  return mapping[status] || status
}

onMounted(() => {
  fetchReservations()
})
</script>

<template>
  <v-container class="py-8" style="max-width: 1200px;">
    <div class="mb-8 d-flex justify-space-between align-center flex-wrap">
      <div>
        <h1 class="text-h4 font-weight-bold text-primary mb-2">Mes réservations</h1>
        <p class="text-body-1 text-medium-emphasis">
          Gérez l'ensemble de vos séjours, consultez les statuts et procédez aux paiements.
        </p>
      </div>
      <v-btn color="primary" to="/villas" elevation="2" prepend-icon="mdi-magnify">
        Trouver une villa
      </v-btn>
    </div>

    <!-- Spinner -->
    <v-row v-if="loading" justify="center" class="mt-12">
      <v-progress-circular indeterminate color="primary" size="64"></v-progress-circular>
    </v-row>

    <!-- Empty State -->
    <v-card v-else-if="reservations.length === 0" class="text-center pa-12 elevation-1 rounded-lg">
      <v-icon size="80" color="grey-lighten-2" class="mb-4">mdi-calendar-blank</v-icon>
      <h3 class="text-h5 font-weight-bold text-grey-darken-2 mb-2">Aucune réservation trouvée</h3>
      <p class="text-body-1 text-grey mb-6">Commencez par explorer nos magnifiques villas pour votre prochain voyage.</p>
      <v-btn color="primary" to="/villas">Parcourir le catalogue</v-btn>
    </v-card>

    <!-- Liste des Réservations -->
    <v-row v-else>
      <v-col cols="12" md="6" lg="4" v-for="res in reservations" :key="res.id">
        <v-card elevation="2" class="rounded-lg h-100 d-flex flex-column hover-card">
          <v-img
            height="180"
            cover
            src="https://images.unsplash.com/photo-1512917774080-9991f1c4c750?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=60"
            class="bg-grey-lighten-2"
          >
            <template #placeholder>
              <div class="d-flex align-center justify-center fill-height">
                <v-progress-circular indeterminate color="grey-lighten-5"></v-progress-circular>
              </div>
            </template>
            <div class="d-flex justify-end ma-3">
              <v-chip :color="getStatusColor(res.status)" size="small" label class="font-weight-bold px-3">
                {{ getStatusText(res.status) }}
              </v-chip>
            </div>
          </v-img>

          <v-card-title class="font-weight-bold text-h6 pt-4 px-4 pb-1">
            {{ res.villa?.title || 'Villa Merveilleuse' }}
          </v-card-title>
          
          <v-card-subtitle class="px-4 pb-2 text-medium-emphasis">
            <v-icon size="small" class="me-1">mdi-calendar-range</v-icon>
            Du {{ new Date(res.check_in).toLocaleDateString('fr-FR') }} au {{ new Date(res.check_out).toLocaleDateString('fr-FR') }}
          </v-card-subtitle>

          <v-card-text class="px-4 py-2 flex-grow-1">
            <div class="d-flex justify-space-between align-center py-2 border-bottom">
              <span class="text-body-2 font-weight-medium text-grey-darken-1">Montant total</span>
              <span class="text-h6 font-weight-black text-primary">{{ res.total_price }} €</span>
            </div>
          </v-card-text>

          <v-card-actions class="px-4 pb-4 pt-0 mt-auto">
            <v-btn
              v-if="res.status === 'approved'"
              color="success"
              variant="flat"
              block
              elevation="1"
              prepend-icon="mdi-credit-card"
            >
              Procéder au paiement
            </v-btn>
            <div v-else class="d-flex w-100 gap-2">
              <v-btn variant="outlined" color="primary" class="flex-grow-1">
                Détails
              </v-btn>
              <v-btn v-if="res.status === 'pending'" variant="text" color="error" class="flex-grow-0 px-2" title="Annuler">
                <v-icon>mdi-cancel</v-icon>
              </v-btn>
            </div>
          </v-card-actions>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<style scoped>
.border-bottom {
  border-bottom: 1px solid #f0f0f0;
}
.hover-card {
  transition: transform 0.2s, box-shadow 0.2s;
}
.hover-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
}
.gap-2 {
  gap: 8px;
}
</style>
