<template>
  <div 
    class="w-full min-h-screen flex items-center justify-center p-4"
    :class="isDark ? 'bg-dark-gradient' : 'bg-light-gradient'"
  >
    <div 
      class="w-full max-w-4xl relative flex flex-col md:flex-row rounded-3xl shadow-2xl overflow-hidden" 
      :class="theme('card').value"
    >
      <button 
        @click="toggleTheme"
        class="absolute top-6 right-6 z-10 w-10 h-10 flex items-center justify-center rounded-xl transition-all duration-200"
        :class="theme('headerButton').value"
        title="Cambiar tema"
      >
        <svg v-if="isDark" class="w-6 h-6 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd" />
        </svg>
        <svg v-else class="w-6 h-6 text-gray-700" fill="currentColor" viewBox="0 0 20 20">
          <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
        </svg>
      </button>

      <div 
        class="w-full md:w-1/2 p-8 sm:p-12 flex flex-col justify-center items-center text-center" 
        :class="isDark ? 'bg-gray-800' : 'bg-gradient-to-br from-blue-500 to-purple-600'"
      >
        <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mb-6 shadow-lg p-1">
           <img
            src="@/images/logo1.png"
            alt="Logo 4scan"
            class="object-contain w-full h-full"
          />
        </div>
        <h1 class="text-4xl font-bold mb-3 text-white">
          Bienvenido a 4Scan
        </h1>
        <p class="text-lg text-blue-100">
          Sistema de gestión de asistencia con reconocimiento facial.
        </p>
      </div>

      <div class="w-full md:w-1/2 p-8 sm:p-12 flex flex-col justify-center">
        <h2 class="text-3xl font-bold mb-6" :class="theme('cardTitle').value">
          Iniciar Sesión
        </h2>
        
        <form @submit.prevent="login" class="space-y-5">
          <div>
            <label class="block text-sm font-medium mb-2" :class="theme('cardSubtitle').value">
              Correo Electrónico
            </label>
            <input 
              v-model="form.email"
              type="email" 
              placeholder="tu@correo.com"
              class="w-full rounded-xl px-4 py-3"
              :class="theme('input').value"
              required
            >
          </div>
          
          <div>
            <label class="block text-sm font-medium mb-2" :class="theme('cardSubtitle').value">
              Contraseña
            </label>
            <div class="relative">
              <input 
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'" 
                placeholder="••••••••"
                class="w-full rounded-xl px-4 py-3 pr-10"
                :class="theme('input').value"
                required
              >
              <button 
                type="button" 
                @click="showPassword = !showPassword"
                class="absolute inset-y-0 right-0 px-3 flex items-center text-sm"
                :class="theme('cardSubtitle').value"
                title="Mostrar/Ocultar contraseña"
              >
                <i class="fas" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
              </button>
            </div>
          </div>

          <div 
            v-if="error" 
            class="text-red-500 text-sm font-medium p-3 rounded-xl bg-red-500/10 border border-red-500/20"
          >
            {{ error }}
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
        
        <div class="text-center w-full space-y-3 mt-6">
            <a 
              href="#" 
              class="text-sm font-medium transition-colors block"
              :class="isDark ? 'text-gray-300 hover:text-white' : 'text-gray-600 hover:text-gray-900'"
            >
              ¿Olvidaste tu contraseña?
            </a>

            <router-link 
              to="/registro"
              class="text-sm font-medium block"
              :class="isDark ? 'text-gray-300 hover:text-white' : 'text-gray-600 hover:text-gray-900'"
            >
              Ir a Registrar Asistencia <i class="fas fa-arrow-right ml-1"></i>
            </router-link>
        </div>
        </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

import { useTheme } from '@/composables/useTheme'
import { useAuth } from '@/composables/useAuth'

const { theme, isDark, toggleTheme } = useTheme()
const { fetchUsuarioActual } = useAuth()
const router = useRouter()

const form = ref({ email: '', password: '' })
const showPassword = ref(false)
const loading = ref(false)
const error = ref(null)

const login = async () => {
  loading.value = true
  error.value = null

  try {
    const response = await axios.post('/login', form.value)
    localStorage.setItem('auth_token', response.data.access_token)
    localStorage.setItem('user_data', JSON.stringify(response.data.user))
    // Force refresh user data
    await fetchUsuarioActual(true)
    
    const user = response.data.user
    if (user.rol === 'secretaria') {
      router.push('/personal')
    } else {
      router.push('/home')
    }
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