<template>
  <aside
    class="bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 flex flex-col h-full shadow-lg transition-all duration-300"
    :class="isCollapsed ? 'w-[92px]' : 'w-[268px]'"
  >
    <div class="flex items-center justify-center py-4 border-b border-gray-200 dark:border-gray-700">
      <img
        v-if="!isCollapsed"
        src="@/images/logo.png"
        alt="Logo completo"
        class="w-40 object-contain"
      />
      <img
        v-else
        src="@/images/logo.svg"
        alt="Logo compacto"
        class="w-10 object-contain"
      />
    </div>

    <!-- Aquí vendrán tus enlaces de navegación -->
    <nav class="flex-1 overflow-y-auto mt-6">
      <!-- ejemplo -->
      <RouterLink
        v-for="item in menu"
        :key="item.path"
        :to="item.path"
        class="flex items-center px-6 py-3 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors duration-200"
      >
        <i :class="['fa', item.icon, 'mr-3']"></i>
        <span v-if="!isCollapsed">{{ item.label }}</span>
      </RouterLink>
    </nav>
  </aside>
</template>

<script setup>
import { ref } from 'vue'
import { RouterLink } from 'vue-router'

defineProps({
  isCollapsed: {
    type: Boolean,
    default: false
  }
})

const menu = ref([
  { path: '/', label: 'Inicio', icon: 'fa-home' },
  { path: '/asistencias', label: 'Asistencias', icon: 'fa-calendar-check' },
  { path: '/empleados', label: 'Empleados', icon: 'fa-users' },
  { path: '/alumnos', label: 'Alumnos', icon: 'fa-user-graduate' },
  { path: '/areas', label: 'Áreas', icon: 'fa-building' },
  { path: '/usuarios', label: 'Usuarios', icon: 'fa-user-cog' },
  { path: '/perfil', label: 'Mi Perfil', icon: 'fa-id-card' }
])
</script>
