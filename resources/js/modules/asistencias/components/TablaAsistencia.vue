<template>
  <div class="w-full">
    <div class="mb-6">
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
          <h1 class="text-2xl sm:text-3xl font-bold mb-2" :class="theme('cardTitle').value">
            <i class="fas fa-calendar-check mr-2"></i>
            {{ nombreGrupo }}
          </h1>
          <p :class="theme('cardSubtitle').value" class="text-sm">
            Resumen de asistencias semanales para este grupo
          </p>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-4 w-full sm:w-auto">
          <button 
            @click="$emit('volver')"
            class="flex items-center justify-center gap-2 px-6 py-3 rounded-xl font-semibold shadow-lg"
            :class="theme('buttonSecondary').value" 
          >
            <i class="fas fa-arrow-left"></i>
            Volver a Grupos
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
                <p>No se encontraron asistencias para este grupo</p>
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
              <td v-for="(dia, diaKey) in ['lunes', 'martes', 'miercoles', 'jueves', 'viernes']"
                  :key="diaKey"
                  class="text-center px-4 py-4 cursor-pointer group/cell relative hover:bg-gray-700/30"
                  @click="$emit('editar-asistencia', {asistencia, dia: dia, estado: asistencia[dia]})"
              >
                <span :class="estadoClase(asistencia[dia])">{{ asistencia[dia] || '-' }}</span>
                <i class="fas fa-edit absolute top-0 right-0 text-xs text-blue-500 opacity-0 group-hover/cell:opacity-100 p-1"></i>
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

const props = defineProps({
  asistencias: {
    type: Array,
    required: true
  },
  nombreGrupo: {
    type: String,
    default: 'Asistencias'
  }
})

defineEmits(['volver', 'editar-asistencia'])

const search = ref('')

const filteredAsistencias = computed(() =>
  props.asistencias.filter(a =>
    (a.persona?.nombre_completo || '')
      .toLowerCase()
      .includes(search.value.toLowerCase())
  )
)

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
  return [base, isDark.value ? 'bg-white/5 text-gray-500' : 'bg-gray-100 text-gray-400']
}
</script>