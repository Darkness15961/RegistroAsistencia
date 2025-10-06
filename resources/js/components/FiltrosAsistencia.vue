<template>
  <div class="flex flex-wrap gap-3 mb-6">
    <button
      v-for="filtro in filtros"
      :key="filtro.id"
      @click="$emit('seleccionar', filtro)"
      :class="[
        'px-5 py-2.5 rounded-xl font-medium text-sm transition-all border backdrop-blur-xl',
        filtroSeleccionado?.id === filtro.id
          ? 'bg-gradient-to-r from-cyan-400 to-blue-500 text-white border-cyan-400 shadow-lg scale-105'
          : 'bg-white/10 hover:bg-white/20 border-white/20 text-gray-200'
      ]"
    >
      <i :class="`fas fa-${filtro.icono} mr-2`"></i>
      {{ filtro.nombre }}
    </button>
  </div>
</template>

<script setup>
defineProps({
  filtros: {
    type: Array,
    default: () => []
  },
  filtroSeleccionado: {
    type: Object,
    default: null
  }
})

defineEmits(['seleccionar'])
</script>
