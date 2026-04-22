<script setup>
import { reactive, ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { getDashboardRouteByRole, saveAuthSession } from '../utils/auth'

const mode = ref('login')
const loading = ref(false)
const successMessage = ref('')
const errorMessage = ref('')
const router = useRouter()
const route = useRoute()

const form = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  role: 'client',
})

function resetMessages() {
  successMessage.value = ''
  errorMessage.value = ''
}

function syncModeFromRoute() {
  const requestedMode = route.query?.mode
  mode.value = requestedMode === 'register' ? 'register' : 'login'
}

syncModeFromRoute()
watch(() => route.query?.mode, syncModeFromRoute)

function normalizeError(payload) {
  if (!payload) return 'Erreur inconnue.'
  if (payload.message && !payload.errors) return payload.message
  if (payload.errors) {
    const firstField = Object.keys(payload.errors)[0]
    if (firstField && payload.errors[firstField]?.length) {
      return payload.errors[firstField][0]
    }
  }
  return 'Requete invalide.'
}

async function submitAuth() {
  resetMessages()
  loading.value = true

  try {
    const endpoint = mode.value === 'register' ? '/auth/register' : '/auth/login'
    const payload =
      mode.value === 'register'
        ? {
            name: form.name,
            email: form.email,
            password: form.password,
            password_confirmation: form.password_confirmation,
            role: form.role,
          }
        : {
            email: form.email,
            password: form.password,
          }

    const response = await fetch(endpoint, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
      },
      body: JSON.stringify(payload),
    })

    const data = await response.json().catch(() => null)

    if (!response.ok) {
      throw new Error(normalizeError(data))
    }

    saveAuthSession(data)

    const name = data?.user?.name || form.name || 'Utilisateur'
    successMessage.value =
      mode.value === 'register'
        ? `Compte cree avec succes pour ${name}.`
        : `Connexion reussie. Bienvenue ${name}.`

    // Check if there is a redirect query parameter
    if (route.query.redirect) {
      await router.push(route.query.redirect)
    } else {
      const targetRoute = getDashboardRouteByRole(data?.user?.role)
      await router.push(targetRoute)
    }
  } catch (err) {
    errorMessage.value = err?.message || 'Impossible de contacter le serveur.'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <section class="container auth-wrap reveal">
    <div class="auth-card">
      <div class="auth-switch">
        <button
          class="ghost-btn"
          :class="{ active: mode === 'login' }"
          @click="mode = 'login'; resetMessages()"
          type="button"
        >
          Connexion
        </button>
        <button
          class="ghost-btn"
          :class="{ active: mode === 'register' }"
          @click="mode = 'register'; resetMessages()"
          type="button"
        >
          Creer un compte
        </button>
      </div>

      <h1>{{ mode === 'register' ? 'Creer un compte' : 'Connexion' }}</h1>
      <p>
        {{
          mode === 'register'
            ? 'Inscris-toi pour reserver une villa et suivre tes sejours.'
            : 'Connecte-toi pour acceder a ton espace voyageur ou proprietaire.'
        }}
      </p>

      <form class="auth-form" @submit.prevent="submitAuth">
        <label v-if="mode === 'register'">
          Nom complet
          <input v-model="form.name" type="text" placeholder="Jean Dupont" required />
        </label>

        <label>
          Email
          <input v-model="form.email" type="email" placeholder="toi@email.com" required />
        </label>

        <label>
          Mot de passe
          <input v-model="form.password" type="password" placeholder="Password123" required />
        </label>

        <label v-if="mode === 'register'">
          Confirmation du mot de passe
          <input v-model="form.password_confirmation" type="password" placeholder="Password123" required />
        </label>

        <label v-if="mode === 'register'">
          Role
          <select v-model="form.role">
            <option value="client">Client</option>
            <option value="traveler">Voyageur</option>
            <option value="owner">Proprietaire</option>
          </select>
        </label>

        <button class="primary-btn" type="submit" :disabled="loading">
          {{ loading ? 'Chargement...' : mode === 'register' ? 'Creer mon compte' : 'Se connecter' }}
        </button>
      </form>

      <p v-if="successMessage" class="auth-success">{{ successMessage }}</p>
      <p v-if="errorMessage" class="auth-error">{{ errorMessage }}</p>
    </div>
  </section>
</template>
