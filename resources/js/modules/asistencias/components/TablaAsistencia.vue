<template>
  <div class="w-full">
    <div class="mb-6">
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
          <h1 class="text-2xl sm:text-3xl font-bold mb-2" :class="theme('cardTitle').value">
            <i class="fas fa-calendar-check mr-2"></i>
            {{ nombreArea }}
          </h1>
          <p :class="theme('cardSubtitle').value" class="text-sm">
            Resumen de asistencias semanales para esta área
          </p>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-4 w-full sm:w-auto">
          <button 
            @click="$emit('volver')"
            class="flex items-center justify-center gap-2 px-6 py-3 rounded-xl font-semibold shadow-lg hover:scale-105 transition-all duration-200 whitespace-nowrap"
            :class="theme('buttonSecondary').value" 
          >
            <i class="fas fa-arrow-left"></i>
            Volver a Áreas
          </button>
        </div>
      </div>

      <div class="relative w-full sm:max-w-md">
        <input
          v-model="search"
          type="text"
          placeholder="Buscar por nombre..."
          class="w-full rounded-xl pl-10 pr-4 py-3 outline-none border transition-colors"
          :class="theme('input').value"
        />
        <i 
          class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2"
          :class="theme('cardSubtitle').value"
        ></i>
      </div>
    </div>

    <div 
      class="backdrop-blur-xl border rounded-3xl overflow-hidden shadow-lg"
      :class="theme('card').value"
    >
      <div class="overflow-x-auto">
        <table class="w-full text-left">
          <thead :class="theme('tableHeader').value">
            <tr class="border-b" :class="isDark ? 'border-white/20' : 'border-gray-200'">
              <th class="px-6 py-4 font-bold text-sm uppercase" :class="theme('cardSubtitle').value">Nombre</th>
              <th class="px-6 py-4 font-bold text-sm uppercase" :class="theme('cardSubtitle').value">Cargo / Grado</th>
              <th class="text-center px-4 py-4 font-bold text-sm uppercase" :class="theme('cardSubtitle').value">Lun</th>
              <th class="text-center px-4 py-4 font-bold text-sm uppercase" :class="theme('cardSubtitle').value">Mar</th>
              <th class="text-center px-4 py-4 font-bold text-sm uppercase" :class="theme('cardSubtitle').value">Mié</th>
              <th class="text-center px-4 py-4 font-bold text-sm uppercase" :class="theme('cardSubtitle').value">Jue</th>
              <th class="text-center px-4 py-4 font-bold text-sm uppercase" :class="theme('cardSubtitle').value">Vie</th>
            </tr>
          </thead>
          <tbody class="divide-y" :class="isDark ? 'divide-white/10' : 'divide-gray-200'">
            <tr v-if="filteredAsistencias.length === 0">
              <td colspan="7" class="text-center p-8" :class="theme('cardSubtitle').value">
                <i class="fas fa-calendar-times text-5xl mb-3 opacity-50"></i>
                <p>No se encontraron asistencias para esta área</p>
              </td>
            </tr>
            <tr 
              v-for="asistencia in filteredAsistencias" 
              :key="asistencia.id_persona"
              class="transition-colors"
              :class="theme('tableRow').value"
            >
              <td class="px-6 py-4">
                <span class="font-semibold" :class="theme('cardTitle').value">{{ asistencia.persona?.nombre_completo || 'Sin nombre' }}</span>
              </td>
              <td class="px-6 py-4" :class="theme('cardSubtitle').value">
                {{ asistencia.persona?.cargo_grado || '-' }}
              </td>
              <td class="text-center px-4 py-4">
                <span :class="estadoClase(asistencia.lunes)">{{ asistencia.lunes || '-' }}</span>
              </td>
              <td class="text-center px-4 py-4">
                <span :class="estadoClase(asistencia.martes)">{{ asistencia.martes || '-' }}</span>
              </td>
              <td class="text-center px-4 py-4">
                <span :class="estadoClase(asistencia.miercoles)">{{ asistencia.miercoles || '-' }}</span>
              </td>
              <td class="text-center px-4 py-4">
                <span :class="estadoClase(asistencia.jueves)">{{ asistencia.jueves || '-' }}</span>
              </td>
              <td class="text-center px-4 py-4">
                <span :class="estadoClase(asistencia.viernes)">{{ asistencia.viernes || '-' }}</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useTheme } from '@/composables/useTheme'

const { theme, isDark } = useTheme()

// Aceptamos props en lugar de cargar datos aquí
const props = defineProps({
  asistencias: {
    type: Array,
    required: true
  },
  nombreArea: {
    type: String,
    default: 'Asistencias'
  }
})

defineEmits(['volver'])

const search = ref('')

/**
 * Filtrar por nombre (basado en el prop)
 */
const filteredAsistencias = computed(() =>
  props.asistencias.filter(a =>
    (a.persona?.nombre_completo || '')
      .toLowerCase()
      .includes(search.value.toLowerCase())
  )
)

/**
 * Colores según estado (P, T, F)
 */
const estadoClase = (valor) => {
  const v = (valor || '').toUpperCase()
  const base = 'inline-flex items-center justify-center w-7 h-7 rounded-full text-xs font-bold'
  
  if (v === 'P') {
    return [base, isDark.value ? 'bg-green-500/20 text-green-300' : 'bg-green-100 text-green-800']
  }
  if (v === 'T') {
    return [base, isDark.value ? 'bg-yellow-500/20 text-yellow-300' : 'bg-yellow-100 text-yellow-800']
  }
  if (v === 'F') {
    return [base, isDark.value ? 'bg-red-500/20 text-red-300' : 'bg-red-100 text-red-800']
  }
  // Estado vacío o '-'
  return [base, isDark.value ? 'bg-white/5 text-gray-500' : 'bg-gray-100 text-gray-400']
}
</script>