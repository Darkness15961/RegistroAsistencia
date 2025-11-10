<!-- resources/js/modules/Alumnos/components/TablaAlumnos.vue -->
<template>
  <div>
    <h3 class="font-bold mb-2">Alumnos</h3>
    <table class="w-full table-auto border-collapse border border-gray-200">
      <thead>
        <tr class="bg-gray-100">
          <th class="border p-2">ID</th>
          <th class="border p-2">Nombre</th>
          <th class="border p-2">DNI</th>
          <th class="border p-2">Grado</th>
          <th class="border p-2">Área</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="alumno in alumnos" :key="alumno.id_persona" class="hover:bg-gray-50">
          <td class="border p-2">{{ alumno.id_persona }}</td>
          <td class="border p-2">{{ alumno.nombre_completo }}</td>
          <td class="border p-2">{{ alumno.dni }}</td>
          <td class="border p-2">{{ alumno.grupo?.grado }} {{ alumno.grupo?.seccion }}</td>
          <td class="border p-2">{{ alumno.area?.nombre_area }}</td>
        </tr>
        <tr v-if="alumnos.length === 0">
          <td class="border p-2 text-center" colspan="5">No hay alumnos.</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
defineProps({
  alumnos: {
    type: Array,
    default: () => [],
  },
})
</script>
