<template>
  <div>
    <!-- Aquí irá la tabla de empleados -->
    <p>Tabla de empleados</p>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const empleados = ref([])

// Aquí puedes recibir props si la tabla usa datos dinámicos
defineProps({
  empleados: {
    type: Array,
    default: () => []
  }
})
</script>

<style scoped>
/* Estilos de la tabla */
</style>
