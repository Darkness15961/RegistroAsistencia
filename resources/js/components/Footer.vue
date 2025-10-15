<!-- resources/js/components/Footer.vue -->
<template>
  <footer class="text-white py-2">
    <div class="max-w-6xl mx-auto px-4 text-center">

      <div class="flex flex-wrap justify-center gap-4 text-xs sm:text-sm text-gray-300">
          <span class="text-sm">Barrios Morales, Bruce Saul - 100%</span>
          <span class="text-sm">Ñaupa Yapura, Eduardo - 100%</span>
          <span class="text-sm">Oyola Saloma, Anthony Dennis - 100%</span>
      </div>

      <p class="mt-1 text-xs text-gray-500">
        © 2025 Registro de Asistencia
      </p>
    </div>
  </footer>
</template>

<script setup>
import { ref } from 'vue'

// Emite evento al padre para colapsar sidebar
defineEmits(['toggle-sidebar'])

const titulo = ref('Dashboard')
</script>
