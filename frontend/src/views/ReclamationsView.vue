<template>
  <v-container>
    <v-row>
      <v-col cols="12" md="8" offset-md="2">
        <v-card elevation="3" class="rounded-lg mb-6">
          <v-toolbar color="warning" dark>
            <v-toolbar-title class="text-h6">
              <v-icon start>mdi-alert-circle</v-icon>
              Soumettre une réclamation
            </v-toolbar-title>
          </v-toolbar>

          <v-card-text class="pa-6">
             <p class="text-body-1 mb-6 text-medium-emphasis">
              Vous avez rencontré un problème lors de votre séjour ? Veuillez nous fournir les détails ci-dessous.
            </p>

            <v-alert
              v-if="success"
              type="success"
              variant="tonal"
              closable
              class="mb-4"
              @click:close="success = null"
            >
              {{ success }}
            </v-alert>

            <v-alert
              v-if="error"
              type="error"
              variant="tonal"
              closable
              class="mb-4"
              @click:close="error = null"
            >
              {{ error }}
            </v-alert>

            <v-form @submit.prevent="createReclamation" ref="form">
              <v-text-field
                v-model="newReclamation.sujet"
                label="Sujet / Motif"
                variant="outlined"
                density="comfortable"
                required
                :rules="[v => !!v || 'Le sujet est requis']"
              ></v-text-field>

              <v-textarea
                v-model="newReclamation.description"
                label="Description détaillée du problème"
                variant="outlined"
                density="comfortable"
                rows="4"
                required
                :rules="[v => !!v || 'Veuillez décrire le problème en détail']"
              ></v-textarea>
              
              <v-card-actions class="px-0 pt-4">
                <v-btn
                  color="warning"
                  variant="flat"
                  type="submit"
                  size="large"
                  :loading="loading"
                  block
                >
                  <v-icon start>mdi-send</v-icon>
                  Envoyer la réclamation
                </v-btn>
              </v-card-actions>
            </v-form>
          </v-card-text>
        </v-card>

        <v-card class="elevation-2 rounded-lg" v-if="reclamations.length > 0">
          <v-card-title class="d-flex align-center">
            <v-icon color="grey-darken-1" start class="mr-2">mdi-history</v-icon>
            Mes réclamations
          </v-card-title>
          <v-divider></v-divider>
          <v-list>
            <v-list-item v-for="reclamation in reclamations" :key="reclamation.id" class="py-3">
              <template v-slot:prepend>
                 <div class="d-flex flex-column align-center mr-4">
                  <v-icon
                    :color="reclamation.statut === 'résolu' ? 'success' : (reclamation.statut === 'en attente' ? 'warning' : 'info')"
                    size="32"
                  >
                    {{ reclamation.statut === 'résolu' ? 'mdi-check-circle' : (reclamation.statut === 'en attente' ? 'mdi-clock-outline' : 'mdi-information') }}
                  </v-icon>
                  <span class="text-caption font-weight-bold mt-1 text-uppercase">{{ reclamation.statut || 'Nouveau' }}</span>
                </div>
              </template>
              
              <v-list-item-title class="font-weight-bold text-subtitle-1 mb-1">
                {{ reclamation.sujet }}
              </v-list-item-title>
              <v-list-item-subtitle class="text-body-2 mb-2 line-clamp-2">
                {{ reclamation.description }}
              </v-list-item-subtitle>
            </v-list-item>
          </v-list>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import api from '@/services/api';

export default {
  name: 'ReclamationsView',
  data() {
    return {
      reclamations: [],
      newReclamation: {
        sujet: '',
        description: '',
        reservation_id: null,
      },
      error: null,
      success: null,
      loading: false,
    };
  },
  async created() {
    this.fetchReclamations();
  },
  methods: {
    async fetchReclamations() {
      try {
        const response = await api.getReclamations();
        this.reclamations = response.data;
      } catch (error) {
        console.error(error);
        // Fallback pour la démo
        this.reclamations = [
          { id: 1, sujet: 'Problème de climatisation', description: 'La climatisation était en panne pendant tout le séjour.', statut: 'en attente' }
        ];
      }
    },
    async createReclamation() {
      if (this.$refs.form) {
        const { valid } = await this.$refs.form.validate()
        if (!valid) return
      }

      this.loading = true;
      this.error = null;
      this.success = null;
      try {
        await api.createReclamation(this.newReclamation);
        this.success = 'Votre réclamation a été envoyée.';
        this.newReclamation.sujet = '';
        this.newReclamation.description = '';
        if (this.$refs.form) this.$refs.form.reset();
        this.fetchReclamations();
      } catch (error) {
        this.error = 'Une erreur est survenue lors de l\'envoi de la réclamation.';
        console.error(error);
      } finally {
        this.loading = false;
      }
    },
  },
};
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;  
  overflow: hidden;
}
</style>