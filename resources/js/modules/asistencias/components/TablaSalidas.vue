<template>
  <div class="w-full">
    <div class="mb-6">
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
          <h1 class="text-2xl sm:text-3xl font-bold mb-2" :class="theme('cardTitle').value">
            <i class="fas fa-clock mr-2"></i>
            Registro de Salidas - {{ nombreGrupo }}
          </h1>
          <p :class="theme('cardSubtitle').value" class="text-sm">
            Horas de salida del personal
          </p>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-4 w-full sm:w-auto">
          <button 
            @click="$emit('volver')"
            class="flex items-center justify-center gap-2 px-6 py-3 rounded-xl font-semibold shadow-lg"
            :class="theme('buttonSecondary').value" 
          >
            <i class="fas fa-arrow-left"></i>
            Volver a Asistencias
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
              <th class="px-6 py-4 font-bold text-sm uppercase min-w-[250px]" :class="theme('cardSubtitle').value">Nombre</th>
              <th class="px-6 py-4 font-bold text-sm uppercase" :class="theme('cardSubtitle').value">Cargo</th>
              
              <th 
                v-for="dia in diasMostrados" 
                :key="dia" 
                class="text-center px-4 py-4 font-bold text-sm uppercase" 
                :class="theme('cardSubtitle').value"
              >
                {{ diasHeader[dia] }}
              </th>
            </tr>
          </thead>
          <tbody class="divide-y" :class="isDark ? 'divide-white/10' : 'divide-gray-200'">
            <tr v-if="filteredSalidas.length === 0">
              <td :colspan="diasMostrados.length + 2" class="text-center p-8" :class="theme('cardSubtitle').value">
                <i class="fas fa-clock text-5xl mb-3 opacity-50"></i>
                <p>No se encontraron registros de salida para este grupo</p>
              </td>
            </tr>
            <tr 
              v-for="(salida, index) in filteredSalidas" 
              :key="salida.id_persona"
              class="transition-colors"
              :class="[
                theme('tableRow').value,
                index % 2 === 0 ? (isDark ? 'bg-white/[0.03]' : 'bg-gray-50/50') : ''
              ]"
            >
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <div 
                    class="w-10 h-10 rounded-xl object-cover shadow-md flex items-center justify-center text-white font-bold text-sm flex-shrink-0"
                    :class="getAvatarGradient(salida.id_persona)"
                  >
                    {{ getInitials(salida.persona?.nombre_completo) }}
                  </div>
                  
                  <div>
                    <span class="font-semibold block" :class="theme('cardTitle').value">
                      {{ salida.persona?.nombre_completo || 'Sin nombre' }}
                    </span>
                  </div>
                </div>
              </td>

              <td class="px-6 py-4" :class="theme('cardSubtitle').value">
                {{ salida.persona?.cargo_grado || '-' }}
              </td>
              
              <td v-for="dia in diasMostrados"
                  :key="dia"
                  class="text-center px-4 py-4"
              >
                <div v-if="salida[dia]?.hora_salida" class="flex flex-col items-center justify-center gap-1">
                  <span class="font-semibold text-sm" :class="theme('cardTitle').value">
                    {{ salida[dia].hora_salida }}
                  </span>
                  <span 
                    v-if="salida[dia].fuera_tiempo" 
                    class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-full"
                    :class="isDark ? 'bg-orange-500/20 text-orange-300' : 'bg-orange-100 text-orange-800'"
                  >
                    <i class="fas fa-exclamation-triangle"></i>
                    Fuera de tiempo
                  </span>
                </div>
                <span v-else :class="theme('cardSubtitle').value">-</span>
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
  salidas: {
    type: Array,
    required: true
  },
  nombreGrupo: {
    type: String,
    default: 'Personal'
  }
})

defineEmits(['volver'])

const search = ref('')

const filteredSalidas = computed(() =>
  props.salidas.filter(s =>
    (s.persona?.nombre_completo || '')
      .toLowerCase()
      .includes(search.value.toLowerCase())
  )
)

// --- Lógica Visual de Avatares ---
const gradients = [
  'bg-gradient-to-br from-blue-400 to-blue-600',
  'bg-gradient-to-br from-purple-400 to-purple-600',
  'bg-gradient-to-br from-green-400 to-green-600',
  'bg-gradient-to-br from-orange-400 to-orange-600',
  'bg-gradient-to-br from-red-400 to-red-600',
  'bg-gradient-to-br from-pink-400 to-pink-600',
  'bg-gradient-to-br from-cyan-400 to-cyan-600'
]

const getAvatarGradient = (id) => {
  const safeId = id || 0
  return gradients[safeId % gradients.length]
}

const getInitials = (name) => {
  if (name) {
    const matches = name.match(/\b\w/g) || []
    return ((matches.shift() || '') + (matches.shift() || '')).toUpperCase()
  }
  return '?'
}

// --- Lógica de Días ---
const diasHeader = {
  lunes: 'Lun',
  martes: 'Mar',
  miercoles: 'Mié',
  jueves: 'Jue',
  viernes: 'Vie',
  sabado: 'Sáb',
  domingo: 'Dom'
}

const diasMostrados = computed(() => {
  // Personal siempre muestra todos los días
  return ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'];
})
</script>
