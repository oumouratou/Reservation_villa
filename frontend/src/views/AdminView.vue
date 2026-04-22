<script setup>
import { computed, onMounted, reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { getAuthToken, getAuthUser, saveAuthSession } from '../utils/auth'

const router = useRouter()
const user = ref(getAuthUser())
const loading = ref(false)
const dashboardLoading = ref(false)
const errorMessage = ref('')
const loginError = ref('')
const dashboard = ref(null)
const loginForm = reactive({
  email: 'admin@villareserv.com',
  password: 'Admin@12345',
})

const adminName = computed(() => user.value?.name || 'Administrateur')
const adminInitial = computed(() => adminName.value.charAt(0).toUpperCase())
const isAdmin = computed(() => user.value?.role === 'admin')

function money(value) {
  const number = Number(value || 0)
  return new Intl.NumberFormat('fr-FR', {
    style: 'currency',
    currency: 'EUR',
    maximumFractionDigits: 0,
  }).format(number)
}

function formatDate(value) {
  if (!value) return '—'
  return new Intl.DateTimeFormat('fr-FR', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
  }).format(new Date(value))
}

async function fetchDashboard() {
  if (!isAdmin.value) return

  dashboardLoading.value = true
  errorMessage.value = ''

  try {
    const response = await fetch('/api/admin/dashboard', {
      headers: {
        Accept: 'application/json',
        Authorization: `Bearer ${getAuthToken()}`,
      },
    })

    const data = await response.json().catch(() => null)

    if (!response.ok) {
      throw new Error(data?.message || 'Impossible de charger le tableau de bord.')
    }

    dashboard.value = data
  } catch (error) {
    errorMessage.value = error?.message || 'Erreur lors du chargement du back-office.'
  } finally {
    dashboardLoading.value = false
  }
}

async function submitAdminLogin() {
  loginError.value = ''
  loading.value = true

  try {
    const response = await fetch('/auth/login', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
      },
      body: JSON.stringify({
        email: loginForm.email,
        password: loginForm.password,
      }),
    })

    const data = await response.json().catch(() => null)

    if (!response.ok) {
      throw new Error(data?.message || 'Connexion refusée.')
    }

    if (data?.user?.role !== 'admin') {
      throw new Error("Ce compte n'a pas le rôle administrateur.")
    }

    saveAuthSession(data)
    user.value = data.user
    await fetchDashboard()
    await router.replace({ name: 'admin' })
  } catch (error) {
    loginError.value = error?.message || 'Connexion impossible.'
  } finally {
    loading.value = false
  }
}

async function logout() {
  localStorage.removeItem('auth_token')
  localStorage.removeItem('auth_user')
  window.dispatchEvent(new Event('auth-changed'))
  user.value = null
  dashboard.value = null
  await router.push({ name: 'auth' })
}

onMounted(() => {
  if (isAdmin.value) {
    fetchDashboard()
  }
})
</script>

