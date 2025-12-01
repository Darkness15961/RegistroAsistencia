<template>
  <header 
    class="sticky top-0 z-30 transition-colors duration-300 rounded-b-3xl"
    :class="theme('header').value"
  >
    <div class="px-6 py-4">
      <div class="flex items-center justify-between">
        
        <div class="flex items-center gap-4">
          <button 
            @click="$emit('toggle-sidebar')"
            class="w-10 h-10 flex items-center justify-center rounded-xl transition-all duration-200"
            :class="theme('headerButton').value"
            title="Abrir/Cerrar menú"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4 6h16M4 12h16M4 18h16" />
            </svg>
          </button>

          <div class="flex items-center gap-2 text-sm">
            <svg class="w-5 h-5" :class="theme('headerBreadcrumb').value" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span :class="theme('headerBreadcrumb').value">4scan</span>
            <span :class="theme('headerBreadcrumbSeparator').value">></span>
            <span class="font-medium" :class="theme('cardTitle').value">{{ titulo }}</span>
          </div>
        </div>

        <div class="flex items-center gap-3 md:gap-4 md:mr-4">

          <button 
            @click="toggleTheme"
            class="w-10 h-10 flex items-center justify-center rounded-xl transition-all duration-200"
            :class="theme('headerButton').value"
            title="Cambiar tema"
          >
            <svg v-if="isDark" class="w-6 h-6 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd" />
            </svg>
            <svg v-else class="w-6 h-6 text-gray-700" fill="currentColor" viewBox="0 0 20 20">
              <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
            </svg>
          </button>

          <div class="relative" ref="dropdownRef">
            <button 
              @click="toggleDropdown"
              type="button"
              class="flex items-center gap-3 px-3 py-2 rounded-xl transition-all cursor-pointer"
              :class="theme('headerUserInfo').value"
            >
              <div 
                class="w-9 h-9 rounded-full flex items-center justify-center"
                :style="{ background: userGradient }"
              >
                <span class="text-white font-semibold text-sm">{{ userInitials }}</span> 
              </div>
              <div class="hidden md:block">
                <p class="text-sm font-medium" :class="theme('cardTitle').value">{{ userName }}</p>
                <p class="text-xs" :class="theme('headerUserRole').value">{{ userRole }}</p>
              </div>
            </button>

            <DesplegableUsuario 
              v-if="isDropdownOpen" 
              @logout="handleLogout"
              @close="isDropdownOpen = false"
            />
          </div>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useTheme } from '@/composables/useTheme.js' 
import { useAuth } from '@/composables/useAuth'
import DesplegableUsuario from '@/layout/DesplegableUsuario.vue' 

// === THEME ===
const { isDark, toggleTheme, theme } = useTheme()

// === AUTH ===
const { usuario, persona, fetchUsuarioActual } = useAuth()

// === ROUTER ===
const route = useRoute()
const router = useRouter()
defineEmits(['toggle-sidebar'])

// === DROPDOWN ===
const isDropdownOpen = ref(false)
const dropdownRef = ref(null)

const toggleDropdown = () => {
  isDropdownOpen.value = !isDropdownOpen.value
}

const handleClickOutside = (event) => {
  if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
    isDropdownOpen.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
  fetchUsuarioActual()
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})

const handleLogout = () => {
  isDropdownOpen.value = false
}

// === USER INFO ===
const userName = computed(() => {
  return persona.value?.nombre_completo || 'Usuario'
})

const userRole = computed(() => {
  const roles = {
    'admin': 'Administrador',
    'supervisor': 'Supervisor',
    'docente': 'Docente',
    'secretaria': 'Secretaria'
  }
  return roles[usuario.value?.rol] || 'Usuario'
})

const userInitials = computed(() => {
  if (!persona.value?.nombre_completo) return 'U'
  const names = persona.value.nombre_completo.split(' ')
  if (names.length >= 2) {
    return (names[0][0] + names[1][0]).toUpperCase()
  }
  return names[0][0].toUpperCase()
})

const userGradient = computed(() => {
  const gradients = {
    'admin': 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
    'supervisor': 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
    'docente': 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
    'secretaria': 'linear-gradient(135deg, #ff9a9e 0%, #fecfef 99%, #fecfef 100%)'
  }
  return gradients[usuario.value?.rol] || 'linear-gradient(135deg, #a8edea 0%, #fed6e3 100%)'
})

// === BREADCRUMB TITLE ===
const titulo = computed(() => {
  const titles = {
    'Home': 'Dashboard',
    'Horarios': 'Horarios',
    'Asistencias': 'Asistencias',
    'Usuarios': 'Usuarios',
    'Empleados': 'Empleados',
    'Areas': 'Áreas',
    'Alumnos': 'Alumnos',
    'MiPerfil': 'Mi Perfil',
    'configuracion': 'Configuración',
    'Personal': 'Personal'
  }
  return titles[route.name] || 'Página' 
})
</script>