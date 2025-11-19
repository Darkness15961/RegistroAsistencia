<template>
  <div 
    class="backdrop-blur-xl rounded-2xl p-6 transition-all duration-300 border"
    :class="theme('card').value"
  >
    <div class="flex items-start justify-between mb-4">
      <div class="relative">
        <div 
          class="w-16 h-16 rounded-full flex items-center justify-center"
          :class="avatarGradient"
        >
          <span class="text-white text-xl font-bold">{{ initials }}</span>
        </div>
        <div 
          class="absolute -bottom-1 -right-1 w-5 h-5 border-2 rounded-full"
          :class="[
            statusColors[usuario.estado] || 'bg-gray-500',
            isDark ? 'border-gray-900' : 'border-white'
          ]"
        ></div>
      </div>
      <span 
        class="text-xs px-3 py-1 rounded-full font-semibold capitalize"
        :class="roleClasses" >
        {{ usuario.rol }}
      </span>
    </div>

    <div class="mb-4">
      <h3 
        class="font-bold text-lg mb-1 truncate"
        :class="theme('cardTitle').value"
        :title="usuario.persona?.nombre_completo || 'Usuario sin persona asignada'"
      >
        {{ usuario.persona?.nombre_completo || 'Usuario sin persona' }}
      </h3>
      <p 
        class="text-sm capitalize"
        :class="theme('cardSubtitle').value"
      >
        {{ usuario.rol }}
      </p>
    </div>

    <div class="space-y-2 mb-4">
      <div 
        class="flex items-center gap-2 text-sm"
        :class="theme('cardSubtitle').value"
      >
        <i class="fas fa-envelope w-4 text-center"></i>
        <span class="truncate" :title="usuario.email">{{ usuario.email }}</span>
      </div>
    </div>

    <div class="flex gap-2">
      <button 
        class="flex-1 py-2 rounded-lg transition-colors text-sm font-medium flex items-center justify-center gap-2"
        :class="theme('buttonSecondary').value"
        @click="$emit('edit', usuario)"
      >
        <i class="fas fa-edit w-4"></i>
        Editar
      </button>
      <button 
        class="p-2 w-10 rounded-lg transition-colors text-red-400 hover:text-red-300"
        :class="theme('buttonSecondary').value"
        @click="$emit('delete', usuario.id_usuario)"
      >
        <i class="fas fa-trash w-4"></i>
      </button>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useTheme } from '@/composables/useTheme'

const { theme, isDark } = useTheme()

const props = defineProps({
  usuario: { // Corregido: La prop ahora es 'usuario'
    type: Object,
    required: true
  }
})

defineEmits(['edit', 'delete'])

// Genera iniciales desde el nombre de la persona o el email
const initials = computed(() => {
  const name = props.usuario.persona?.nombre_completo
  if (name) {
    return name.match(/\b\w/g).join('').substring(0, 2).toUpperCase()
  }
  return props.usuario.email.substring(0, 2).toUpperCase()
})

// Clases por rol (copiado del original)
const roleClasses = computed(() => {
  const role = props.usuario.rol
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
})

// Colores por estado
const statusColors = {
  'activo': 'bg-green-500',
  'inactivo': 'bg-gray-500',
}

const gradients = [
  'from-blue-400 to-blue-600',
  'from-purple-400 to-purple-600',
  'from-green-400 to-green-600',
  'from-orange-400 to-orange-600',
  'from-red-400 to-red-600',
  'from-pink-400 to-pink-600',
  'from-cyan-400 to-cyan-600'
]
const avatarGradient = computed(() => {
  const id = props.usuario.id_usuario || 0
  return `bg-gradient-to-br ${gradients[id % gradients.length]}`
})
</script>