<template>
  <section class="admin-shell">
    <div class="admin-ambient admin-ambient-a"></div>
    <div class="admin-ambient admin-ambient-b"></div>

    <template v-if="!isAdmin">
      <div class="admin-auth-card reveal">
        <div class="admin-auth-badge">Laravel native admin</div>
        <h1>Connexion administrateur</h1>
        <p>Accédez au back-office Laravel avec un compte au rôle <strong>admin</strong>.</p>

        <form class="admin-auth-form" @submit.prevent="submitAdminLogin">
          <label>
            Email administrateur
            <input v-model="loginForm.email" type="email" placeholder="admin@villareserv.com" required />
          </label>

          <label>
            Mot de passe
            <input v-model="loginForm.password" type="password" placeholder="Admin@12345" required />
          </label>

          <button class="admin-primary-btn" type="submit" :disabled="loading">
            {{ loading ? 'Connexion...' : 'Entrer dans l\'admin' }}
          </button>
        </form>

        <p class="admin-hint">Compte de démonstration: admin@villareserv.com / Admin@12345</p>
        <p v-if="loginError" class="admin-error">{{ loginError }}</p>
      </div>
    </template>

    <template v-else>
      <aside class="admin-sidebar reveal">
        <div class="brand-row">
          <div class="brand-mark">V</div>
          <div>
            <p class="brand-kicker">Back-office</p>
            <h2>VillaReserv</h2>
          </div>
        </div>

        <nav class="admin-nav">
          <a href="#overview" class="admin-nav-link">Vue d'ensemble</a>
          <a href="#users" class="admin-nav-link">Utilisateurs</a>
          <a href="#villas" class="admin-nav-link">Villas</a>
          <a href="#reservations" class="admin-nav-link">Réservations</a>
          <a href="#revenue" class="admin-nav-link">Revenus</a>
        </nav>

        <div class="admin-profile">
          <div class="admin-avatar">{{ adminInitial }}</div>
          <div>
            <strong>{{ adminName }}</strong>
            <span>Administrateur</span>
          </div>
        </div>

        <button class="admin-secondary-btn" type="button" @click="logout">Déconnexion</button>
      </aside>

      <main class="admin-main">
        <header class="admin-hero reveal">
          <div>
            <p class="eyebrow">Espace administrateur</p>
            <h1>Tableau de bord natif Laravel</h1>
            <p>Supervisez les utilisateurs, les villas, les réservations et les litiges depuis cette interface connectée au backend.</p>
          </div>
          <div class="hero-metric">
            <span>Session active</span>
            <strong>{{ adminName }}</strong>
          </div>
        </header>

        <p v-if="errorMessage" class="admin-error reveal">{{ errorMessage }}</p>

        <section id="overview" class="admin-stats-grid reveal">
          <article class="admin-stat-card">
            <span>Utilisateurs</span>
            <strong>{{ dashboardLoading ? '...' : dashboard?.stats?.users?.total ?? '—' }}</strong>
            <small>{{ dashboard?.stats?.users?.travelers ?? 0 }} voyageurs / {{ dashboard?.stats?.users?.owners ?? 0 }} propriétaires</small>
          </article>
          <article class="admin-stat-card">
            <span>Villas</span>
            <strong>{{ dashboardLoading ? '...' : dashboard?.stats?.villas?.total ?? '—' }}</strong>
            <small>{{ dashboard?.stats?.villas?.pending ?? 0 }} en attente</small>
          </article>
          <article class="admin-stat-card">
            <span>Réservations</span>
            <strong>{{ dashboardLoading ? '...' : dashboard?.stats?.reservations?.total ?? '—' }}</strong>
            <small>{{ dashboard?.stats?.reservations?.pending ?? 0 }} en cours</small>
          </article>
          <article class="admin-stat-card">
            <span>Revenus</span>
            <strong>{{ dashboardLoading ? '...' : money(dashboard?.stats?.revenue?.total) }}</strong>
            <small>{{ money(dashboard?.stats?.revenue?.this_month) }} ce mois</small>
          </article>
        </section>

        <section id="users" class="admin-panel reveal">
          <div class="panel-head">
            <div>
              <p class="eyebrow">Pilotage</p>
              <h3>Dernières réservations</h3>
            </div>
          </div>

          <div class="admin-table">
            <div class="admin-table-head">
              <span>Villa</span>
              <span>Voyageur</span>
              <span>Montant</span>
              <span>Créée le</span>
            </div>
            <div v-if="dashboardLoading" class="admin-table-empty">Chargement du back-office...</div>
            <div v-else-if="!dashboard?.recentReservations?.length" class="admin-table-empty">Aucune réservation à afficher.</div>
            <div v-else v-for="reservation in dashboard.recentReservations" :key="reservation.id" class="admin-table-row">
              <span>{{ reservation.villa?.title || '—' }}</span>
              <span>{{ reservation.traveler?.name || '—' }}</span>
              <span>{{ money(reservation.total_price ?? reservation.amount) }}</span>
              <span>{{ formatDate(reservation.created_at) }}</span>
            </div>
          </div>
        </section>

        <section id="villas" class="admin-grid-two reveal">
          <article class="admin-panel">
            <div class="panel-head">
              <div>
                <p class="eyebrow">Contrôle</p>
                <h3>Statut des villas</h3>
              </div>
            </div>

            <div class="admin-kpi-list">
              <div class="kpi-line">
                <span>Approuvées</span>
                <strong>{{ dashboard?.stats?.villas?.approved ?? 0 }}</strong>
              </div>
              <div class="kpi-line">
                <span>En attente</span>
                <strong>{{ dashboard?.stats?.villas?.pending ?? 0 }}</strong>
              </div>
            </div>
          </article>

          <article id="revenue" class="admin-panel">
            <div class="panel-head">
              <div>
                <p class="eyebrow">Finances</p>
                <h3>Revenus mensuels</h3>
              </div>
            </div>

            <div class="revenue-list">
              <div v-if="dashboardLoading" class="admin-table-empty">Chargement...</div>
              <div v-else-if="!dashboard?.monthlyRevenue?.length" class="admin-table-empty">Aucune donnée mensuelle.</div>
              <div v-else v-for="entry in dashboard.monthlyRevenue" :key="entry.month" class="revenue-row">
                <span>Mois {{ entry.month }}</span>
                <strong>{{ money(entry.total) }}</strong>
              </div>
            </div>
          </article>
        </section>

        <section id="reservations" class="admin-panel reveal">
          <div class="panel-head">
            <div>
              <p class="eyebrow">Accès rapide</p>
              <h3>Actions administrateur</h3>
            </div>
          </div>

          <div class="quick-actions">
            <a href="/connexion?mode=login" class="quick-action">Ouvrir la page de connexion</a>
            <a href="/villas" class="quick-action">Consulter les villas</a>
            <a href="/messages" class="quick-action">Voir les messages</a>
          </div>
        </section>
      </main>
    </template>
  </section>
