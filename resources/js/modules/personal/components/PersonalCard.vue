<template>
  <div 
    class="backdrop-blur-xl border rounded-3xl shadow-lg p-5 transition-all hover:shadow-xl"
    :class="theme('card').value"
  >
    <div class="flex items-start justify-between gap-3 mb-4">
      <div class="flex items-center gap-3">
        <div 
          class="w-12 h-12 rounded-2xl object-cover shadow-lg bg-gradient-to-br from-blue-400 to-cyan-500 text-white flex items-center justify-center font-bold text-lg"
          :class="getAvatarGradient(persona.id_persona)"
        >
          {{ getInitials(persona) }}
        </div>
        
        <div>
          <h3 class="text-lg font-semibold leading-tight" :class="theme('cardTitle').value">
            {{ persona.nombre_completo }}
          </h3>
          <span class="text-xs px-2 py-0.5 rounded-full mt-1 inline-block border" 
                :class="isDark ? 'bg-white/10 border-white/10 text-gray-300' : 'bg-gray-100 border-gray-200 text-gray-600'">
            DNI: {{ persona.dni }}
          </span>
        </div>
      </div>

      <div class="flex gap-2">
        <button 
          @click="$emit('editar', persona)"
          class="p-2 w-9 h-9 rounded-xl transition-all group flex items-center justify-center"
          :class="isDark ? 'bg-blue-500/20 hover:bg-blue-500/30' : 'bg-blue-50 hover:bg-blue-100'"
          title="Editar"
        >
          <i class="fas fa-edit text-sm" :class="isDark ? 'text-blue-300' : 'text-blue-600'"></i>
        </button>
        <button 
          @click="$emit('eliminar', persona.id_persona)"
          class="p-2 w-9 h-9 rounded-xl transition-all group flex items-center justify-center"
          :class="isDark ? 'bg-red-500/20 hover:bg-red-500/30' : 'bg-red-50 hover:bg-red-100'"
          title="Eliminar"
        >
          <i class="fas fa-trash text-sm" :class="isDark ? 'text-red-300' : 'text-red-600'"></i>
        </button>
      </div>
    </div>

    <div class="flex flex-col gap-2 pt-3 border-t" :class="isDark ? 'border-white/10' : 'border-gray-100'">
      
      <div class="flex items-center gap-2">
        <i class="fas fa-briefcase text-xs w-5 text-center opacity-70" :class="theme('cardSubtitle').value"></i>
        <span class="text-sm font-medium" :class="theme('cardTitle').value">
          {{ persona.cargo_grado || 'Sin cargo' }}
        </span>
      </div>

      <div class="flex items-center gap-2">
        <i class="fas fa-envelope text-xs w-5 text-center opacity-70" :class="theme('cardSubtitle').value"></i>
        <span class="text-sm" :class="theme('cardSubtitle').value">
          {{ persona.correo }}
        </span>
      </div>

      <div v-if="persona.area" class="flex items-center gap-2">
        <i class="fas fa-building text-xs w-5 text-center opacity-70" :class="theme('cardSubtitle').value"></i>
        <span class="text-sm" :class="theme('cardSubtitle').value">
          {{ persona.area.nombre_area }}
        </span>
      </div>

      <div class="mt-2">
        <span 
          class="px-2.5 py-1 rounded-full text-xs font-bold inline-flex items-center gap-1.5 border"
          :class="persona.estado === 'activo' 
            ? (isDark ? 'bg-green-500/10 border-green-500/20 text-green-300' : 'bg-green-50 border-green-100 text-green-700') 
            : (isDark ? 'bg-red-500/10 border-red-500/20 text-red-300' : 'bg-red-50 border-red-100 text-red-700')"
        >
          <i class="fas fa-circle text-[0.5rem]" :class="persona.estado === 'activo' ? 'text-green-500' : 'text-red-500'"></i>
          {{ persona.estado ? persona.estado.toUpperCase() : 'DESCONOCIDO' }}
        </span>
      </div>

    </div>
  </div>
</template>

<script setup>
import { useTheme } from '@/composables/useTheme'

const { theme, isDark } = useTheme()

defineProps({
  persona: { // Renombrado a 'persona'
    type: Object,
    required: true
  }
})

defineEmits(['editar', 'eliminar'])

// --- Lógica Visual de Avatares (Copiada de TablaUsuario) ---
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

const getInitials = (persona) => {
  const name = persona.nombre_completo
  if (name) {
    const matches = name.match(/\b\w/g) || []
    return ((matches.shift() || '') + (matches.shift() || '')).toUpperCase()
  }
  return '??'
}
</script>