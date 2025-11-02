<template>
  <div
    class="absolute top-full right-0 mt-3 w-64 rounded-3xl border shadow-2xl p-4 z-50"
    :class="isDark 
      ? 'bg-gradient-to-br from-gray-800 to-gray-900 border-gray-700' 
      : 'bg-white border-gray-200'"
  >
    <div class="flex items-center gap-3 pb-4 border-b" :class="isDark ? 'border-white/10' : 'border-gray-200'">
      <div class="w-10 h-10 bg-gradient-to-br from-purple-400 to-pink-400 rounded-full flex items-center justify-center">
        <span class="text-white font-semibold">U</span>
      </div>
      <div>
        <p class="text-sm font-semibold" :class="theme('cardTitle').value">Usuario</p>
        <p class="text-xs" :class="theme('cardSubtitle').value">admin@sis.com</p>
      </div>
    </div>
    
    <nav class="mt-4">
      <ul>
        <li>
          <a 
            href="#" 
            class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all"
            :class="menuItemClass"
          >
            <i class="fas fa-user w-4 text-center" :class="theme('cardSubtitle').value"></i>
            <span class="text-sm font-medium">Mi Perfil</span>
          </a>
        </li>
        <li>
          <a 
            href="#" 
            class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all"
            :class="menuItemClass"
          >
            <i class="fas fa-cog w-4 text-center" :class="theme('cardSubtitle').value"></i>
            <span class="text-sm font-medium">Configuración</span>
          </a>
        </li>
        <li class="mt-2 pt-2 border-t" :class="isDark ? 'border-white/10' : 'border-gray-200'">
          <a 
            href="#"
            @click.prevent="$emit('logout')"
            class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all"
            :class="menuItemDangerClass"
          >
            <i class="fas fa-sign-out-alt w-4 text-center"></i>
            <span class="text-sm font-medium">Cerrar Sesión</span>
          </a>
        </li>
      </ul>
    </nav>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useTheme } from '../composables/useTheme'

const { theme, isDark } = useTheme()
defineEmits(['logout'])

// CORRECCIÓN: Se añadió el color del texto (theme('cardTitle').value)
const menuItemClass = computed(() => {
  const textColor = theme('cardTitle').value; // 'text-white' o 'text-gray-900'
  const hoverBg = isDark.value ? 'hover:bg-white/10' : 'hover:bg-gray-100';
  return [textColor, hoverBg];
})

// Esta ya estaba correcta, define su propio color de texto (rojo)
const menuItemDangerClass = computed(() => {
  return isDark.value
    ? 'text-red-400 hover:bg-red-500/20'
    : 'text-red-600 hover:bg-red-50'
})
</script>