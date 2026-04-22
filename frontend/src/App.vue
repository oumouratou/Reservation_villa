<script setup>
import { computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useTheme } from 'vuetify'
import AppHeader from './components/AppHeader.vue'
import AppFooter from './components/AppFooter.vue'

const route = useRoute()
const theme = useTheme()
const isOwnerDashboard = computed(() => route.name === 'owner-dashboard')

const applyTheme = (themeName) => {
  if (typeof theme.change === 'function') {
    theme.change(themeName)
    return
  }
  theme.global.name.value = themeName
}

const isDarkMode = computed(() => theme.global.name.value === 'villaThemeDark')

// Update CSS variables based on theme
const cssVariables = computed(() => {
  const isDark = isDarkMode.value
  return {
    '--sneat-bg': isDark ? '#25253b' : '#f5f5f9',
    '--sneat-surface': isDark ? '#2d2e44' : '#ffffff',
    '--sneat-surface-soft': isDark ? '#3a3b55' : '#fcfcff',
    '--sneat-border': isDark ? '#42435f' : '#e7e7ef',
    '--sneat-title': isDark ? '#b8bcc8' : '#566a7f',
    '--sneat-text': isDark ? '#7f88a0' : '#697a8d',
    '--sneat-primary': '#696cff',
    '--sneat-primary-2': '#5f63f2',
    '--sneat-success': '#71dd37',
    '--sneat-danger': '#ff3e1d',
    '--sneat-info': '#00bcd4',
    '--sneat-warning': '#ffb300',
  }
})

onMounted(() => {
  // Load saved theme preference
  const savedTheme = localStorage.getItem('theme')
  applyTheme(savedTheme === 'dark' ? 'villaThemeDark' : 'villaTheme')
})
</script>

<template>
  <v-app :style="cssVariables" class="sneat-app">
    <div class="app-shell" :class="{ 'app-shell-dashboard': isOwnerDashboard }">
      <template v-if="!isOwnerDashboard">
        <div class="bg-orb orb-a"></div>
        <div class="bg-orb orb-b"></div>
        <AppHeader />
        <nav>
          <router-link to="/">Accueil</router-link> |
          <router-link to="/villas">Villas</router-link> |
          <router-link to="/login">Connexion</router-link> |
          <router-link to="/register">Inscription</router-link> |
          <router-link to="/reclamations">Mes réclamations</router-link> |
          <router-link to="/profile">Profil</router-link>
        </nav>
      </template>

      <v-main class="main-content" :class="{ 'main-content-dashboard': isOwnerDashboard }">
        <router-view />
      </v-main>

      <AppFooter v-if="!isOwnerDashboard" />
    </div>
  </v-app>
</template>

<style scoped>
.sneat-app {
  --sneat-bg: #f5f5f9;
  --sneat-surface: #ffffff;
  --sneat-surface-soft: #fcfcff;
  --sneat-border: #e7e7ef;
  --sneat-title: #566a7f;
  --sneat-text: #697a8d;
  --sneat-primary: #696cff;
  --sneat-primary-2: #5f63f2;
  --sneat-success: #71dd37;
  --sneat-danger: #ff3e1d;
  --sneat-info: #00bcd4;
  --sneat-warning: #ffb300;
}

.app-shell {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  background-color: var(--sneat-bg);
  transition: background-color 0.3s ease;
}

.app-shell-dashboard {
  background-color: var(--sneat-bg);
}

.main-content {
  flex: 1;
}

.main-content-dashboard {
  padding: 0;
}

/* Background orbs for visual effect */
.bg-orb {
  position: fixed;
  border-radius: 50%;
  opacity: 0.05;
  pointer-events: none;
  z-index: 0;
}

.orb-a {
  width: 400px;
  height: 400px;
  background: radial-gradient(circle, #696cff, transparent);
  top: -100px;
  right: -100px;
}

.orb-b {
  width: 300px;
  height: 300px;
  background: radial-gradient(circle, #5f63f2, transparent);
  bottom: -50px;
  left: -50px;
}

/* Global dark mode transitions */
:deep(.v-app) {
  transition: background-color 0.3s ease, color 0.3s ease;
}
</style>
