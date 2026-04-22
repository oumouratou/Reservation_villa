<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { setAuthSession } from '../../utils/auth'
import api from '../../services/api'

const router = useRouter()
const name = ref('')
const email = ref('')
const password = ref('')
const password_confirmation = ref('')
const errorMessage = ref('')
const loading = ref(false)

async function register() {
  loading.value = true
  errorMessage.value = ''
  try {
    const response = await api.register({
      name: name.value,
      email: email.value,
      password: password.value,
      password_confirmation: password_confirmation.value,
    })
    setAuthSession(response.data.user, response.data.token)
    window.dispatchEvent(new Event('auth-changed'))
    router.push({ name: 'profile' })
  } catch (error) {
    if (error.response && error.response.status === 422) {
       errorMessage.value = 'Veuillez vérifier les informations saisies.'
    } else {
       errorMessage.value = error.response?.data?.message || 'Une erreur est survenue.'
    }
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <v-form @submit.prevent="register">
    <v-alert v-if="errorMessage" type="error" dense class="mb-4">{{ errorMessage }}</v-alert>
    <v-text-field
      v-model="name"
      label="Nom complet"
      required
      class="mb-2"
    ></v-text-field>
    <v-text-field
      v-model="email"
      label="Email"
      type="email"
      required
      class="mb-2"
    ></v-text-field>
    <v-text-field
      v-model="password"
      label="Mot de passe"
      type="password"
      required
      class="mb-2"
    ></v-text-field>
    <v-text-field
      v-model="password_confirmation"
      label="Confirmer le mot de passe"
      type="password"
      required
      class="mb-2"
    ></v-text-field>
    <v-btn :loading="loading" type="submit" color="primary" block>S'inscrire</v-btn>
  </v-form>
</template>
