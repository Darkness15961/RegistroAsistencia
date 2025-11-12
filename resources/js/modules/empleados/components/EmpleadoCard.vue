<template>
  <div class="p-4 bg-white rounded-xl shadow hover:shadow-lg transition">
    <h3 class="text-lg font-semibold">{{ empleado.nombre_completo }}</h3>
    <p class="text-sm text-gray-500">{{ empleado.cargo_grado }}</p>
    <p class="text-xs mt-2"><strong>DNI:</strong> {{ empleado.dni }}</p>
    <p class="text-xs"><strong>Área:</strong> {{ empleado.area?.nombre_area || 'Sin área' }}</p>

    <div class="flex justify-end mt-3 gap-2">
      <button @click="$emit('editar')" class="text-blue-500 hover:text-blue-700">
        <i class="fas fa-edit"></i>
      </button>
      <button @click="$emit('eliminar')" class="text-red-500 hover:text-red-700">
        <i class="fas fa-trash"></i>
      </button>
    </div>
  </div>
</template>

<script setup>
defineProps({ empleado: Object })
</script>
