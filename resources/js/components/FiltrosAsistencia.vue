<template>
  <div class="flex flex-wrap gap-3">
    <button
      v-for="filtro in filtros"
      :key="filtro.id"
      @click="$emit('seleccionar', filtro)"
      :class="[
        'px-5 py-2.5 rounded-xl font-semibold text-sm transition-all border backdrop-blur-xl shadow-md',
        filtroSeleccionado?.id === filtro.id
          ? (isDark 
              ? 'bg-cyan-500/30 text-cyan-200 border-cyan-500/50 scale-105' 
              : 'bg-blue-500 text-white border-blue-500 scale-105 shadow-blue-200')
          : (isDark 
              ? 'bg-white/10 hover:bg-white/20 border-white/20 text-gray-200'
              : 'bg-white hover:bg-gray-100 border-gray-200 text-gray-700')
      ]"
    >
      <i :class="`fas fa-${filtro.icono} mr-2`"></i>
      {{ filtro.nombre }}
    </button>
  </div>
</template>

<script setup>
import { useTheme } from '../composables/useTheme'

const { isDark } = useTheme()

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