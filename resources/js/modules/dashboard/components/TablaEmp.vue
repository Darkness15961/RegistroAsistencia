<!-- resources/js/modules/empleados/components/TablaEmpleados.vue -->
<template>
  <div>
    <h3 class="font-bold mb-2">Empleados</h3>
    <table class="w-full table-auto border-collapse border border-gray-200">
      <thead>
        <tr class="bg-gray-100">
          <th class="border p-2">ID</th>
          <th class="border p-2">Nombre</th>
          <th class="border p-2">DNI</th>
          <th class="border p-2">Área</th>
          <th class="border p-2">Cargo</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="empleado in empleados" :key="empleado.id_persona" class="hover:bg-gray-50">
          <td class="border p-2">{{ empleado.id_persona }}</td>
          <td class="border p-2">{{ empleado.nombre_completo }}</td>
          <td class="border p-2">{{ empleado.dni }}</td>
          <td class="border p-2">{{ empleado.area?.nombre_area }}</td>
          <td class="border p-2">{{ empleado.cargo_grado }}</td>
        </tr>
        <tr v-if="empleados.length === 0">
          <td class="border p-2 text-center" colspan="5">No hay empleados.</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
defineProps({
  empleados: {
    type: Array,
    default: () => [],
  },
})
</script>
