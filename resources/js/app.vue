<template>
  <div 
    class="min-h-screen transition-colors duration-300"
    :class="isDark ? 'bg-dark-gradient' : 'bg-light-gradient'"
  >
    
    <!-- 1. Páginas de Pantalla Completa (Login, Registro, 404) -->
    <div v-if="isFullScreenPage" class="min-h-screen">
      <router-view />
    </div>
    
    <!-- 2. Layout Principal (Sidebar + Header + Contenido) -->
    <div v-else class="flex h-screen overflow-hidden">
      <div 
        v-if="!sidebarCollapsed" 
        @click="toggleSidebar"
        class="fixed inset-0 bg-black/50 z-30 md:hidden"
      ></div>
      
      <Sidebar :is-collapsed="sidebarCollapsed" />
      
      <div 
        class="flex flex-col flex-1 transition-all duration-300"
        :class="sidebarCollapsed ? 'ml-0 md:ml-[92px]' : 'ml-0 md:ml-[268px]'"
      >
        <Header @toggle-sidebar="toggleSidebar" />
        
        <main class="flex-1 overflow-y-auto">
          <!-- Contenido de la página actual (Home, Alumnos, Empleados, etc.) -->
          <div class="p-6">
            <router-view />
          </div>
          <!-- Asumiendo que Footer se importa y usa aquí -->
          <Footer />
        </main>
      </div>
    </div>
    
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRoute } from 'vue-router'
// --- IMPORTACIONES CORREGIDAS A LAYOUT/ ---
import Sidebar from './components/layout/Sidebar.vue'
import Header from './components/layout/Header.vue'
import Footer from './components/Footer.vue' // Asumiendo que Footer está en la raíz de components
import { useTheme } from './composables/useTheme'

const route = useRoute()
const { isDark } = useTheme()
const sidebarCollapsed = ref(false)

const isFullScreenPage = computed(() => {
  return route.name === 'login' || 
           route.name === 'registro' || 
           route.name === 'NotFound';
})

const toggleSidebar = () => {
  sidebarCollapsed.value = !sidebarCollapsed.value
}

const handleResize = () => {
  // Colapsar la barra lateral automáticamente en pantallas pequeñas
  if (window.innerWidth < 768) { 
    sidebarCollapsed.value = true
  } else {
    sidebarCollapsed.value = false
  }
}

onMounted(() => {
  handleResize()
  window.addEventListener('resize', handleResize)
})

onUnmounted(() => {
  window.removeEventListener('resize', handleResize)
})
</script>