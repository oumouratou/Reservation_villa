import axios from 'axios';
import { getAuthToken } from './auth';

export function useApi() {
  const token = getAuthToken();
  if (token) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
  }
  return axios;
}
