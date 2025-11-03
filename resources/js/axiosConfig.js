import axios from 'axios';

// Establece la URL base de tu API de Laravel (la de 'php artisan serve')
axios.defaults.baseURL = 'http://127.0.0.1:8000';
axios.defaults.withCredentials = true;
// --- Interceptor de Solicitudes  ---
// Esto se ejecuta ANTES de que se envíe cualquier petición de Axios
axios.interceptors.request.use(config => {

  // 1. Busca el token que guardamos en el localStorage
  const token = localStorage.getItem('auth_token');

  // 2. Si el token existe, lo adjunta a la cabecera 'Authorization'
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }

  return config;
});

export default axios;