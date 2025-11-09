<template>
  <div 
    class="w-full min-h-screen flex items-center justify-center p-4 text-center"
    :class="isDark ? 'bg-dark-gradient' : 'bg-light-gradient'"
  >
    <div>
      <h1 
        class="text-9xl font-extrabold" 
        :class="isDark ? 'text-blue-400' : 'text-blue-600'"
      >
        404
      </h1>
      <p class="text-3xl font-bold mt-4" :class="theme('cardTitle').value">
        Esta página no pudo ser encontrada.
      </p>
      <p class="text-lg mt-2" :class="theme('cardSubtitle').value">
        Lo sentimos, la ruta a la que intentas acceder no existe.
      </p>
      <div class="mt-8">
        <router-link 
          to="/home"
          class="inline-flex items-center gap-2 px-6 py-3 rounded-xl font-semibold shadow-lg hover:scale-105 transition-all duration-200"
          :class="theme('buttonPrimary').value"
        >
          <i class="fas fa-home"></i>
          Volver al Inicio
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useTheme } from '../composables/useTheme'
// Importar isDark para el fondo
const { theme, isDark } = useTheme()
</script>