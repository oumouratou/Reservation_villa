<template>
  <div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Mon Profil</h1>

    <div class="bg-white p-6 rounded-lg shadow-md mb-6">
      <h2 class="text-xl font-semibold mb-4">Informations personnelles</h2>
      <form @submit.prevent="updateProfile">
        <div class="mb-4">
          <label for="name" class="block text-gray-700">Nom</label>
          <input type="text" id="name" v-model="user.name" class="w-full px-4 py-2 border rounded-lg" />
        </div>
        <div class="mb-4">
          <label for="email" class="block text-gray-700">Email</label>
          <input type="email" id="email" v-model="user.email" class="w-full px-4 py-2 border rounded-lg" disabled />
        </div>
        <div class="mb-4">
          <label for="phone" class="block text-gray-700">Téléphone</label>
          <input type="text" id="phone" v-model="user.phone" class="w-full px-4 py-2 border rounded-lg" />
        </div>
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg">Mettre à jour</button>
      </form>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md">
      <h2 class="text-xl font-semibold mb-4">Changer le mot de passe</h2>
      <form @submit.prevent="updatePassword">
        <div class="mb-4">
          <label for="password" class="block text-gray-700">Nouveau mot de passe</label>
          <input type="password" id="password" v-model="passwordForm.password" class="w-full px-4 py-2 border rounded-lg" />
        </div>
        <div class="mb-4">
          <label for="password_confirmation" class="block text-gray-700">Confirmer le mot de passe</label>
          <input type="password" id="password_confirmation" v-model="passwordForm.password_confirmation" class="w-full px-4 py-2 border rounded-lg" />
        </div>
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg">Changer le mot de passe</button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const user = ref({});
const passwordForm = ref({
  password: '',
  password_confirmation: '',
});

const fetchUser = async () => {
  try {
    const response = await axios.get('/api/profile');
    user.value = response.data;
  } catch (error) {
    console.error('Erreur lors de la récupération du profil', error);
  }
};

const updateProfile = async () => {
  try {
    await axios.put('/api/profile', {
      name: user.value.name,
      phone: user.value.phone,
    });
    alert('Profil mis à jour avec succès');
  } catch (error) {
    console.error('Erreur lors de la mise à jour du profil', error);
  }
};

const updatePassword = async () => {
  try {
    await axios.put('/api/profile', passwordForm.value);
    alert('Mot de passe mis à jour avec succès');
    passwordForm.value.password = '';
    passwordForm.value.password_confirmation = '';
  } catch (error) {
    console.error('Erreur lors de la mise à jour du mot de passe', error);
  }
};

onMounted(fetchUser);
</script>
