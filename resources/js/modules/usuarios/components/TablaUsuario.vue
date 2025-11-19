<template>
  <div 
    class="backdrop-blur-xl border rounded-3xl overflow-hidden shadow-lg"
    :class="theme('card').value"
  >
    <div class="overflow-x-auto">
      <table class="w-full text-left">
        <thead :class="theme('tableHeader').value">
          <tr class="border-b" :class="isDark ? 'border-white/20' : 'border-gray-200'">
            <th class="text-left p-4 font-bold text-sm uppercase min-w-[250px]" :class="theme('cardSubtitle').value">Nombre</th>
            <th class="text-left p-4 font-bold text-sm uppercase" :class="theme('cardSubtitle').value">Email de Acceso</th>
            <th class="text-left p-4 font-bold text-sm uppercase" :class="theme('cardSubtitle').value">Rol</th>
            <th class="text-center p-4 font-bold text-sm uppercase" :class="theme('cardSubtitle').value">Estado</th>
            <th class="text-center p-4 font-bold text-sm uppercase min-w-[120px]" :class="theme('cardSubtitle').value">Acciones</th>
          </tr>
        </thead>
        <tbody class="divide-y" :class="isDark ? 'divide-white/10' : 'divide-gray-200'">
          <tr 
            v-for="(usuario, index) in usuarios" 
            :key="usuario.id_usuario"
            class="transition-colors"
            :class="[
              theme('tableRow').value,
              index % 2 === 0 ? (isDark ? 'bg-white/[0.03]' : 'bg-gray-50/50') : ''
            ]"
          >
            <td class="p-4">
              <div class="flex items-center gap-3">
                <div 
                  class="w-10 h-10 rounded-xl object-cover shadow-md flex items-center justify-center text-white font-bold text-sm"
                  :class="getAvatarGradient(usuario.id_usuario)"
                >
                  {{ getInitials(usuario) }}
                </div>

                <div>
                  <span class="font-semibold block" :class="theme('cardTitle').value">
                    {{ usuario.persona?.nombre_completo || 'Usuario sin persona' }}
                  </span>
                  <span v-if="usuario.persona?.dni" class="text-xs font-mono" :class="theme('cardSubtitle').value">
                    DNI: {{ usuario.persona.dni }}
                  </span>
                </div>
              </div>
            </td>
            
            <td class="p-4 text-sm" :class="theme('cardSubtitle').value">
              {{ usuario.email }}
            </td>

            <td class="p-4">
              <span 
                class="px-3 py-1 rounded-full text-xs font-semibold capitalize"
                :class="roleClasses(usuario.rol)" 
              >
                {{ usuario.rol }}
              </span>
            </td>

            <td class="p-4 text-center">
              <span 
                class="px-3 py-1 rounded-full text-xs font-semibold capitalize inline-flex items-center gap-1.5"
                :class="statusClasses(usuario.estado)" 
              >
                <i class="fas fa-circle text-[0.5rem]" :class="usuario.estado === 'activo' ? 'text-green-500' : 'text-red-500'"></i>
                {{ usuario.estado }}
              </span>
            </td>
            
            <td class="p-4">
              <div class="flex justify-center gap-2">
                <button 
                  @click="$emit('edit', usuario)"
                  class="p-2 w-10 h-10 rounded-xl transition-all hover:scale-110 group"
                  :class="isDark ? 'bg-blue-500/20 hover:bg-blue-500/30' : 'bg-blue-50 hover:bg-blue-100'"
                  title="Editar"
                >
                  <i class="fas fa-edit" :class="isDark ? 'text-blue-300' : 'text-blue-600'"></i>
                </button>
                <button 
                  @click="$emit('delete', usuario.id_usuario)"
                  class="p-2 w-10 h-10 rounded-xl transition-all hover:scale-110 group"
                  :class="isDark ? 'bg-red-500/20 hover:bg-red-500/30' : 'bg-red-50 hover:bg-red-100'"
                  title="Eliminar"
                >
                  <i class="fas fa-trash" :class="isDark ? 'text-red-300' : 'text-red-600'"></i>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { useTheme } from '@/composables/useTheme'

const { theme, isDark } = useTheme()

defineProps({
  usuarios: {
    type: Array,
    required: true
  }
})

defineEmits(['edit', 'delete'])

// --- Lógica Visual (Copiada y adaptada de UserCard) ---

// Colores de fondo para el avatar
const gradients = [
  'bg-gradient-to-br from-blue-400 to-blue-600',
  'bg-gradient-to-br from-purple-400 to-purple-600',
  'bg-gradient-to-br from-green-400 to-green-600',
  'bg-gradient-to-br from-orange-400 to-orange-600',
  'bg-gradient-to-br from-red-400 to-red-600',
  'bg-gradient-to-br from-pink-400 to-pink-600',
  'bg-gradient-to-br from-cyan-400 to-cyan-600'
]

// Función para obtener el color basado en el ID (consistente con UserCard)
const getAvatarGradient = (id) => {
  const safeId = id || 0
  return gradients[safeId % gradients.length]
}

// Función para obtener iniciales
const getInitials = (usuario) => {
  const name = usuario.persona?.nombre_completo
  if (name) {
    // Intenta tomar la primera letra de las dos primeras palabras
    const matches = name.match(/\b\w/g) || []
    return ((matches.shift() || '') + (matches.shift() || '')).toUpperCase()
  }
  // Fallback al email
  return usuario.email.substring(0, 2).toUpperCase()
}

// Helper para clases de Rol
const roleClasses = (role) => {
  if (isDark.value) {
    switch (role) {
      case 'administrador': return 'bg-blue-500/20 text-blue-300'
      case 'docente': return 'bg-purple-500/20 text-purple-300'
      case 'empleado': return 'bg-green-500/20 text-green-300'
      default: return 'bg-gray-500/20 text-gray-300'
    }
  } else {
    switch (role) {
      case 'administrador': return 'bg-blue-100 text-blue-800'
      case 'docente': return 'bg-purple-100 text-purple-800'
      case 'empleado': return 'bg-green-100 text-green-800'
      default: return 'bg-gray-100 text-gray-800'
    }
  }
}

// Helper para clases de Estado
const statusClasses = (estado) => {
    if (isDark.value) {
        return estado === 'activo' 
          ? 'bg-green-500/20 text-green-300 border border-green-500/30' 
          : 'bg-red-500/20 text-red-300 border border-red-500/30'
    } else {
        return estado === 'activo' 
          ? 'bg-green-50 text-green-700 border border-green-200' 
          : 'bg-red-50 text-red-700 border border-red-200'
    }
}
</script>