<template>
  <header 
    class="sticky top-0 z-30 transition-colors duration-300"
    :class="theme('header').value"
  >
    <div class="px-6 py-4">
      <div class="flex items-center justify-between">
        
        <!-- === BOTÓN SIDEBAR + TÍTULO DE PÁGINA (DINÁMICO) === -->
        <div class="flex items-center gap-4">
          <!-- Botón Sidebar -->
          <button 
            @click="$emit('toggle-sidebar')"
            class="w-10 h-10 flex items-center justify-center rounded-xl transition-all duration-200"
            :class="theme('headerButton').value"
            title="Abrir/Cerrar menú"
          >
            <i class="fas fa-bars"></i>
          </button>

          <!-- Título y Subtítulo dinámicos -->
          <div>
            <h1 class="text-xl font-bold" :class="theme('cardTitle').value">
              {{ pageInfo.title }}
            </h1>
            <p class="text-sm" :class="theme('cardSubtitle').value">
              {{ pageInfo.subtitle }}
            </p>
          </div>
        </div>

        <!-- === CONTROLES DERECHA (BOTÓN DINÁMICO + USUARIO) === -->
        <div class="flex items-center gap-4 md:mr-4">

          <!-- Botón de acción dinámico -->
          <button
            v-if="pageInfo.showButton"
            @click="pageInfo.buttonAction"
            class="flex items-center gap-2 px-4 py-2 rounded-xl font-semibold whitespace-nowrap shadow-lg hover:scale-105 transition-all duration-200 text-sm"
            :class="theme('buttonPrimary').value"
          >
            <i :class="`fas ${pageInfo.buttonIcon}`"></i>
            <span class="hidden sm:inline">{{ pageInfo.buttonText }}</span>
          </button>

          <!-- Botón de tema -->
          <button 
            @click="toggleTheme"
            class="w-10 h-10 flex items-center justify-center rounded-xl transition-all duration-200"
            :class="theme('headerButton').value"
            title="Cambiar tema"
          >
            <i v-if="!isDark" class="fas fa-sun"></i>
            <i v-else class="fas fa-moon"></i>
          </button>

          <!-- Usuario -->
          <div class="relative" ref="dropdownRef">
            <button @click="toggleDropdown" class="focus:outline-none">
              <img 
                src='@/images/avatar-default.png'
                alt="Usuario" 
                class="w-9 h-9 rounded-full border border-gray-300 dark:border-gray-600"
              />
            </button>

            <DesplegableUsuario 
              v-if="isDropdownOpen" 
              @logout="handleLogout"
            />
          </div>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { usePageHeader } from '@/composables/usePageHeader.js' 
import { useTheme } from '@/composables/useTheme.js'
import DesplegableUsuario from '@/layout/DesplegableUsuario.vue'

const { pageInfo } = usePageHeader()
const { isDark, toggleTheme, theme } = useTheme()
defineEmits(['toggle-sidebar'])

// --- Dropdown Usuario ---
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

const handleLogout = () => {
  console.log('Cerrar sesión') // Aquí puedes implementar el logout real
  isDropdownOpen.value = false
}

onMounted(() => document.addEventListener('click', handleClickOutside))
onUnmounted(() => document.removeEventListener('click', handleClickOutside))
</script>
