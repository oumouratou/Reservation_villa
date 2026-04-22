<script setup>
import { ref, onMounted } from 'vue'
import { getAuthUser, saveAuthSession } from '../utils/auth'

const user = ref(getAuthUser() || {})
const loading = ref(false)
const successMsg = ref('')
const errorMsg = ref('')
const activeTab = ref('profile')

const profileForm = ref({
  name: user.value.name || '',
  email: user.value.email || '',
  phone: user.value.phone || '',
  bio: user.value.bio || '',
})

const passwordForm = ref({
  current_password: '',
  password: '',
  password_confirmation: '',
})

async function fetchProfile() {
  try {
    const res = await fetch('/auth/me', {
      headers: {
        'Accept': 'application/json',
        'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
      }
    })
    if (res.ok) {
      const data = await res.json()
      user.value = data
      profileForm.value = { ...data }
      saveAuthSession({ user: data })
    }
  } catch (e) {
    console.error('Erreur profil', e)
  }
}

async function updateProfile() {
  loading.value = true
  successMsg.value = ''
  errorMsg.value = ''
  try {
    const res = await fetch('/auth/profile', {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
      },
      body: JSON.stringify({
        name: profileForm.value.name,
        phone: profileForm.value.phone,
        bio: profileForm.value.bio
      })
    })

    const data = await res.json()
    if (!res.ok) throw new Error(data.message || 'Erreur lors de la mise à jour.')
    
    successMsg.value = 'Profil mis à jour avec succès.'
    user.value = data.user
    saveAuthSession({ user: data.user })
  } catch (err) {
    errorMsg.value = err.message
  } finally {
    loading.value = false
  }
}

async function updatePassword() {
  loading.value = true
  successMsg.value = ''
  errorMsg.value = ''
  try {
    const res = await fetch('/auth/password', {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
      },
      body: JSON.stringify(passwordForm.value)
    })

    const data = await res.json()
    if (!res.ok) throw new Error(data.message || 'Erreur lors du changement de mot de passe.')
    
    successMsg.value = 'Mot de passe modifié avec succès.'
    passwordForm.value = { current_password: '', password: '', password_confirmation: '' }
  } catch (err) {
    errorMsg.value = err.message
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchProfile()
})
</script>

<template>
  <v-container class="py-8" style="max-width: 900px;">
    <div class="mb-6">
      <h1 class="text-h4 font-weight-bold text-primary mb-2">Mieux vous connaître</h1>
      <p class="text-body-1 text-medium-emphasis">Gérez vos informations personnelles et vos paramètres de sécurité depuis cet espace.</p>
    </div>

    <v-card class="elevation-2 rounded-lg">
      <v-tabs v-model="activeTab" color="primary" bg-color="grey-lighten-4">
        <v-tab value="profile">
          <v-icon start>mdi-account-edit</v-icon>
          Informations du profil
        </v-tab>
        <v-tab value="security">
          <v-icon start>mdi-shield-lock</v-icon>
          Sécurité & Mot de passe
        </v-tab>
      </v-tabs>

      <v-card-text class="pa-6">
        <v-alert v-if="successMsg" type="success" variant="tonal" class="mb-6" closable @click:close="successMsg = ''">
          {{ successMsg }}
        </v-alert>
        
        <v-alert v-if="errorMsg" type="error" variant="tonal" class="mb-6" closable @click:close="errorMsg = ''">
          {{ errorMsg }}
        </v-alert>

        <!-- Onglet Profil -->
        <v-window v-model="activeTab">
          <v-window-item value="profile">
            <v-form @submit.prevent="updateProfile">
              <v-row>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="profileForm.name"
                    label="Nom complet"
                    variant="outlined"
                    prepend-inner-icon="mdi-account"
                    required
                  ></v-text-field>
                </v-col>
                
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="profileForm.email"
                    label="Adresse Email"
                    variant="outlined"
                    prepend-inner-icon="mdi-email"
                    disabled
                    hint="L'email ne peut pas être modifié"
                    persistent-hint
                  ></v-text-field>
                </v-col>
                
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="profileForm.phone"
                    label="Numéro de téléphone (optionnel)"
                    variant="outlined"
                    prepend-inner-icon="mdi-phone"
                  ></v-text-field>
                </v-col>
                
                <v-col cols="12">
                  <v-textarea
                    v-model="profileForm.bio"
                    label="Une petite bio (optionnelle)"
                    variant="outlined"
                    prepend-inner-icon="mdi-text"
                    rows="3"
                    hint="Présentez-vous brièvement aux hôtes ou voyageurs."
                  ></v-textarea>
                </v-col>
              </v-row>

              <div class="mt-4 text-right">
                <v-btn color="primary" type="submit" :loading="loading" elevation="2">
                  <v-icon start>mdi-content-save</v-icon>
                  Enregistrer les modifications
                </v-btn>
              </div>
            </v-form>
          </v-window-item>

          <!-- Onglet Sécurité -->
          <v-window-item value="security">
            <v-form @submit.prevent="updatePassword">
              <v-row>
                <v-col cols="12">
                  <v-text-field
                    v-model="passwordForm.current_password"
                    label="Mot de passe actuel"
                    type="password"
                    variant="outlined"
                    prepend-inner-icon="mdi-lock-outline"
                    required
                  ></v-text-field>
                </v-col>
                
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="passwordForm.password"
                    label="Nouveau mot de passe"
                    type="password"
                    variant="outlined"
                    prepend-inner-icon="mdi-lock-plus"
                    required
                  ></v-text-field>
                </v-col>
                
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="passwordForm.password_confirmation"
                    label="Confirmer le nouveau mot de passe"
                    type="password"
                    variant="outlined"
                    prepend-inner-icon="mdi-lock-check"
                    required
                  ></v-text-field>
                </v-col>
              </v-row>

              <div class="mt-4 text-right">
                <v-btn color="primary" type="submit" :loading="loading" elevation="2">
                  <v-icon start>mdi-key-change</v-icon>
                  Changer le mot de passe
                </v-btn>
              </div>
            </v-form>
          </v-window-item>
        </v-window>
      </v-card-text>
    </v-card>
  </v-container>
</template>