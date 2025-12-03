import axios from 'axios'

// URL base del backend Laravel
axios.defaults.baseURL = 'https://navajowhite-albatross-624410.hostingersite.com/api'
//axios.defaults.baseURL = import.meta.env.VITE_API_URL || 'http://127.0.0.1:8000/api'

// Permitir el envío de cookies si usas sanctum o sesiones
axios.defaults.withCredentials = true

// Interceptor para agregar el token si existe
axios.interceptors.request.use(config => {
  const token = localStorage.getItem('auth_token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

// Interceptor de respuesta (opcional)
axios.interceptors.response.use(
  response => response,
  error => {
    // Si el token expiró o hay 401 → redirigir al login
    if (error.response?.status === 401) {
      localStorage.removeItem('auth_token')
      if (window.location.pathname !== '/login') {
        window.location.href = '/login'
      }
    }
    return Promise.reject(error)
  }
)

export default axios
