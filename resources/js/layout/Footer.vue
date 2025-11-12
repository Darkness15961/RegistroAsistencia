<template>
  <footer
    class="w-full p-6 text-sm border-t"
    :class="theme('header').value" 
  >
    <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-4">
      
      <p :class="theme('cardSubtitle').value">
        © {{ year }} 4Scan — Todos los derechos reservados.
      </p>
      
      <div 
        class="flex flex-col md:flex-row gap-x-6 gap-y-2 text-center md:text-right" 
        :class="theme('cardSubtitle').value"
      >
        <p>
          <span class="font-semibold" :class="theme('cardTitle').value">Desarrollado por:</span>
          Eduardo Ñaupa Yapura
        </p>
        <p>
          <span class="font-semibold" :class="theme('cardTitle').value">Diseñado por:</span>
          Bruce Saul Barrios Morales
        </p>
        <p>
          <span class="font-semibold" :class="theme('cardTitle').value">Gestionado por:</span>
          Anthony Denis Oyola Saloma
        </p>
      </div>
    </div>
  </footer>
</template>

<script setup>
import { useTheme } from '@/composables/useTheme' // Importa tu composable de tema

const { theme } = useTheme() // Inicializa el tema
const year = new Date().getFullYear()
</script>