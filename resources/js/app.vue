<!-- resources/js/app.vue -->
<template>
  <div class="min-h-screen" style="background:
    radial-gradient(circle at 35% 30%, #59144E 0%, rgba(89, 20, 78, 0.4) 20%, transparent 50%),
    radial-gradient(circle at 60% 55%, #0A0140 0%, rgba(10, 1, 64, 0.5) 20%, transparent 35%),
    radial-gradient(circle at 70% 70%, #060273 0%, rgba(6, 2, 115, 0.4) 15%, transparent 30%),
    radial-gradient(circle at 75% 85%, #050259 0%, rgba(5, 2, 89, 0.3) 10%, transparent 25%),
    linear-gradient(to bottom right, #190426 0%, #000000 100%)">

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
import Footer from './components/Footer.vue'

const route = useRoute()

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

<style>
/* Scrollbar personalizado */
::-webkit-scrollbar {
  width: 8px;
}

::-webkit-scrollbar-track {
  background: rgba(255, 255, 255, 0.05);
}

::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.2);
  border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
  background: rgba(255, 255, 255, 0.3);
}
</style>
