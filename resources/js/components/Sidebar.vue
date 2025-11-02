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
      class="flex items-center justify-between p-6 transition-colors"
      :class="theme('sidebarBorder').value"
    >
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-cyan-400 rounded-full flex items-center justify-center flex-shrink-0">
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
          </svg>
        </div>
        <span
          v-show="!isCollapsed"
          class="text-2xl font-bold transition-opacity"
          :class="theme('cardTitle').value"
        >
          4scan
        </span>
      </div>
    </div>

    <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
      <router-link
        to="/home"
        class="flex items-center gap-3 px-3 py-3 rounded-2xl transition-all duration-200 group relative"
        :class="isActive('/home') ? theme('sidebarItemActive').value : theme('sidebarItem').value"
      >
        <div class="w-10 h-10 flex items-center justify-center flex-shrink-0">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
          </svg>
        </div>
        <span v-show="!isCollapsed" class="font-medium">Home</span>

        <div v-show="isCollapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-sm rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap">
          Home
        </div>
      </router-link>

      <router-link
        to="/usuarios"
        class="flex items-center gap-3 px-3 py-3 rounded-2xl transition-all duration-200 group relative"
        :class="isActive('/usuarios') ? theme('sidebarItemActive').value : theme('sidebarItem').value"
      >
        <div class="w-10 h-10 flex items-center justify-center flex-shrink-0">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
          </svg>
        </div>
        <span v-show="!isCollapsed" class="font-medium">Usuarios</span>

        <div v-show="isCollapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-sm rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap">
          Usuarios
        </div>
      </router-link>

      <router-link
        to="/areas"
        class="flex items-center gap-3 px-3 py-3 rounded-2xl transition-all duration-200 group relative"
        :class="isActive('/areas') ? theme('sidebarItemActive').value : theme('sidebarItem').value"
      >
        <div class="w-10 h-10 flex items-center justify-center flex-shrink-0">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
          </svg>
        </div>
        <span v-show="!isCollapsed" class="font-medium">Areas</span>

        <div v-show="isCollapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-sm rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap">
          Areas
        </div>
      </router-link>

      <router-link
        to="/horarios"
        class="flex items-center gap-3 px-3 py-3 rounded-2xl transition-all duration-200 group relative"
        :class="isActive('/horarios') ? theme('sidebarItemActive').value : theme('sidebarItem').value"
      >
        <div class="w-10 h-10 flex items-center justify-center flex-shrink-0">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        <span v-show="!isCollapsed" class="font-medium">Horarios</span>

        <div v-show="isCollapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-sm rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap">
          Horarios
        </div>
      </router-link>

      <router-link
        to="/empleados"
        class="flex items-center gap-3 px-3 py-3 rounded-2xl transition-all duration-200 group relative"
        :class="isActive('/empleados') ? theme('sidebarItemActive').value : theme('sidebarItem').value"
      >
        <div class="w-10 h-10 flex items-center justify-center flex-shrink-0">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
          </svg>
        </div>
        <span v-show="!isCollapsed" class="font-medium">Empleados</span>

        <div v-show="isCollapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-sm rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap">
          Empleados
        </div>
      </router-link>

      <router-link
        to="/asistencias"
        class="flex items-center gap-3 px-3 py-3 rounded-2xl transition-all duration-200 group relative"
        :class="isActive('/asistencias') ? theme('sidebarItemActive').value : theme('sidebarItem').value"
      >
        <div class="w-10 h-10 flex items-center justify-center flex-shrink-0">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
          </svg>
        </div>
        <span v-show="!isCollapsed" class="font-medium">Asistencias</span>

        <div v-show="isCollapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-sm rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap">
          Asistencias
        </div>
      </router-link>
    </nav>
  </aside>
</template>

<script setup>
import { useRoute } from 'vue-router'
import { useTheme } from '../composables/useTheme'

defineProps({
  isCollapsed: {
    type: Boolean,
    default: false
  }
})

const { theme } = useTheme()
const route = useRoute()

const isActive = (path) => route.path === path
</script>
