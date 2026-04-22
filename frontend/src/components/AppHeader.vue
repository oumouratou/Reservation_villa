<script setup>
import { computed, onMounted, onUnmounted, ref, inject } from 'vue'
import { useRouter } from 'vue-router'
import { useTheme } from 'vuetify'
import { clearAuthSession, getAuthUser } from '../utils/auth'

const router = useRouter()
const theme = useTheme()
const user = ref(getAuthUser())
const drawer = ref(false)

const applyTheme = (themeName) => {
  if (typeof theme.change === 'function') {
    theme.change(themeName)
    return
  }
  theme.global.name.value = themeName
}

const isDarkMode = computed({
  get: () => theme.global.name.value === 'villaThemeDark',
  set: (val) => {
    applyTheme(val ? 'villaThemeDark' : 'villaTheme')
    localStorage.setItem('theme', val ? 'dark' : 'light')
  },
})

const navLinks = computed(() => {
  const base = [
    { to: '/', label: 'Accueil', icon: 'mdi-home' },
    { to: '/villas', label: 'Villas', icon: 'mdi-home-city' },
  ]

  if (!user.value) {
    return base
  }

  if (user.value.role === 'owner') {
    return [
      ...base,
      { to: '/dashboard/owner', label: 'Dashboard', icon: 'mdi-view-dashboard' },
      { to: '/messages', label: 'Messages', icon: 'mdi-email' },
      { to: '/profile', label: 'Profil', icon: 'mdi-account' },
    ]
  }

  if (user.value.role === 'client' || user.value.role === 'traveler') {
    return [
      ...base,
      { to: '/dashboard/traveler', label: 'Voyages', icon: 'mdi-briefcase' },
      { to: '/reservations', label: 'Réservations', icon: 'mdi-calendar' },
      { to: '/reclamations', label: 'Réclamations', icon: 'mdi-alert-circle' },
      { to: '/messages', label: 'Messages', icon: 'mdi-email' },
      { to: '/profile', label: 'Profil', icon: 'mdi-account' },
    ]
  }

  return [...base, { to: '/admin', label: 'Admin', icon: 'mdi-cog' }]
})

function syncAuth() {
  user.value = getAuthUser()
}

function logout() {
  clearAuthSession()
  router.push({ name: 'auth' })
}

onMounted(() => {
  window.addEventListener('auth-changed', syncAuth)
  const savedTheme = localStorage.getItem('theme') === 'dark'
  applyTheme(savedTheme ? 'villaThemeDark' : 'villaTheme')
})

onUnmounted(() => {
  window.removeEventListener('auth-changed', syncAuth)
})
</script>

