<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { setAuthSession } from '../../utils/auth'
import api from '../../services/api'

const router = useRouter()
const email = ref('')
const password = ref('')
const errorMessage = ref('')
const loading = ref(false)

async function login() {
  loading.value = true
  errorMessage.value = ''
  try {
    const response = await api.login({
      email: email.value,
      password: password.value,
    })
    setAuthSession(response.data.user, response.data.token)
    window.dispatchEvent(new Event('auth-changed'))
    router.push({ name: 'profile' })
  } catch (error) {
    if (error.response && error.response.status === 401) {
       errorMessage.value = 'Les identifiants sont incorrects.'
    } else {
       errorMessage.value = error.response?.data?.message || 'Une erreur est survenue.'
    }
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <v-form @submit.prevent="login">
    <v-alert v-if="errorMessage" type="error" dense class="mb-4">{{ errorMessage }}</v-alert>
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
    <v-btn :loading="loading" type="submit" color="primary" block>Se connecter</v-btn>
  </v-form>
</template>
