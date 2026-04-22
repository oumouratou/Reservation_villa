<template>
  <div class="container profile-view">
    <h1>Mon Profil</h1>
    <p>Mettez à jour vos informations personnelles ici.</p>

    <form @submit.prevent="updateProfile" class="profile-form">
      <div class="form-grid">
        <label>
          Prénom
          <input v-model="form.prenom" type="text" placeholder="Votre prénom" required />
        </label>
        <label>
          Nom
          <input v-model="form.nom" type="text" placeholder="Votre nom" required />
        </label>
      </div>

      <label>
        Email
        <input v-model="form.email" type="email" placeholder="Votre email" required />
      </label>

      <label>
        Téléphone
        <input v-model="form.telephone" type="tel" placeholder="Votre numéro de téléphone" />
      </label>

      <button class="primary-btn" type="submit" :disabled="loading">
        {{ loading ? 'Sauvegarde...' : 'Sauvegarder les modifications' }}
      </button>
    </form>

    <p v-if="successMessage" class="success-message">{{ successMessage }}</p>
    <p v-if="errorMessage" class="error-message">{{ errorMessage }}</p>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { useApi } from '@/utils/api'; // Assurez-vous que ce chemin est correct

const api = useApi();
const loading = ref(false);
const successMessage = ref('');
const errorMessage = ref('');

const form = reactive({
  nom: '',
  prenom: '',
  email: '',
  telephone: '',
});

// Sépare le nom complet en prénom et nom
function splitFullName(fullName) {
  if (!fullName) return { prenom: '', nom: '' };
  const parts = fullName.split(' ');
  const prenom = parts.shift() || '';
  const nom = parts.join(' ');
  return { prenom, nom };
}

async function fetchProfile() {
  loading.value = true;
  errorMessage.value = '';
  try {
    const response = await api.get('/client/profile');
    const user = response.data;
    const { prenom, nom } = splitFullName(user.name);
    
    form.prenom = prenom;
    form.nom = nom;
    form.email = user.email;
    form.telephone = user.phone;
  } catch (error) {
    errorMessage.value = 'Erreur lors de la récupération de votre profil.';
    console.error("Fetch Profile Error:", error);
  } finally {
    loading.value = false;
  }
}

async function updateProfile() {
  loading.value = true;
  successMessage.value = '';
  errorMessage.value = '';
  try {
    await api.put('/client/profile', form);
    successMessage.value = 'Votre profil a été mis à jour avec succès !';
  } catch (error) {
    errorMessage.value = 'Une erreur est survenue lors de la mise à jour.';
    console.error("Update Profile Error:", error);
  } finally {
    loading.value = false;
  }
}

onMounted(fetchProfile);
</script>

<style scoped>
.profile-view {
  max-width: 600px;
  margin: 2rem auto;
  padding: 2rem;
  background-color: #fff;
  border-radius: 8px;
  box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.profile-form {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.5rem;
}

.success-message {
  color: green;
  margin-top: 1rem;
}

.error-message {
  color: red;
  margin-top: 1rem;
}
</style>
