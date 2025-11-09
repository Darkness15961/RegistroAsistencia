<template>
  <div 
    class="w-full min-h-screen flex items-center justify-center p-4"
    :class="isDark ? 'bg-dark-gradient' : 'bg-light-gradient'"
  >
    <div 
      class="w-full max-w-4xl relative flex flex-col md:flex-row rounded-3xl shadow-2xl overflow-hidden" 
      :class="theme('card').value"
    >
      <!-- Botón de cambio de tema -->
      <button 
        @click="toggleTheme"
        class="absolute top-6 right-6 z-10 w-10 h-10 flex items-center justify-center rounded-xl transition-all duration-200"
        :class="theme('headerButton').value"
        title="Cambiar tema"
      >
        <svg v-if="isDark" class="w-6 h-6 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
          <path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zM14 10a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>
        <svg v-else class="w-6 h-6 text-gray-700" fill="currentColor" viewBox="0 0 20 20">
          <path d="M17.293 13.293A8 8 0 016.707 2.707a8 8 0 1010.586 10.586z" />
        </svg>
      </button>

      <!-- Panel izquierdo -->
      <div 
        class="w-full md:w-1/2 p-8 sm:p-12 flex flex-col justify-center items-center text-center" 
        :class="isDark ? 'bg-gray-800' : 'bg-gradient-to-br from-blue-500 to-purple-600'"
      >
        <div class="w-20 h-20 bg-gradient-to-br from-blue-400 to-cyan-400 rounded-full flex items-center justify-center mb-6 shadow-lg">
          <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
          </svg>
        </div>
        <h1 class="text-4xl font-bold mb-3 text-white">
          Bienvenido a 4Scan
        </h1>
        <p class="text-lg text-blue-100">
          Sistema de gestión de asistencia con reconocimiento facial.
        </p>
      </div>

      <!-- Panel derecho (formulario) -->
      <div class="w-full md:w-1/2 p-8 sm:p-12 flex flex-col justify-center">
        <h2 class="text-3xl font-bold mb-6" :class="theme('cardTitle').value">
          Iniciar Sesión
        </h2>
        
        <form @submit.prevent="login" class="space-y-5">
          <div>
            <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">
              Correo Electrónico
            </label>
            <input 
              v-model="form.email"
              type="email" 
              placeholder="tu@correo.com"
              class="w-full rounded-xl"
              :class="theme('input').value"
              required
            >
          </div>
          
          <div>
            <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">
              Contraseña
            </label>
            <input 
              v-model="form.password"
              type="password" 
              placeholder="••••••••"
              class="w-full rounded-xl"
              :class="theme('input').value"
              required
            >
          </div>

          <div 
            v-if="error" 
            class="text-red-500 text-sm font-medium p-3 rounded-xl bg-red-500/10 border border-red-500/20"
          >
            {{ error }}
          </div>
          
          <div class="flex items-center justify-between">
            <a 
              href="#" 
              class="text-sm font-medium transition-colors"
              :class="isDark ? 'text-gray-300 hover:text-white' : 'text-gray-600 hover:text-gray-900'"
            >
              ¿Olvidaste tu contraseña?
            </a>
          </div>
          
          <button 
            type="submit"
            :disabled="loading"
            class="w-full flex items-center justify-center gap-2 px-6 py-3 rounded-xl font-semibold shadow-lg hover:scale-105 transition-all duration-200"
            :class="[theme('buttonPrimary').value, loading ? 'opacity-50 cursor-not-allowed' : '']"
          >
            <i v-if="loading" class="fas fa-spinner animate-spin"></i>
            {{ loading ? 'Ingresando...' : 'Ingresar' }}
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

// ✅ Importa tu composable de tema desde el nuevo módulo (ajusta si cambió ruta)
import { useTheme } from '@/composables/useTheme'

const { theme, isDark, toggleTheme } = useTheme()
const router = useRouter()

const form = ref({ email: '', password: '' })
const loading = ref(false)
const error = ref(null)

const login = async () => {
  loading.value = true
  error.value = null

  try {
    const response = await axios.post('/login', form.value)
    localStorage.setItem('auth_token', response.data.access_token)
    localStorage.setItem('user_data', JSON.stringify(response.data.user))
    router.push('/home')
  } catch (err) {
    loading.value = false
    if (err.response && err.response.status === 422) {
      error.value = err.response.data.message || 'Credenciales incorrectas.'
    } else {
      error.value = 'Error inesperado. Intente nuevamente.'
    }
    console.error('Error de login:', err)
  }
}
</script>
