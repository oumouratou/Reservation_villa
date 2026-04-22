<template>
  <v-container>
    <div class="d-flex align-center justify-space-between mb-6">
      <h1 class="text-h4 font-weight-bold text-primary">Nos Villas</h1>
    </div>

    <!-- Filtres -->
    <v-card class="mb-8 rounded-lg elevation-2">
      <v-card-text>
        <v-row>
          <v-col cols="12" md="5">
            <v-text-field
              v-model="cityFilter"
              label="Filtrer par ville"
              placeholder="ex: Dakar"
              prepend-inner-icon="mdi-map-marker"
              variant="outlined"
              density="comfortable"
              hide-details
            ></v-text-field>
          </v-col>
          <v-col cols="12" md="5">
            <v-text-field
              v-model="priceFilter"
              label="Prix maximum (€)"
              placeholder="ex: 350"
              type="number"
              prepend-inner-icon="mdi-currency-eur"
              variant="outlined"
              density="comfortable"
              hide-details
            ></v-text-field>
          </v-col>
          <v-col cols="12" md="2" class="d-flex align-center">
            <v-btn color="primary" block size="large" @click="resetFilters">
              <v-icon start>mdi-refresh</v-icon>
              Réinitialiser
            </v-btn>
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>

    <div v-if="loading" class="d-flex justify-center my-12">
      <v-progress-circular indeterminate color="primary" size="64"></v-progress-circular>
    </div>

    <div v-else-if="filteredVillas.length === 0" class="text-center py-12">
      <v-icon size="64" color="grey-lighten-1" class="mb-4">mdi-home-search</v-icon>
      <h3 class="text-h6 text-grey-darken-1">Aucune villa ne correspond à vos critères.</h3>
      <v-btn class="mt-4" color="primary" variant="text" @click="resetFilters">Voir toutes les villas</v-btn>
    </div>

    <!-- Liste des Villas -->
    <v-row v-else>
      <v-col v-for="villa in filteredVillas" :key="villa.id" cols="12" md="6" lg="4">
        <v-card class="h-100 d-flex flex-column rounded-lg elevation-3 hover-card">
          <v-img
            :src="villa.photos && villa.photos.length > 0 ? villa.photos[0].url : `https://picsum.photos/seed/${villa.id}/800/600`"
            height="250"
            cover
            class="align-end"
          >
            <div class="d-flex justify-end pa-3">
              <v-chip color="white" text-color="primary" class="font-weight-bold" elevation="2">
                {{ villa.price_per_night || villa.price || '120' }}€ / nuit
              </v-chip>
            </div>
          </v-img>

          <v-card-item class="flex-grow-1 pt-4">
            <div class="text-h6 font-weight-bold mb-1 line-clamp-1">{{ villa.title || villa.titre }}</div>
            <div class="d-flex align-center text-subtitle-2 text-grey-darken-1 mb-3">
              <v-icon size="small" class="mr-1">mdi-map-marker</v-icon>
              {{ villa.city || villa.ville || 'Dakar' }}
            </div>
            
            <p class="text-body-2 text-medium-emphasis line-clamp-3">
              {{ villa.description || 'Magnifique villa offrant tout le confort nécessaire pour un séjour inoubliable.' }}
            </p>
          </v-card-item>

          <v-divider></v-divider>

          <v-card-actions class="pa-4">
            <v-rating
              :model-value="4.5"
              color="amber"
              density="compact"
              size="small"
              half-increments
              readonly
            ></v-rating>
            <span class="text-caption ml-2 text-grey">(12 avis)</span>
            <v-spacer></v-spacer>
            <v-btn
              color="primary"
              variant="flat"
              :to="`/villas/${villa.id}`"
            >
              Voir détails
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '@/services/api'

const villas = ref([])
const loading = ref(true)
const cityFilter = ref('')
const priceFilter = ref(null)

const fetchVillas = async () => {
  loading.value = true
  try {
    const response = await api.getVillas()
    villas.value = response.data
    
    // Si l'API retourne un tableau vide (pas encore de données en base), on mock
    if (villas.value.length === 0) {
      generateMockVillas()
    }
  } catch (error) {
    console.error(error)
    generateMockVillas()
  } finally {
    loading.value = false
  }
}

const generateMockVillas = () => {
  const tunisianCities = [
    'Hammamet', 'Sousse', 'Djerba', 'Tunis', 'La Marsa', 'Sidi Bou Saïd',
    'Carthage', 'Gammarth', 'Nabeul', 'Kélibia', 'Monastir', 'Mahdia',
    'Tabarka', 'Bizerte', 'Tozeur', 'Douz', 'Sfax', 'Zarzis', 'El Haouaria', 'Korba'
  ];
  
  const villaNouns = ['Villa', 'Dar', 'Résidence', 'Maison', 'Palais', 'Domaine'];
  const villaSuffixes = ['Jasmin', 'Azur', 'de la Plage', 'des Palmiers', 'Oasis', 'Mirage', 'Méditerranée', 'du Golfe', 'des Oliviers', 'Sérénité', 'La Blanche', 'Du Soleil'];
  const villaAdjectives = ['Majestueuse', 'Luxueuse', 'Charmante', 'Moderne', 'Traditionnelle', 'Spacieuse', 'Magnifique', 'Prestigieuse', 'Élégante', 'Authentique'];

  const generated = [];
  for (let i = 1; i <= 36; i++) {
    const city = tunisianCities[i % tunisianCities.length];
    const noun = villaNouns[i % villaNouns.length];
    const adj = villaAdjectives[(i * 3) % villaAdjectives.length];
    const suff = villaSuffixes[(i * 5) % villaSuffixes.length];
    const price = 80 + ((i * 27) % 250); // Prix varié entre 80€ et ~330€

    const title = i % 2 === 0 ? `${noun} ${adj}` : `${noun} ${suff}`;
    
    generated.push({
      id: i,
      title: title,
      city: city,
      price_per_night: price,
      description: `Séjournez dans ce bien d'exception idéalement situé à ${city}. Profitez d'un cadre idyllique avec toutes les commodités modernes, une belle décoration et un accès rapide aux lieux d'intérêt de la Tunisie.`,
      photos: [{ url: `https://picsum.photos/seed/tunisia_villa_${i + 100}/800/600` }]
    });
  }
  
  villas.value = generated;
}

onMounted(() => {
  fetchVillas()
})

const filteredVillas = computed(() => {
  let result = villas.value

  if (cityFilter.value) {
    const term = cityFilter.value.toLowerCase()
    result = result.filter(v => (v.city || v.ville || '').toLowerCase().includes(term))
  }

  if (priceFilter.value) {
    const maxPrice = Number(priceFilter.value)
    result = result.filter(v => {
      const price = Number(v.price_per_night || v.price || 0)
      return price <= maxPrice
    })
  }

  return result
})

const resetFilters = () => {
  cityFilter.value = ''
  priceFilter.value = null
}
</script>

<style scoped>
.line-clamp-1 {
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;  
  overflow: hidden;
}

.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;  
  overflow: hidden;
}

.hover-card {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.hover-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15) !important;
}
</style>
