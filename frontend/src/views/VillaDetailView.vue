<template>
  <v-container>
    <!-- Loading State -->
    <div v-if="loading" class="d-flex justify-center my-12">
      <v-progress-circular indeterminate color="primary" size="64"></v-progress-circular>
    </div>

    <!-- Error State -->
    <v-alert v-else-if="error" type="error" variant="tonal" class="my-6">
      {{ error }}
      <template v-slot:append>
        <v-btn color="error" variant="text" @click="fetchVilla">Réessayer</v-btn>
      </template>
    </v-alert>

    <!-- Main Content -->
    <div v-else-if="villa">
      <v-row>
        <!-- Titre et Navigation Parente -->
        <v-col cols="12" class="pb-0">
          <v-btn variant="text" color="primary" class="mb-4 px-0 pb-2" to="/villas">
            <v-icon start>mdi-arrow-left</v-icon>
            Retour aux villas
          </v-btn>
          <div class="d-flex align-center justify-space-between flex-wrap gap-4">
            <h1 class="text-h3 font-weight-bold text-grey-darken-4">{{ villa.title || villa.titre }}</h1>
            <v-chip color="primary" size="large" elevation="2" class="pa-4 text-h6 font-weight-bold">
              {{ villa.price_per_night || villa.prix_par_nuit || 150 }}€ <span class="text-subtitle-1 ml-1">/ nuit</span>
            </v-chip>
          </div>
          <div class="d-flex align-center text-subtitle-1 text-grey-darken-1 mt-3">
            <v-icon color="grey" class="mr-1">mdi-map-marker</v-icon>
            {{ villa.address || villa.adresse }}, {{ villa.city || villa.ville }}
          </div>
        </v-col>

        <!-- Galerie Images -->
        <v-col cols="12" class="mt-4">
          <v-card rounded="xl" elevation="3" class="overflow-hidden">
            <v-img
              :src="villa.photos && villa.photos.length > 0 ? villa.photos[0].url : `https://picsum.photos/seed/tunisia_villa_${(villa.id || 1) + 100}/1200/500`"
              height="450"
              cover
            ></v-img>
          </v-card>
        </v-col>
      </v-row>

      <v-row class="mt-8">
        <!-- Informations Détails -->
        <v-col cols="12" md="8">
          <v-card elevation="0" class="border rounded-lg h-100 pa-6">
            <h2 class="text-h5 font-weight-bold mb-4">À propos de ce logement</h2>
            <p class="text-body-1 text-medium-emphasis mb-8" style="line-height: 1.8;">
              {{ villa.description }}
            </p>

            <v-divider class="mb-8"></v-divider>

            <h3 class="text-h6 font-weight-bold mb-6">Équipements et Options</h3>
            <v-row v-if="optionsList.length > 0">
              <v-col
                v-for="(option, index) in optionsList"
                :key="index"
                cols="12"
                sm="6"
                class="py-2"
              >
                <div class="d-flex align-center">
                  <v-icon color="success" class="mr-3">mdi-check-circle-outline</v-icon>
                  <span class="text-body-1 text-grey-darken-2">{{ option.nom || option.name || option }}</span>
                </div>
              </v-col>
            </v-row>
            <p v-else class="text-body-2 text-grey">Aucune option renseignée.</p>
          </v-card>
        </v-col>

        <!-- Card de Réservation -->
        <v-col cols="12" md="4">
          <v-card elevation="4" class="rounded-xl sticky-card">
            <v-card-text class="pa-6">
              <h3 class="text-h5 font-weight-bold mb-2">Réserver</h3>
              <p class="text-body-2 text-medium-emphasis mb-6">
                Sélectionnez vos dates pour vérifier la disponibilité.
              </p>

              <!-- Simulation Date Picker -->
              <v-text-field
                label="Date d'arrivée"
                type="date"
                variant="outlined"
                density="comfortable"
                prepend-inner-icon="mdi-calendar-import"
                class="mb-2"
              ></v-text-field>

              <v-text-field
                label="Date de départ"
                type="date"
                variant="outlined"
                density="comfortable"
                prepend-inner-icon="mdi-calendar-export"
                class="mb-6"
              ></v-text-field>

              <v-divider class="mb-6"></v-divider>
              
              <div v-if="!isAuthenticated">
                <v-alert type="warning" variant="tonal" class="mb-4 text-left text-body-2">
                  <v-icon start>mdi-account-lock</v-icon>
                  Vous devez avoir un compte pour louer cette villa.
                </v-alert>
                <div class="d-flex flex-column gap-3">
                  <v-btn
                    color="primary"
                    block
                    size="large"
                    :to="{ name: 'login', query: { redirect: `/reservations/create?villaId=${villa.id}`, mode: 'register' } }"
                    elevation="2"
                  >
                    <v-icon start>mdi-account-plus</v-icon>
                    Créer un compte pour réserver
                  </v-btn>
                  <v-btn
                    variant="text"
                    color="primary"
                    block
                    size="small"
                    :to="{ name: 'login', query: { redirect: `/reservations/create?villaId=${villa.id}` } }"
                  >
                    Déjà un compte ? Se connecter
                  </v-btn>
                </div>
              </div>
              <div v-else>
                <v-btn
                  color="primary"
                  block
                  size="x-large"
                  :to="{ name: 'create-reservation', query: { villaId: villa.id } }"
                  elevation="3"
                >
                  Continuer la réservation
                  <v-icon end>mdi-arrow-right</v-icon>
                </v-btn>
              </div>
              
              <div class="text-center mt-4">
                <p class="text-caption text-grey">Aucun montant ne vous sera débité pour le moment.</p>
              </div>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </div>
  </v-container>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import api from '@/services/api'
