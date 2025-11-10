<template>
  <div class="tabla-asistencia p-4 border rounded-lg shadow-md">
    <h3 class="font-bold mb-2">Asistencias</h3>
    <table class="w-full table-auto">
      <thead>
        <tr>
          <th>Nombre</th>
          <th>Fecha</th>
          <th>Hora Entrada</th>
          <th>Hora Salida</th>
          <th>Estado</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="item in asistencias" :key="item.id_asistencia">
          <td>{{ item.persona.nombre_completo }}</td>
          <td>{{ item.fecha }}</td>
          <td>{{ item.hora_entrada }}</td>
          <td>{{ item.hora_salida }}</td>
          <td>{{ item.estado_asistencia }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import api from '@/axiosConfig'

const asistencias = ref([])

const fetchAsistencias = async () => {
  try {
    const response = await api.get('/asistencias')
    asistencias.value = response.data
  } catch (error) {
    console.error('Error al cargar asistencias:', error)
  }
}

fetchAsistencias()
</script>

<style scoped>
.tabla-asistencia table {
  border-collapse: collapse;
}
.tabla-asistencia th,
.tabla-asistencia td {
  border: 1px solid #ccc;
  padding: 0.5rem;
  text-align: left;
}
</style>
