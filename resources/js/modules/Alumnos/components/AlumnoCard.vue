<template>
  <div class="grid gap-4 sm:hidden"> <!-- Solo se muestra en móviles -->
    <div
      v-for="alumno in alumnos"
      :key="alumno.id_persona"
      class="bg-white p-4 rounded-xl shadow-md border border-gray-100 flex flex-col gap-2"
    >
      <div class="flex items-center gap-3">
        <img
          :src="alumno.foto_url || '/images/avatar-default.png'"
          alt="Foto alumno"
          class="w-14 h-14 rounded-full object-cover border"
        />
        <div>
          <p class="font-semibold text-gray-800">
            {{ alumno.nombres }} {{ alumno.apellidos }}
          </p>
          <p class="text-sm text-gray-500">DNI: {{ alumno.dni }}</p>
          <p class="text-sm text-gray-500">{{ alumno.email || 'Sin correo' }}</p>
        </div>
      </div>

      <div class="flex justify-end gap-2 mt-2">
        <button
          @click="$emit('editar', alumno)"
          class="bg-yellow-400 text-white px-3 py-1 rounded hover:bg-yellow-500 transition"
        >
          Editar
        </button>
        <button
          @click="$emit('eliminar', alumno.id_persona)"
          class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 transition"
        >
          Eliminar
        </button>
      </div>
    </div>

    <div v-if="!alumnos.length" class="text-center text-gray-500 py-8">
      No hay alumnos registrados
    </div>
  </div>
</template>

<script setup>
defineProps({
  alumnos: { type: Array, required: true },
})
defineEmits(['editar', 'eliminar'])
</script>
