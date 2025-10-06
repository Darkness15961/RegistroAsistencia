<template>
  <div class="flex-1 p-6 overflow-x-hidden">
    <!-- Título -->
    <h1 class="text-3xl font-bold text-white mb-6">Lista de Áreas</h1>

    <!-- Encabezado con botón y búsqueda -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
      <!-- Botón de nuevo -->
      <button
        class="flex items-center bg-gradient-to-r from-cyan-400 to-blue-500 text-white font-semibold px-5 py-2 rounded-xl shadow-md hover:scale-105 transition-transform"
      >
        <i class="fas fa-plus mr-2"></i> Nuevo
      </button>

      <!-- Barra de búsqueda -->
      <div class="relative w-full sm:w-64">
        <input
          type="text"
          placeholder="Buscar área..."
          v-model="busqueda"
          class="w-full bg-white/10 text-white placeholder-gray-400 rounded-xl pl-10 pr-4 py-2 outline-none focus:ring-2 focus:ring-cyan-400 transition"
        />
        <i class="fas fa-search absolute left-3 top-2.5 text-gray-400"></i>
      </div>
    </div>

    <!-- Tabla principal (solo visible en pantallas medianas o mayores) -->
    <div
      class="hidden md:block bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl shadow-md overflow-hidden"
    >
      <table class="w-full text-left text-white">
        <thead class="bg-white/10 text-sm uppercase tracking-wide text-gray-300">
          <tr>
            <th class="px-6 py-3">ID</th>
            <th class="px-6 py-3">Nombre</th>
            <th class="px-6 py-3">Descripción</th>
            <th class="px-6 py-3 text-center">Acción</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="area in areasFiltradas"
            :key="area.id"
            class="border-t border-white/10 hover:bg-white/10 transition"
          >
            <td class="px-6 py-3">{{ area.id }}</td>
            <td class="px-6 py-3">{{ area.nombre }}</td>
            <td class="px-6 py-3">{{ area.descripcion }}</td>
            <td class="px-6 py-3 text-center">
              <button
                class="text-yellow-400 hover:text-yellow-300 mr-3 transition"
                title="Editar"
              >
                <i class="fas fa-edit"></i>
              </button>
              <button
                class="text-red-500 hover:text-red-400 transition"
                title="Eliminar"
              >
                <i class="fas fa-trash-alt"></i>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Versión móvil (tarjetas) -->
    <div class="grid gap-4 md:hidden">
      <div
        v-for="area in areasFiltradas"
        :key="area.id"
        class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl shadow-md p-4 text-white"
      >
        <div class="flex justify-between items-center mb-2">
          <h2 class="text-lg font-semibold">{{ area.nombre }}</h2>
          <div class="flex space-x-3">
            <button
              class="text-yellow-400 hover:text-yellow-300 transition"
              title="Editar"
            >
              <i class="fas fa-edit"></i>
            </button>
            <button
              class="text-red-500 hover:text-red-400 transition"
              title="Eliminar"
            >
              <i class="fas fa-trash-alt"></i>
            </button>
          </div>
        </div>
        <p class="text-sm text-gray-300">{{ area.descripcion }}</p>
        <span class="text-xs text-gray-400 mt-2 block">ID: {{ area.id }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const busqueda = ref('')

const areas = ref([
  { id: 1, nombre: 'Recursos Humanos', descripcion: 'Gestión del personal y nóminas' },
  { id: 2, nombre: 'Contabilidad', descripcion: 'Manejo de finanzas y presupuestos' },
  { id: 3, nombre: 'Sistemas', descripcion: 'Infraestructura tecnológica y soporte' },
  { id: 4, nombre: 'Logística', descripcion: 'Distribución y almacenamiento' },
  { id: 5, nombre: 'Marketing', descripcion: 'Estrategias y campañas publicitarias' }
])

const areasFiltradas = computed(() =>
  areas.value.filter(a =>
    a.nombre.toLowerCase().includes(busqueda.value.toLowerCase()) ||
    a.descripcion.toLowerCase().includes(busqueda.value.toLowerCase())
  )
)
</script>

<style scoped>
table {
  border-collapse: collapse;
}
</style>