import { getAuthUser } from '@/utils/auth'

const route = useRoute()
const villaId = route.params.id

const villa = ref(null)
const loading = ref(true)
const error = ref(null)

const user = ref(getAuthUser())
// Écouter si jamais le state d'auth change
window.addEventListener('auth-changed', () => {
  user.value = getAuthUser()
})

const isAuthenticated = computed(() => !!user.value)

const optionsList = computed(() => {
  if (!villa.value) return []
  return villa.value.options || villa.value.amenities || [
    'Piscine privée', 'Climatisation', 'Wi-Fi haut débit', 'Cuisine équipée', 'Parking gratuit'
  ]
})

const fetchVilla = async () => {
  loading.value = true
  error.value = null
  
  try {
    const response = await api.getVilla(villaId)
    villa.value = response.data
    if (!villa.value) {
        throw new Error("Villa introuvable")
    }
  } catch (err) {
    console.error(err)
    // Fallback Mock en cas d'absence d'API connectée pour simuler les villes tunisiennes
    const idInt = parseInt(villaId) || 1;
    const tunisianCities = ['Hammamet', 'Sousse', 'Djerba', 'Tunis', 'La Marsa', 'Sidi Bou Saïd', 'Carthage', 'Gammarth', 'Nabeul', 'Kélibia', 'Monastir', 'Mahdia', 'Tabarka', 'Bizerte', 'Tozeur', 'Douz', 'Sfax', 'Zarzis'];
    const city = tunisianCities[idInt % tunisianCities.length];
        
    villa.value = {
      id: idInt,
      title: `Résidence d'exception N°${idInt}`,
      description: "Découvrez cette splendide résidence conçue pour offrir un confort absolu en plein cœur de la Tunisie. Profitez des espaces généreux, de la grande terrasse ensoleillée et du cadre paisible qui entoure cette propriété. Parfaitement aménagée pour les familles et amis recherchant la détente et l'évasion sous le soleil.",
      adresse: "Quartier Résidentiel",
      ville: city,
      prix_par_nuit: 80 + ((idInt * 27) % 250),
      options: ['Climatisation globale', 'Piscine privée', 'Jardin arboré', 'Vue dégagée', 'Wi-Fi haut débit', 'Barbecue', 'Parking sécurisé']
    }
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchVilla()
})
</script>

<style scoped>
.sticky-card {
  position: -webkit-sticky;
  position: sticky;
  top: 100px;
  z-index: 5;
}
</style>