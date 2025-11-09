<template>
  <footer
    class="w-full py-4 text-center text-sm border-t border-gray-200 dark:border-gray-700
           text-gray-600 dark:text-gray-400 bg-white/50 dark:bg-gray-900/50 backdrop-blur-md"
  >
    <p>
      © {{ year }} Sistema de Registro de Asistencia — Todos los derechos reservados.
    </p>
  </footer>
</template>

<script setup>
const year = new Date().getFullYear()
</script>
