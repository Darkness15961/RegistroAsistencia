<template>
  <div
    class="min-h-screen transition-colors duration-300"
    :class="isDark ? 'bg-dark-gradient' : 'bg-light-gradient'"
  >

    <!-- Si está en login, mostrar solo la vista -->
    <div v-if="isLoginPage" class="min-h-screen">
      <router-view />
    </div>

    <!-- Si NO está en login, mostrar con layout (sidebar + header) -->
    <div v-else class="flex h-screen overflow-hidden">
      <!-- Overlay para móvil cuando sidebar está abierto -->
      <div
        v-if="!sidebarCollapsed"
        @click="toggleSidebar"
        class="fixed inset-0 bg-black/50 z-30 md:hidden"
      ></div>

      <!-- Sidebar con prop de colapso -->
      <Sidebar :is-collapsed="sidebarCollapsed" />

      <!-- Contenedor derecho - Se expande según el sidebar -->
      <div
        class="flex flex-col flex-1 transition-all duration-300"
        :class="sidebarCollapsed ? 'ml-0 md:ml-[92px]' : 'ml-0 md:ml-[268px]'"
      >
        <!-- Header con evento de toggle -->
        <Header @toggle-sidebar="toggleSidebar" />

        <!-- Contenido con scroll -->
        <main class="flex-1 overflow-y-auto">
          <div class="p-6">
            <router-view />
          </div>
        </main>

        <Footer @toggle-sidebar="toggleSidebar" />
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRoute } from 'vue-router'
import Sidebar from './components/Sidebar.vue'
import Header from './components/Header.vue'
import { useTheme } from './composables/useTheme'

const route = useRoute()

// Inicializar tema
const { isDark } = useTheme()

// Estado para colapsar/expandir sidebar
const sidebarCollapsed = ref(false)

// Detectar si estamos en la página de login
const isLoginPage = computed(() => route.path === '/login')

// Función para toggle del sidebar
const toggleSidebar = () => {
  sidebarCollapsed.value = !sidebarCollapsed.value
}

// Detectar tamaño de pantalla y auto-colapsar SOLO en móvil
const handleResize = () => {
  if (window.innerWidth < 370) {
    // En móvil: siempre colapsado (oculto) por defecto
    sidebarCollapsed.value = true
  } else {
    // En desktop: siempre expandido por defecto
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
