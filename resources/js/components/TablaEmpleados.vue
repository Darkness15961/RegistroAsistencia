<template>
  <div>
    <!-- HEADER -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
      <div>
        <h2 class="text-2xl font-bold flex items-center text-gray-800 mb-2">
          <i :class="`fas fa-${iconoArea} mr-2 text-blue-500`"></i>
          {{ nombreArea }}
        </h2>
        <p class="text-sm text-gray-400">{{ empleadosFiltrados.length }} personas registradas</p>
      </div>

      <button
        @click="$emit('nuevoEmpleado')"
        class="flex items-center gap-2 px-6 py-3 rounded-xl font-semibold shadow-lg hover:scale-105 transition-all duration-200 whitespace-nowrap
              bg-gradient-to-r from-blue-500 to-purple-500 text-white"
        style="box-shadow: 0 4px 24px 0 rgba(96, 72, 255, 0.08);"
      >
        <i class="fas fa-plus"></i>
        Nuevo Empleado
      </button>
    </div>
    <!-- BUSCADOR -->
<!-- BUSCADOR ESTILO Áreas -->
      <div class="relative w-full sm:max-w-md mb-6">
        <input
          v-model="busqueda"
          type="text"
          placeholder="Buscar empleado..."
          class="w-full bg-white border border-gray-200 rounded-xl pl-10 pr-4 py-3 text-gray-800 placeholder-gray-400 shadow focus:outline-none focus:border-blue-400 transition"
        />
        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
      </div>
    <!-- Desktop Table -->
    <div class="hidden md:block bg-white border border-gray-200 rounded-3xl shadow-xl overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="bg-gray-50 border-b border-gray-200">
              <th class="px-6 py-4 text-sm font-bold text-gray-600 uppercase">ID</th>
              <th class="px-6 py-4 text-sm font-bold text-gray-600 uppercase">Nombre</th>
              <th class="px-6 py-4 text-sm font-bold text-gray-600 uppercase">Cargo</th>
              <th class="px-6 py-4 text-sm font-bold text-gray-600 uppercase">Correo</th>
              <th class="px-6 py-4 text-sm font-bold text-gray-600 uppercase">Teléfono</th>
              <th class="px-6 py-4 text-sm font-bold text-gray-600 uppercase">Estado</th>
              <th class="px-6 py-4 text-center text-sm font-bold text-gray-600 uppercase">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(emp, idx) in empleadosFiltrados"
              :key="emp.id"
              :class="idx % 2 === 0 ? 'bg-white' : 'bg-gray-50'"
            >
              <td class="px-6 py-4 text-gray-800 font-mono">{{ emp.id }}</td>
              <td class="px-6 py-4 text-gray-800 font-semibold">{{ emp.nombre }}</td>
              <td class="px-6 py-4">
                <span class="inline-block bg-blue-100 text-blue-700 rounded-lg px-3 py-1 border border-blue-200 text-sm font-medium">
                  {{ emp.cargo }}
                </span>
              </td>
              <td class="px-6 py-4 text-gray-500">{{ emp.correo }}</td>
              <td class="px-6 py-4 text-gray-500">{{ emp.telefono }}</td>
              <td class="px-6 py-4">
                <span
                  :class="emp.estado === 'Activo'
                    ? 'bg-green-100 text-green-700'
                    : 'bg-red-100 text-red-700'
                  "
                  class="rounded-full px-3 py-1 text-xs font-medium border"
                >{{ emp.estado }}</span>
              </td>
              <td class="px-6 py-4 flex justify-center gap-2">
                <button
                  class="p-2 rounded-xl transition-all hover:scale-110 group
                         bg-blue-50 hover:bg-blue-100 border border-blue-200"
                  title="Editar"
                  @click="$emit('editar', emp)"
                >
                  <i class="fas fa-edit text-blue-600 group-hover:text-blue-700"></i>
                </button>
                <button
                  class="p-2 rounded-xl transition-all hover:scale-110 group
                         bg-red-50 hover:bg-red-100 border border-red-200"
                  title="Eliminar"
                  @click="$emit('eliminar', emp.id)"
                >
                  <i class="fas fa-trash text-red-600 group-hover:text-red-700"></i>
                </button>
              </td>
            </tr>
            <tr v-if="empleadosFiltrados.length === 0">
              <td colspan="7" class="text-center py-10 text-gray-400">
                <i class="fas fa-search text-4xl mb-3 opacity-60"></i>
                <p>No se encontraron empleados</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Mobile Cards -->
    <div class="grid gap-4 md:hidden">
      <div
        v-for="emp in empleadosFiltrados"
        :key="emp.id"
        class="bg-white rounded-2xl shadow-xl p-6"
      >
        <div class="flex items-center gap-3 mb-2">
          <div class="bg-blue-400 text-white rounded-xl w-12 h-12 flex items-center justify-center text-xl font-bold">
            <i :class="`fas fa-${iconoArea}`"></i>
          </div>
          <div>
            <h3 class="font-bold text-lg text-gray-800 mb-1">{{ emp.nombre }}</h3>
            <div class="text-xs text-gray-400 mb-1">ID: {{ emp.id }}</div>
          </div>
          <div class="flex gap-2 ml-auto">
            <button class="p-2 rounded-xl bg-blue-50 border border-blue-200" title="Editar">
              <i class="fas fa-edit text-blue-600"></i>
            </button>
            <button class="p-2 rounded-xl bg-red-50 border border-red-200" title="Eliminar">
              <i class="fas fa-trash text-red-600"></i>
            </button>
          </div>
        </div>
        <div class="mb-1 font-semibold text-blue-700">{{ emp.cargo }}</div>
        <div class="text-sm text-gray-500 mb-1">Correo: {{ emp.correo }}</div>
        <div class="text-sm text-gray-500 mb-1">Teléfono: {{ emp.telefono }}</div>
        <div class="flex items-center gap-1 text-sm">
          Estado:
          <span :class="emp.estado === 'Activo'
             ? 'bg-green-100 text-green-700 px-2 py-1 rounded-full border'
             : 'bg-red-100 text-red-700 px-2 py-1 rounded-full border'">{{ emp.estado }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
const props = defineProps({
  empleados: { type: Array, default: () => [] },
  nombreArea: { type: String, required: true },
  iconoArea: { type: String, default: 'users' }
})
const busqueda = ref('')
const empleadosFiltrados = computed(() =>
  busqueda.value
    ? props.empleados.filter(e => (
        e.nombre.toLowerCase().includes(busqueda.value.toLowerCase()) ||
        e.cargo.toLowerCase().includes(busqueda.value.toLowerCase()) ||
        e.correo.toLowerCase().includes(busqueda.value.toLowerCase())
      ))
    : props.empleados
)
defineEmits(['nuevoEmpleado', 'editar', 'eliminar', 'volver'])
</script>