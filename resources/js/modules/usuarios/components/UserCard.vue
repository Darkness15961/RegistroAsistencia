<!-- resources/js/modules/usuarios/components/UserCard.vue -->
<template>
  <div>
    <h3 class="font-bold mb-2">Usuarios del Sistema</h3>
    <table class="w-full table-auto border-collapse border border-gray-200">
      <thead>
        <tr class="bg-gray-100">
          <th class="border p-2">ID</th>
          <th class="border p-2">Email</th>
          <th class="border p-2">Rol</th>
          <th class="border p-2">Persona</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="usuario in usuarios" :key="usuario.id_usuario" class="hover:bg-gray-50">
          <td class="border p-2">{{ usuario.id_usuario }}</td>
          <td class="border p-2">{{ usuario.email }}</td>
          <td class="border p-2">{{ usuario.rol }}</td>
          <td class="border p-2">{{ usuario.persona?.nombre_completo }}</td>
        </tr>
        <tr v-if="usuarios.length === 0">
          <td class="border p-2 text-center" colspan="4">No hay usuarios.</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
defineProps({
  usuarios: {
    type: Array,
    default: () => [],
  },
})
</script>
