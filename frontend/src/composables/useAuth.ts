import { ref } from 'vue'
import axios from 'axios'
import { User } from '../types'

axios.defaults.headers.common['Accept'] = 'application/json';
axios.defaults.headers.common['Content-Type'] = 'application/json';

import.meta.env.VITE_API_BASE_URL && (axios.defaults.baseURL = import.meta.env.VITE_API_BASE_URL);

export const user = ref<User | null>(null)

function setAuthHeader(token: string | null) {
  if (token) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
  } else {
    delete axios.defaults.headers.common['Authorization'];
  }
}

const tokenKey = 'jwt_token';

export async function fetchUser() {
  const token = localStorage.getItem(tokenKey);
  setAuthHeader(token);
  try {
    const { data } = await axios.get('/api/user');
    user.value = data ? (data as User) : null;
  } catch (error) {
    user.value = null;
    setAuthHeader(null);
    localStorage.removeItem(tokenKey);
  }
}

export async function login(email: string, password: string) {
  const response = await axios.post('/api/login', { email, password });
  const data = response.data as { access_token: string };
  localStorage.setItem(tokenKey, data.access_token);
  setAuthHeader(data.access_token);
  await fetchUser();
}

export async function register(name: string, email: string, password: string, password_confirmation: string) {
  const response = await axios.post('/api/register', { name, email, password, password_confirmation });
  const data = response.data as { access_token: string };
  localStorage.setItem(tokenKey, data.access_token);
  setAuthHeader(data.access_token);
  await fetchUser();
}

export async function logout() {
  try {
    await axios.post('/api/logout');
  } catch {}
  localStorage.removeItem(tokenKey);
  setAuthHeader(null);
  user.value = null
}

export function isLoggedIn() {
  return !!user.value
}
