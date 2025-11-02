<template>
  <div 
    class="min-h-screen transition-colors duration-300"
    :class="isDark ? 'bg-dark-gradient' : 'bg-light-gradient'"
  >
    
    <div v-if="isFullScreenPage" class="min-h-screen">
      <router-view />
    </div>
    
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
          <div class="p-6">
            <router-view />
          </div>
        </main>
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
const { isDark } = useTheme()
const sidebarCollapsed = ref(false)

// --- CORRECCIÓN 2 ---
// Renombramos 'isLoginPage' a 'isFullScreenPage' (es más preciso)
// y añadimos 'NotFound' a la lista, usando 'route.name' (más robusto)
const isFullScreenPage = computed(() => {
  return route.name === 'login' || 
         route.name === 'registro' || 
         route.name === 'NotFound';
})
// --- FIN DE LA CORRECCIÓN ---

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