</template>

<style scoped>
.admin-shell {
  position: relative;
  min-height: 100vh;
  padding: 28px;
  background:
    radial-gradient(circle at top left, rgba(245, 158, 11, 0.16), transparent 35%),
    radial-gradient(circle at bottom right, rgba(59, 130, 246, 0.16), transparent 30%),
    linear-gradient(135deg, #08111f 0%, #111827 54%, #f3f4f6 54%, #f8fafc 100%);
  color: #e5eefb;
  overflow: hidden;
}

.admin-ambient {
  position: absolute;
  border-radius: 999px;
  filter: blur(40px);
  opacity: 0.55;
  pointer-events: none;
}

.admin-ambient-a {
  width: 320px;
  height: 320px;
  left: -90px;
  top: 60px;
  background: rgba(245, 158, 11, 0.24);
}

.admin-ambient-b {
  width: 260px;
  height: 260px;
  right: 20px;
  top: 130px;
  background: rgba(59, 130, 246, 0.18);
}

.admin-auth-card,
.admin-sidebar,
.admin-panel,
.admin-hero,
.admin-stat-card {
  position: relative;
  z-index: 1;
}

.admin-auth-card {
  max-width: 520px;
  margin: 9vh auto 0;
  padding: 32px;
  border-radius: 28px;
  background: rgba(8, 17, 31, 0.8);
  border: 1px solid rgba(255, 255, 255, 0.08);
  box-shadow: 0 30px 80px rgba(0, 0, 0, 0.35);
  backdrop-filter: blur(20px);
}

.admin-auth-badge,
.eyebrow,
.brand-kicker {
  text-transform: uppercase;
  letter-spacing: 0.14em;
  font-size: 0.72rem;
  color: #fbbf24;
}

.admin-auth-card h1,
.admin-hero h1 {
  margin: 10px 0 10px;
  font-size: clamp(2rem, 4vw, 3.2rem);
  line-height: 1;
}

.admin-auth-card p,
.admin-hero p {
  color: rgba(229, 238, 251, 0.78);
}

.admin-auth-form {
  display: grid;
  gap: 16px;
  margin-top: 24px;
}

.admin-auth-form label {
  display: grid;
  gap: 8px;
  font-size: 0.95rem;
}

.admin-auth-form input {
  border-radius: 16px;
  border: 1px solid rgba(255, 255, 255, 0.1);
  background: rgba(255, 255, 255, 0.05);
  color: #fff;
  padding: 14px 16px;
}

.admin-primary-btn,
.admin-secondary-btn,
.quick-action {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-height: 48px;
  border-radius: 14px;
  border: 0;
  text-decoration: none;
  cursor: pointer;
  transition: transform 0.2s ease, opacity 0.2s ease, background 0.2s ease;
}

.admin-primary-btn {
  background: linear-gradient(135deg, #f59e0b, #f97316);
  color: #111827;
  font-weight: 700;
}

.admin-secondary-btn {
  width: 100%;
  margin-top: 18px;
  background: rgba(255, 255, 255, 0.06);
  color: #f8fafc;
}

.admin-primary-btn:hover,
.admin-secondary-btn:hover,
.quick-action:hover {
  transform: translateY(-1px);
}

.admin-hint,
.admin-error {
  margin-top: 12px;
  font-size: 0.92rem;
}

.admin-error {
  color: #fca5a5;
}

.admin-sidebar {
  width: 280px;
  padding: 24px;
  border-radius: 28px;
  background: rgba(8, 17, 31, 0.82);
  border: 1px solid rgba(255, 255, 255, 0.08);
  box-shadow: 0 30px 80px rgba(0, 0, 0, 0.35);
  backdrop-filter: blur(20px);
  position: fixed;
  inset: 28px auto 28px 28px;
  display: flex;
  flex-direction: column;
}

.brand-row {
  display: flex;
  gap: 14px;
  align-items: center;
}

.brand-mark,
.admin-avatar {
  width: 46px;
  height: 46px;
  border-radius: 16px;
  display: grid;
  place-items: center;
  font-weight: 800;
  color: #111827;
  background: linear-gradient(135deg, #fbbf24, #fb7185);
}

.brand-row h2 {
  margin: 0;
}

.admin-nav {
  display: grid;
  gap: 10px;
  margin: 28px 0;
}

.admin-nav-link {
  padding: 12px 14px;
  border-radius: 14px;
  color: rgba(229, 238, 251, 0.82);
  background: rgba(255, 255, 255, 0.04);
  text-decoration: none;
}

.admin-profile {
  margin-top: auto;
  display: flex;
  gap: 12px;
  align-items: center;
  padding: 16px;
  border-radius: 18px;
  background: rgba(255, 255, 255, 0.05);
}

.admin-profile span {
  display: block;
  color: rgba(229, 238, 251, 0.62);
  font-size: 0.9rem;
}

.admin-main {
  margin-left: 308px;
  display: grid;
  gap: 20px;
}

.admin-hero,
.admin-panel,
.admin-stat-card {
  border-radius: 28px;
  background: rgba(255, 255, 255, 0.08);
  border: 1px solid rgba(255, 255, 255, 0.08);
  box-shadow: 0 18px 60px rgba(2, 6, 23, 0.18);
  backdrop-filter: blur(20px);
}

.admin-hero {
  padding: 28px;
  display: flex;
  justify-content: space-between;
  gap: 24px;
}

.hero-metric {
  min-width: 220px;
  padding: 18px;
  border-radius: 22px;
  background: rgba(255, 255, 255, 0.06);
  display: grid;
  align-content: start;
  gap: 8px;
}

.hero-metric span {
  color: rgba(229, 238, 251, 0.62);
}

.admin-stats-grid {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 18px;
}

.admin-stat-card {
  padding: 22px;
  display: grid;
  gap: 10px;
  min-height: 150px;
}

.admin-stat-card span,
.admin-stat-card small {
  color: rgba(229, 238, 251, 0.72);
}

.admin-stat-card strong {
  font-size: 2rem;
}

.admin-panel {
  padding: 24px;
}

.panel-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 18px;
}

.admin-table {
  display: grid;
  gap: 10px;
}

.admin-table-head,
.admin-table-row {
  display: grid;
  grid-template-columns: 2fr 1.2fr 0.9fr 0.9fr;
  gap: 14px;
  align-items: center;
}

.admin-table-head {
  color: rgba(229, 238, 251, 0.56);
  font-size: 0.84rem;
  text-transform: uppercase;
  letter-spacing: 0.08em;
}

.admin-table-row,
.admin-table-empty,
.revenue-row,
.kpi-line {
  padding: 14px 16px;
  border-radius: 16px;
  background: rgba(255, 255, 255, 0.05);
}

.admin-table-empty {
  color: rgba(229, 238, 251, 0.68);
}

.admin-grid-two {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 18px;
}

.admin-kpi-list,
.revenue-list,
.quick-actions {
  display: grid;
  gap: 12px;
}

.kpi-line,
.revenue-row {
  display: flex;
  justify-content: space-between;
}

.quick-actions {
  grid-template-columns: repeat(3, minmax(0, 1fr));
}

.quick-action {
  background: linear-gradient(135deg, rgba(245, 158, 11, 0.18), rgba(59, 130, 246, 0.18));
  color: #e5eefb;
  border: 1px solid rgba(255, 255, 255, 0.08);
}

@media (max-width: 1100px) {
  .admin-sidebar {
    position: static;
    width: auto;
    margin-bottom: 18px;
  }

  .admin-main {
    margin-left: 0;
  }

  .admin-stats-grid,
  .admin-grid-two,
  .quick-actions {
    grid-template-columns: 1fr 1fr;
  }

  .admin-hero {
    flex-direction: column;
  }
}

@media (max-width: 720px) {
  .admin-shell {
    padding: 16px;
  }

  .admin-stats-grid,
  .admin-grid-two,
  .quick-actions {
    grid-template-columns: 1fr;
  }

  .admin-table-head,
  .admin-table-row {
    grid-template-columns: 1fr;
  }

  .admin-auth-card,
  .admin-hero,
  .admin-panel,
  .admin-stat-card,
  .admin-sidebar {
    border-radius: 22px;
  }
}
</style>
