import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import VillasView from '../views/VillasView.vue'
import VillaDetailView from '../views/VillaDetailView.vue'
import CreateReservationView from '../views/CreateReservationView.vue'
import ReclamationsView from '../views/ReclamationsView.vue'
import ProfileView from '../views/ProfileView.vue'
import TravelerDashboardView from '../views/TravelerDashboardView.vue'
import OwnerDashboardView from '../views/OwnerDashboardView.vue'
import MessagesView from '../views/MessagesView.vue'
import ReservationView from '../views/ReservationView.vue'
import { getAuthUser } from '../utils/auth'

import AuthView from '../views/AuthView.vue'

const routes = [
  { path: '/', name: 'home', component: HomeView },
  { path: '/villas', name: 'villas', component: VillasView },
  { path: '/villas/:id', name: 'villa-detail', component: VillaDetailView, props: true },
  { path: '/login', name: 'login', component: AuthView },
  { path: '/register', name: 'register', component: AuthView },
  
  // Protected Routes
  { path: '/reservations/create', name: 'create-reservation', component: CreateReservationView, meta: { requiresAuth: true } },
  { path: '/reclamations', name: 'reclamations', component: ReclamationsView, meta: { requiresAuth: true } },
  { path: '/profile', name: 'profile', component: ProfileView, meta: { requiresAuth: true } },
  
  // Dashboard & Specific Views
  { path: '/dashboard/traveler', name: 'traveler-dashboard', component: TravelerDashboardView, meta: { requiresAuth: true } },
  { path: '/dashboard/owner', name: 'owner-dashboard', component: OwnerDashboardView, meta: { requiresAuth: true } },
  { path: '/messages', name: 'messages', component: MessagesView, meta: { requiresAuth: true } },
  { path: '/reservations', name: 'reservations', component: ReservationView, meta: { requiresAuth: true } },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach((to, from, next) => {
  const user = getAuthUser()

  if (to.meta.requiresAuth && !user) {
    next({ name: 'login', query: { redirect: to.fullPath } })
  } else if (user && (to.name === 'login' || to.name === 'register')) {
    next({ name: 'profile' })
  } else {
    next()
  }
})

export default router
