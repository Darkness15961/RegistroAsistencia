<template>
  <div class="overflow-x-auto">
    <table class="min-w-full bg-white border border-gray-300 rounded-lg shadow-md">
      <thead class="bg-gray-100">
        <tr>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
            ID
          </th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
            Nombre
          </th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
            Cargo
          </th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
            Salario
          </th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
            Estado
          </th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200">
        <tr 
          v-for="empleado in empleados" 
          :key="empleado.id"
          class="hover:bg-gray-50 transition-colors"
        >
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
            {{ empleado.id }}
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
            {{ empleado.nombre }}
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
            {{ empleado.cargo }}
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
            S/ {{ empleado.salario.toLocaleString() }}
          </td>
          <td class="px-6 py-4 whitespace-nowrap">
            <span 
              :class="empleado.activo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
              class="px-2 py-1 text-xs font-semibold rounded-full"
            >
              {{ empleado.activo ? 'Activo' : 'Inactivo' }}
            </span>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { ref } from 'vue'

// Datos de ejemplo (inventados)
const empleados = ref([
  {
    id: 1,
    nombre: 'Juan Pérez',
    cargo: 'Desarrollador',
    salario: 3500,
    activo: true
  },
  {
    id: 2,
    nombre: 'María García',
    cargo: 'Diseñadora',
    salario: 3200,
    activo: true
  },
  {
    id: 3,
    nombre: 'Carlos López',
    cargo: 'Gerente',
    salario: 5000,
    activo: true
  },
  {
    id: 4,
    nombre: 'Ana Martínez',
    cargo: 'Analista',
    salario: 2800,
    activo: false
  },
  {
    id: 5,
    nombre: 'Luis Rodríguez',
    cargo: 'Programador',
    salario: 3300,
    activo: true
  }
])
</script>