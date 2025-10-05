<!-- resources/js/components/Header.vue -->
<template>
  <header class="sticky top-0 z-30 text-white">
    <div class="px-6 py-4">
      <div class="flex items-center justify-between">
        
        <!-- Lado izquierdo: Hamburguesa + Breadcrumb -->
        <div class="flex items-center gap-4">
          <!-- Botón Hamburguesa -->
          <button 
            @click="$emit('toggle-sidebar')"
            class="w-10 h-10 flex items-center justify-center rounded-xl hover:bg-white/10 transition-all duration-200"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
          </button>
          
          <!-- Breadcrumb / Título -->
          <div class="flex items-center gap-2 text-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span class="text-gray-400">Home</span>
            <span class="text-gray-600">></span>
            <span class="font-medium">{{ titulo }}</span>
          </div>
        </div>
        
        <!-- Lado derecho: Solo Usuario -->
        <div class="flex items-center gap-4">
          <!-- Usuario -->
          <div class="flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-white/5 transition-all cursor-pointer">
            <div class="w-9 h-9 bg-gradient-to-br from-purple-400 to-pink-400 rounded-full flex items-center justify-center">
              <span class="text-white font-semibold text-sm">U</span>
            </div>
            <div class="hidden md:block">
              <p class="text-sm font-medium">Usuario</p>
              <p class="text-xs text-gray-400">Admin</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref } from 'vue'

// Emite evento al padre para colapsar sidebar
defineEmits(['toggle-sidebar'])

const titulo = ref('Dashboard')
</script>