<template>
  <v-app-bar elevation="1" :color="isDarkMode ? '#2d2e44' : '#ffffff'" class="sneat-header">
    <template #prepend>
      <v-app-bar-nav-icon @click="drawer = !drawer" class="d-md-none"></v-app-bar-nav-icon>
    </template>

    <router-link to="/" class="header-brand">
      <span class="brand-icon">VP</span>
      <span class="brand-text">Villa Project</span>
    </router-link>

    <v-spacer></v-spacer>

    <!-- Navigation desktop -->
    <nav class="d-none d-md-flex nav-desktop">
      <router-link
        v-for="link in navLinks"
        :key="link.to"
        :to="link.to"
        class="nav-item-desktop"
      >
        <v-icon size="small" class="me-1">{{ link.icon }}</v-icon>
        {{ link.label }}
      </router-link>
    </nav>

    <v-spacer></v-spacer>

    <!-- Actions -->
    <div class="header-actions">
      <!-- Dark mode toggle -->
      <v-btn
        icon
        @click="isDarkMode = !isDarkMode"
        :aria-label="isDarkMode ? 'Mode clair' : 'Mode sombre'"
        variant="text"
      >
        <v-icon>{{ isDarkMode ? 'mdi-white-balance-sunny' : 'mdi-moon-waning-crescent' }}</v-icon>
      </v-btn>

      <!-- Auth buttons -->
      <div v-if="!user" class="d-none d-sm-flex gap-2">
        <router-link to="/register" class="auth-link">
          <v-btn variant="outlined" size="small">S'inscrire</v-btn>
        </router-link>
        <router-link to="/login" class="auth-link">
          <v-btn color="primary" size="small">Connexion</v-btn>
        </router-link>
      </div>

      <v-menu v-else>
        <template #activator="{ props }">
          <v-btn icon v-bind="props" class="d-sm-flex">
            <v-icon>mdi-account-circle</v-icon>
          </v-btn>
        </template>

        <v-list>
          <v-list-item class="user-info">
            <span class="text-caption">Connecté en tant que</span>
            <span class="font-weight-bold">{{ user.name }}</span>
          </v-list-item>
          <v-divider></v-divider>
          <v-list-item @click="logout">
            <v-icon start>mdi-logout</v-icon>
            Déconnexion
          </v-list-item>
        </v-list>
      </v-menu>
    </div>
  </v-app-bar>

  <!-- Navigation drawer mobile -->
  <v-navigation-drawer
    v-model="drawer"
    temporary
    class="d-md-none"
  >
    <v-list>
      <v-list-item class="mb-3">
        <template #prepend>
          <span class="brand-icon-mobile">VP</span>
        </template>
        <v-list-item-title class="font-weight-bold">Villa Project</v-list-item-title>
      </v-list-item>

      <v-divider></v-divider>

      <router-link
        v-for="link in navLinks"
        :key="link.to"
        :to="link.to"
        class="nav-link-mobile"
        @click="drawer = false"
      >
        <v-list-item :title="link.label">
          <template #prepend>
            <v-icon>{{ link.icon }}</v-icon>
          </template>
        </v-list-item>
      </router-link>

      <v-divider v-if="!user" class="my-2"></v-divider>

      <div v-if="!user" class="px-4">
        <router-link to="/register" @click="drawer = false">
          <v-btn block variant="outlined" size="small" class="mb-2">S'inscrire</v-btn>
        </router-link>
        <router-link to="/login" @click="drawer = false">
          <v-btn block color="primary" size="small">Connexion</v-btn>
        </router-link>
      </div>
    </v-list>
  </v-navigation-drawer>
</template>

<style scoped>
.sneat-header {
  border-bottom: 1px solid rgba(105, 108, 255, 0.1) !important;
  box-shadow: 0 0.25rem 1rem rgba(161, 172, 184, 0.15) !important;
}

.header-brand {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  text-decoration: none;
  margin-right: 2rem;
}

.brand-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 40px;
  height: 40px;
  border-radius: 8px;
  background: linear-gradient(135deg, #696cff 0%, #5f63f2 100%);
  color: white;
  font-weight: 700;
  font-size: 14px;
}

.brand-text {
  font-weight: 700;
  font-size: 16px;
  letter-spacing: 0.5px;
  background: linear-gradient(135deg, #696cff 0%, #5f63f2 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.brand-icon-mobile {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
  border-radius: 6px;
  background: linear-gradient(135deg, #696cff 0%, #5f63f2 100%);
  color: white;
  font-weight: 700;
  font-size: 12px;
}

.nav-desktop {
  gap: 2rem;
}

.nav-item-desktop {
  display: flex !important;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 500;
  text-decoration: none;
  color: rgba(97, 97, 153, 0.8);
  transition: all 0.3s ease;
}

.nav-item-desktop:hover {
  background-color: rgba(105, 108, 255, 0.08);
  color: #696cff;
}

.nav-item-desktop.router-link-active {
  background: linear-gradient(135deg, rgba(105, 108, 255, 0.15) 0%, rgba(95, 99, 242, 0.1) 100%);
  color: #696cff;
}

.header-actions {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.auth-link {
  text-decoration: none;
}

.user-info {
  display: flex;
  flex-direction: column;
  padding: 1rem !important;
}

.nav-link-mobile {
  text-decoration: none;
}

.gap-2 {
  gap: 0.5rem;
}
</style>
