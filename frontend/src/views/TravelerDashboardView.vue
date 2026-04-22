<script setup>
import { ref } from 'vue'

const stats = ref([
  { title: 'Réservations actives', value: '0', icon: 'mdi-calendar-check', desc: 'Secteur en attente' },
  { title: 'Prochains départs', value: '0', icon: 'mdi-airplane-takeoff', desc: 'Prochaines 4 semaines' },
  { title: 'Paiements validés', value: '0', icon: 'mdi-credit-card-check-outline', desc: 'Mois dernier' },
  { title: 'Messages non lus', value: '0', icon: 'mdi-email-alert', desc: 'De la part de propriétaires' },
])
</script>

<template>
  <v-container class="py-8" style="max-width: 1200px;">
    <div class="mb-8">
      <h1 class="text-h4 font-weight-bold text-primary mb-2">Tableau de bord Voyageur</h1>
      <p class="text-body-1 text-medium-emphasis">
        Retrouvez vos réservations, paiements et messages en un seul endroit simple et accessible.
      </p>
    </div>

    <!-- Statistiques Rapides -->
    <v-row class="mb-8">
      <v-col cols="12" sm="6" md="3" v-for="(stat, i) in stats" :key="i">
        <v-card elevation="2" class="rounded-lg h-100 pa-4 
            d-flex flex-column align-center justify-center text-center
            hover-card-elevate transition-swing">
          <v-avatar color="primary-lighten-4" size="56" class="mb-3">
            <v-icon size="32" color="primary">{{ stat.icon }}</v-icon>
          </v-avatar>
          <h3 class="text-h4 font-weight-black text-primary mb-1">{{ stat.value }}</h3>
          <p class="text-button font-weight-medium mb-1">{{ stat.title }}</p>
          <span class="text-caption text-medium-emphasis">{{ stat.desc }}</span>
        </v-card>
      </v-col>
    </v-row>

    <!-- Actions Rapides -->
    <v-card class="elevation-3 rounded-lg overflow-hidden mt-6">
      <v-card-text class="pa-10 text-center">
        <v-icon size="64" color="grey-lighten-1" class="mb-4">mdi-compass-outline</v-icon>
        <h3 class="text-h5 font-weight-bold mb-3">Prêt pour votre prochaine aventure ?</h3>
        <p class="text-body-1 text-medium-emphasis mb-6 max-w-600 mx-auto" style="max-width: 600px;">
          Consultez vos réservations existantes, préparez votre voyage, réglez vos factures, prenez contact avec vos hôtes ou déposez une réclamation si nécessaire.
        </p>
        <div class="d-flex justify-center flex-wrap gap-4" style="gap: 16px;">
          <v-btn color="primary" size="large" to="/reservations" elevation="2">
            <v-icon start>mdi-calendar-search</v-icon>
            Gérer mes réservations
          </v-btn>
          <v-btn variant="outlined" size="large" to="/messages" class="bg-white" color="secondary">
            <v-icon start>mdi-forum</v-icon>
            Accéder à la messagerie
          </v-btn>
          <v-btn variant="outlined" size="large" to="/reclamations" class="bg-white" color="warning">
            <v-icon start>mdi-alert-circle</v-icon>
            Réclamations
          </v-btn>
          <v-btn variant="text" size="large" to="/villas" color="primary">
            Explorer les villas
            <v-icon end>mdi-arrow-right</v-icon>
          </v-btn>
        </div>
      </v-card-text>
    </v-card>
  </v-container>
</template>

<style scoped>
.hover-card-elevate:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1) !important;
}
</style>
