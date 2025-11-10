<template>
  <aside
    :class="[
      'fixed top-0 h-screen flex flex-col overflow-hidden z-40 transition-all duration-300',
      theme('sidebar').value,
      'md:left-3 md:top-3 md:rounded-3xl md:h-[calc(100vh-24px)]',
      isCollapsed ? 'md:w-20' : 'md:w-64',
      'left-0 w-64',
      isCollapsed ? 'max-md:-translate-x-full' : 'max-md:translate-x-0'
    ]"
  >
    <div
      class="flex items-center justify-center p-6 transition-colors h-auto"
      :class="theme('sidebarBorder').value"
    >
      <div class="flex items-center gap-3">
        <div class="w-14 h-14 bg-white rounded-full flex items-center justify-center flex-shrink-0 p-1 shadow-md">
          <img
            src="@/images/logo1.png"
            alt="Logo 4scan"
            class="object-contain w-full h-full"
          />
        </div>
        
        <span 
          v-show="!isCollapsed" 
          class="text-3xl font-bold transition-opacity"
          :class="theme('cardTitle').value"
        >
          4Scan
        </span>
      </div>
    </div>

    <hr :class="theme('sidebarBorder').value" class="mx-4 my-2">

    <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
      <router-link
        v-for="item in menu"
        :key="item.path"
        :to="item.path"
        class="flex items-center gap-3 px-3 py-3 rounded-2xl transition-all duration-200 group relative"
        :class="isActive(item.path) ? theme('sidebarItemActive').value : theme('sidebarItem').value"
      >
        <div class="w-10 h-10 flex items-center justify-center flex-shrink-0">
          <i :class="['fa', item.icon, 'text-xl w-6 h-6 text-center']"></i>
        </div>
        
        <span v-show="!isCollapsed" class="font-medium">{{ item.label }}</span>
        
        <div 
          v-show="isCollapsed" 
          class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-sm rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-50"
        >
          {{ item.label }}
        </div>
      </router-link>
    </nav>
  </aside>
</template>

<script setup>
import { ref } from 'vue'
import { useRoute } from 'vue-router'
import { useTheme } from '@/composables/useTheme.js'

defineProps({
  isCollapsed: {
    type: Boolean,
    default: false
  }
})

const { theme } = useTheme()
const route = useRoute()

const isActive = (path) => route.path === path

const menu = ref([
  { path: '/home', label: 'Inicio', icon: 'fa-home' },
  { path: '/usuarios', label: 'Usuarios', icon: 'fa-user-cog' },
  { path: '/areas', label: 'Áreas', icon: 'fa-building' },
  { path: '/horarios', label: 'Horarios', icon: 'fa-clock' },
  { path: '/empleados', label: 'Empleados', icon: 'fa-users' },
  { path: '/alumnos', label: 'Alumnos', icon: 'fa-user-graduate' },
  { path: '/asistencias', label: 'Asistencias', icon: 'fa-calendar-check' },




])
</script>