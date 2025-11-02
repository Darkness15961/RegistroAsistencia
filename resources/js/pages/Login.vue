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
        :class="isDark
          ? 'bg-gray-800'
          : 'bg-gradient-to-br from-blue-500 to-purple-600'"
      >

        <div class="w-20 h-20 bg-gradient-to-br from-blue-400 to-cyan-400 rounded-full flex items-center justify-center mb-6 shadow-lg">
          <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
          </svg>
        </div>

        <h1 class="text-4xl font-bold mb-3" :class="isDark ? 'text-white' : 'text-white'">
          Bienvenido a 4scan
        </h1>
        <p class="text-lg" :class="isDark ? 'text-gray-300' : 'text-blue-100'">
          Tu sistema de gestión de asistencia.
        </p>
      </div>

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

          <div class="flex items-center justify-between">
            <a
              href="#"
              class="text-sm font-medium transition-colors"
              :class="isDark
                ? 'text-gray-300 hover:text-white'
                : 'text-gray-600 hover:text-gray-900'"
            >
              ¿Olvidaste tu contraseña?
            </a>
          </div>

          <button
            type="submit"
            class="w-full flex items-center justify-center gap-2 px-6 py-3 rounded-xl font-semibold shadow-lg hover:scale-105 transition-all duration-200"
            :class="theme('buttonPrimary').value"
          >
            Ingresar
          </button>
        </form>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useTheme } from '../composables/useTheme'
import { useRouter } from 'vue-router'

const { theme, isDark, toggleTheme } = useTheme()
const router = useRouter()

const form = ref({
  email: '',
  password: ''
})

const login = () => {
  console.log('Intento de login:', form.value)
  // Lógica de autenticación...

  // Simulación de login exitoso:
  router.push('/home')
}
</script>
