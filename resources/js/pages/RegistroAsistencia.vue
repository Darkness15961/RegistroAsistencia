<template>
  <div 
    class="w-full min-h-screen flex flex-col items-center justify-center p-4"
    :class="isDark ? 'bg-dark-gradient' : 'bg-light-gradient'"
  >
    
    <div 
      class="w-full max-w-6xl flex flex-col md:flex-row rounded-3xl shadow-2xl overflow-hidden" 
      :class="theme('card').value"
    >
      
      <div 
        class="w-full md:w-1/2 p-8 sm:p-12 flex flex-col justify-center items-center text-center"
        :class="isDark ? 'bg-gray-800' : 'bg-gradient-to-br from-blue-500 to-purple-600'"
      >
        <div class="w-20 h-20 bg-gradient-to-br from-blue-400 to-cyan-400 rounded-full flex items-center justify-center mb-6 shadow-lg">
          <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
          </svg>
        </div>
        
        <h1 class="text-3xl font-bold mb-4" :class="isDark ? 'text-white' : 'text-white'">
          Registro de Asistencia
        </h1>
        
        <div class="font-mono text-6xl font-extrabold" :class="isDark ? 'text-cyan-300' : 'text-white'">
          {{ horaActual }}
        </div>
        
        <p class="text-xl mt-2" :class="isDark ? 'text-gray-300' : 'text-blue-100'">
          {{ fechaActual }}
        </p>
      </div>
      
      <div class="w-full md:w-1/2 p-8 sm:p-12 flex flex-col justify-center items-center relative">
        
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
          class="w-full h-64 bg-black/50 rounded-2xl flex items-center justify-center text-center p-4 border"
          :class="theme('input').value"
        >
          <div :class="theme('cardSubtitle').value">
            <i class="fas fa-video text-5xl mb-4"></i>
            <p class="font-medium">Iniciando cámara para reconocimiento facial...</p>
          </div>
        </div>
        
        <div class="mt-6 text-center">
          <p class="text-lg font-medium" :class="theme('cardTitle').value">
            Por favor, mire a la cámara
          </p>
          <p class="text-sm" :class="theme('cardSubtitle').value">
            El registro se tomará automáticamente.
          </p>
        </div>
        
         <router-link 
            to="/login"
            class="text-sm font-medium mt-6"
            :class="isDark ? 'text-gray-400 hover:text-white' : 'text-gray-600 hover:text-gray-900'"
          >
            Ir a Iniciar Sesión <i class="fas fa-arrow-right ml-1"></i>
          </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useTheme } from '../composables/useTheme'

const { theme, isDark, toggleTheme } = useTheme()

const horaActual = ref('')
const fechaActual = ref('')
let timerId = null

const actualizarHora = () => {
  const now = new Date()
  horaActual.value = now.toLocaleTimeString('es-PE', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true })
  fechaActual.value = now.toLocaleDateString('es-PE', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })
}

onMounted(() => {
  actualizarHora()
  timerId = setInterval(actualizarHora, 1000)
})

onUnmounted(() => {
  clearInterval(timerId)
})
</script>