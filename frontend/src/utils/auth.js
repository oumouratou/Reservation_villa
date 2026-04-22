export function saveAuthSession(payload) {
  const token = payload?.token || payload?.access_token || payload?.data?.token || payload?.data?.access_token
  if (token) {
    localStorage.setItem('auth_token', token)
  }

  if (payload?.user) {
    localStorage.setItem('auth_user', JSON.stringify(payload.user))
  }

  window.dispatchEvent(new Event('auth-changed'))
}

export function clearAuthSession() {
  localStorage.removeItem('auth_token')
  localStorage.removeItem('token')
  localStorage.removeItem('access_token')
  localStorage.removeItem('auth_user')
  window.dispatchEvent(new Event('auth-changed'))
}

export function getAuthUser() {
  try {
    const raw = localStorage.getItem('auth_user')
    return raw ? JSON.parse(raw) : null
  } catch {
    return null
  }
}

export function getAuthToken() {
  return (
    localStorage.getItem('auth_token') ||
    localStorage.getItem('token') ||
    localStorage.getItem('access_token') ||
    ''
  )
}

export function getDashboardRouteByRole(role) {
  if (role === 'owner') return { name: 'owner-dashboard' } // Remplacez si cette route n'existe pas
  if (role === 'traveler') return { name: 'traveler-dashboard' } // Remplacez si cette route n'existe pas
  if (role === 'admin') return { name: 'admin' }
  return { name: 'profile' } // Rediriger par défaut (client) vers le profil
}
