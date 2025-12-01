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
        
        <main class="flex-1 overflow-y-auto flex flex-col">
          <!-- Contenido de la página actual (Home, Alumnos, Personal, etc.) -->
          <div class="flex-1 p-6">
            <router-view />
          </div>
          <!-- Footer siempre al final -->
          <Footer />
        </main>
      </div>
    </div>
    
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRoute } from 'vue-router'
// --- ¡IMPORTACIONES ACTUALIZADAS! ---
import Sidebar from './layout/Sidebar.vue' // Antes: ./components/layout/Sidebar.vue [cite: js/components/layout/Sidebar.vue]
import Header from './layout/Header.vue'   // Antes: ./components/layout/Header.vue [cite: js/components/layout/Header.vue]
import Footer from './layout/Footer.vue'   // Antes: ./components/Footer.vue [cite: js/components/Footer.vue]
import { useTheme } from './composables/useTheme' // Antes: ./composables/useTheme [cite: js/composables/useTheme.js]

// --- TU SCRIPT SETUP SE MANTIENE 100% IDÉNTICO [cite: js/app.vue] ---
const route = useRoute()
const { isDark } = useTheme()
const sidebarCollapsed = ref(false)

const isFullScreenPage = computed(() => {
  // Esta lógica funciona perfecto con tu nuevo router [cite: js/routes/index.js]
  return route.name === 'Login' || 
           route.name === 'registro' || // (Tu ruta 'registro' original) [cite: js/routes/index.js]
           route.name === 'NotFound';
})

const toggleSidebar = () => {
  sidebarCollapsed.value = !sidebarCollapsed.value
}

const handleResize = () => {
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