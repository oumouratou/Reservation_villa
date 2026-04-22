<template>
  <div>
    <h1>Créer une réservation</h1>
    <form @submit.prevent="createReservation">
      <div>
        <label for="start_date">Date de début:</label>
        <input type="date" id="start_date" v-model="start_date" required>
      </div>
      <div>
        <label for="end_date">Date de fin:</label>
        <input type="date" id="end_date" v-model="end_date" required>
      </div>
      <button type="submit">Réserver</button>
    </form>
  </div>
</template>

<script>
import api from '@/services/api';

export default {
  name: 'CreateReservationView',
  data() {
    return {
      start_date: '',
      end_date: '',
      error: null,
      success: null,
    };
  },
  methods: {
    async createReservation() {
      this.error = null;
      this.success = null;
      try {
        await api.createReservation({
          villa_id: this.$route.params.id, // Assurez-vous que l'ID de la villa est disponible dans la route
          date_debut: this.start_date,
          date_fin: this.end_date,
        });
        this.success = 'Votre demande de réservation a été envoyée.';
      } catch (error) {
        this.error = 'Une erreur est survenue lors de la réservation.';
        console.error(error);
      }
    },
  },
};
</script>