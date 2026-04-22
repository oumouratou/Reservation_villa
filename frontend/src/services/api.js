import axios from 'axios';
import { getAuthToken } from '../utils/auth';

const apiClient = axios.create({
  baseURL: 'http://localhost:8000/api', // Assurez-vous que c'est la bonne URL de votre backend
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
});

apiClient.interceptors.request.use(config => {
  const token = getAuthToken();
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

export default {
  // Authentification
  register(user) {
    return apiClient.post('/client/register', user);
  },
  login(credentials) {
    return apiClient.post('/client/login', credentials);
  },
  logout() {
    return apiClient.post('/client/logout');
  },

  // Villas
  getVillas() {
    return apiClient.get('/villas');
  },
  getVilla(id) {
    return apiClient.get(`/villas/${id}`);
  },

  // Réservations
  createReservation(reservation) {
    return apiClient.post('/client/reservations', reservation);
  },
  getReservations() {
    return apiClient.get('/client/reservations');
  },

  // Réclamations
  createReclamation(reclamation) {
    return apiClient.post('/client/reclamations', reclamation);
  },
  getReclamations() {
    return apiClient.get('/client/reclamations');
  },

  // Profil
  getProfile() {
    return apiClient.get('/client/profile');
  },
  updateProfile(profile) {
    return apiClient.put('/client/profile', profile);
  },
};
