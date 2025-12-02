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
          
          <button 
            v-if="esGrupoPersonal"
            @click="$emit('ver-salidas')"
            class="flex items-center justify-center gap-2 px-6 py-3 rounded-xl font-semibold shadow-lg"
            :class="theme('buttonPrimary').value" 
          >
            <i class="fas fa-clock"></i>
            Ver Salidas
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
      <div class="overflow-x-auto max-w-[calc(100vw-2rem)] sm:max-w-full">
        <table class="w-full text-left">
          <thead :class="theme('tableHeader').value">
            <tr class="border-b" :class="isDark ? 'border-white/20' : 'border-gray-200'">
              <th class="px-6 py-4 font-bold text-sm uppercase min-w-[250px]" :class="theme('cardSubtitle').value">Nombre</th>
              <th class="px-6 py-4 font-bold text-sm uppercase" :class="theme('cardSubtitle').value">Cargo / Grado</th>
              
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
            <tr v-if="filteredAsistencias.length === 0">
              <td :colspan="diasMostrados.length + 2" class="text-center p-8" :class="theme('cardSubtitle').value">
                <i class="fas fa-calendar-times text-5xl mb-3 opacity-50"></i>
                <p>No se encontraron asistencias para este grupo</p>
              </td>
            </tr>
            <tr 
              v-for="(asistencia, index) in filteredAsistencias" 
              :key="asistencia.id_persona"
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
                    :class="getAvatarGradient(asistencia.id_persona)"
                  >
                    {{ getInitials(asistencia.persona?.nombre_completo) }}
                  </div>
                  
                  <div>
                    <span class="font-semibold block" :class="theme('cardTitle').value">
                      {{ asistencia.persona?.nombre_completo || 'Sin nombre' }}
                    </span>
                  </div>
                </div>
              </td>

              <td class="px-6 py-4" :class="theme('cardSubtitle').value">
                {{ asistencia.persona?.cargo_grado || '-' }}
              </td>
              
              <td v-for="dia in diasMostrados"
                  :key="dia"
                  class="text-center px-4 py-4 cursor-pointer group/cell relative hover:bg-gray-700/30"
                  @click="$emit('editar-asistencia', {asistencia, dia: dia, estado: asistencia[dia]?.estado})"
              >
                <div class="flex flex-col items-center justify-center gap-1">
                  <span :class="estadoClase(asistencia[dia]?.estado)">{{ asistencia[dia]?.estado || '-' }}</span>
                </div>
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
  },
  esGrupoPersonal: {
    type: Boolean,
    default: false
  }
})

defineEmits(['volver', 'editar-asistencia', 'ver-salidas'])

const search = ref('')

const filteredAsistencias = computed(() =>
  props.asistencias.filter(a =>
    (a.persona?.nombre_completo || '')
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

// --- Lógica de Días Dinámicos ---
const diasHeader = {
  lunes: 'Lun',
  martes: 'Mar',
  miercoles: 'Mié',
  jueves: 'Jue',
  viernes: 'Vie',
  sabado: 'Sáb',
  domingo: 'Dom'
}

const mostrarDomingo = computed(() => {
  return props.asistencias.some(a => 
    a.persona?.tipo_persona === 'empleado' ||
    a.persona?.tipo_persona === 'docente' ||
    a.persona?.tipo_persona === 'administrativo'
  )
})

const diasMostrados = computed(() => {
  if (mostrarDomingo.value) {
    return ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'];
  }
  return ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];
})

const estadoClase = (valor) => {
  const v = (valor || '').toUpperCase()
  const base = 'inline-flex items-center justify-center w-8 h-8 rounded-full text-xs font-bold shadow-sm transition-transform hover:scale-110'
  
  if (v === 'P') {
    return [base, isDark.value ? 'bg-green-500/20 text-green-300 border border-green-500/30' : 'bg-green-100 text-green-800 border border-green-200']
  }
  if (v === 'T') {
    return [base, isDark.value ? 'bg-yellow-500/20 text-yellow-300 border border-yellow-500/30' : 'bg-yellow-100 text-yellow-800 border border-yellow-200']
  }
  if (v === 'F') {
    return [base, isDark.value ? 'bg-red-500/20 text-red-300 border border-red-500/30' : 'bg-red-100 text-red-800 border border-red-200']
  }
  // Estado por defecto (null/-)
  return [base, isDark.value ? 'bg-white/5 text-gray-500 border border-white/5' : 'bg-gray-50 text-gray-400 border border-gray-100']
}
</script>