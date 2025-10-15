<template>
  <div 
    class="backdrop-blur-xl rounded-2xl p-6 transition-all duration-300 hover:shadow-2xl hover:scale-105"
    :class="theme('card').value"
  >
    <!-- Header: Avatar + Badge -->
    <div class="flex items-start justify-between mb-4">
      <div class="relative">
        <div 
          class="w-16 h-16 rounded-full flex items-center justify-center"
          :class="user.avatarGradient"
        >
          <span class="text-white text-xl font-bold">{{ user.initials }}</span>
        </div>
        <div 
          class="absolute -bottom-1 -right-1 w-5 h-5 border-2 rounded-full"
          :class="[
            statusColors[user.status],
            isDark ? 'border-gray-900' : 'border-white'
          ]"
        ></div>
      </div>
      <span 
        class="text-xs px-3 py-1 rounded-full font-semibold"
        :class="roleColors[user.role]"
      >
        {{ user.role }}
      </span>
    </div>

    <!-- Info: Nombre + Cargo -->
    <div class="mb-4">
      <h3 
        class="font-bold text-lg mb-1"
        :class="theme('cardTitle').value"
      >
        {{ user.name }}
      </h3>
      <p 
        class="text-sm"
        :class="theme('cardSubtitle').value"
      >
        {{ user.position }}
      </p>
    </div>

    <!-- Detalles: Email + Último acceso -->
    <div class="space-y-2 mb-4">
      <div 
        class="flex items-center gap-2 text-sm"
        :class="theme('cardSubtitle').value"
      >
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
        </svg>
        <span class="truncate">{{ user.email }}</span>
      </div>
      <div 
        class="flex items-center gap-2 text-sm"
        :class="theme('cardSubtitle').value"
      >
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span>{{ user.lastAccess }}</span>
      </div>
    </div>

    <!-- Acciones -->
    <div class="flex gap-2">
      <button 
        class="flex-1 py-2 rounded-lg transition-colors text-sm font-medium"
        :class="theme('buttonSecondary').value"
        @click="$emit('view-profile', user)"
      >
        Ver Perfil
      </button>
      <button 
        class="p-2 rounded-lg transition-colors"
        :class="theme('buttonSecondary').value"
        @click="$emit('open-menu', user)"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
        </svg>
      </button>
    </div>
  </div>
</template>

<script setup>
import { useTheme } from '../composables/useTheme'

const { theme, isDark } = useTheme()

defineProps({
  user: {
    type: Object,
    required: true
  }
})

defineEmits(['view-profile', 'open-menu'])

// Colores por rol
const roleColors = {
  'Administrador': 'bg-blue-500/20 text-blue-300',
  'Docente': 'bg-purple-500/20 text-purple-300',
  'Secretaría': 'bg-orange-500/20 text-orange-300',
  'Psicología': 'bg-red-500/20 text-red-300',
  'Tutora': 'bg-pink-500/20 text-pink-300',
  'Tutor': 'bg-cyan-500/20 text-cyan-300'
}

// Colores por estado
const statusColors = {
  'online': 'bg-green-500',
  'away': 'bg-yellow-500',
  'offline': 'bg-gray-500'
}
</script>