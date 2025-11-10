<!-- resources/js/modules/dashboard/pages/Home.vue -->
<script setup>
import { ref, onMounted } from 'vue'
import api from '@/axiosConfig'

// Componentes
import Card from '../components/Card.vue'
import TablaAsistencia from '@/modules/asistencias/components/TablaAsistencia.vue'
import TablaEmpleados from '@/modules/empleados/components/TablaEmpleados.vue'
import TablaAlumnos from '@/modules/Alumnos/components/TablaAlumnos.vue'
import UserCard from '@/modules/usuarios/components/UserCard.vue'

const loading = ref(true)
const error = ref(null)

// Datos para dashboard
const stats = ref({})
const asistencias = ref([])
const empleados = ref([])
const alumnos = ref([])
const usuarios = ref([])

const fetchDashboardData = async () => {
  loading.value = true
  error.value = null
  try {
    // Estadísticas
    const statsRes = await api.get('/dashboard-stats')
    stats.value = statsRes.data.stats || {}

    // Últimas asistencias
    const asistenciasRes = await api.get('/asistencias')
    asistencias.value = Array.isArray(asistenciasRes.data) ? asistenciasRes.data : []

    // Empleados
    const empleadosRes = await api.get('/personas')
    empleados.value = empleadosRes.data.filter(p => p.tipo_persona === 'empleado')

    // Alumnos
    alumnos.value = empleadosRes.data.filter(p => p.tipo_persona === 'alumno')

    // Usuarios
    const usuariosRes = await api.get('/usuarios')
    usuarios.value = Array.isArray(usuariosRes.data) ? usuariosRes.data : []

  } catch (err) {
    console.error("Error cargando dashboard:", err)
    error.value = "No se pudieron cargar los datos. Intente más tarde."
  } finally {
    loading.value = false
  }
}

onMounted(fetchDashboardData)
</script>

<template>
  <div class="p-6">
    <div v-if="loading" class="text-center py-20">
      <i class="fas fa-spinner fa-spin text-4xl"></i>
      <p class="mt-4">Cargando dashboard...</p>
    </div>

    <div v-else-if="error" class="text-center text-red-500">
      <p>{{ error }}</p>
    </div>

    <div v-else>
      <!-- Estadísticas -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <Card title="Empleados" :value="stats.empleados" icon="users" gradient-class="bg-gradient-to-br from-blue-400 to-blue-600"/>
        <Card title="Asistencias Hoy" :value="stats.asistenciasHoy" icon="calendar-check" gradient-class="bg-gradient-to-br from-green-400 to-emerald-600"/>
        <Card title="Horarios Activos" :value="stats.horariosActivos" icon="clock" gradient-class="bg-gradient-to-br from-purple-400 to-pink-600"/>
        <Card title="Usuarios" :value="stats.usuarios" icon="user-shield" gradient-class="bg-gradient-to-br from-orange-400 to-red-600"/>
      </div>

      <!-- Tablas -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <TablaAsistencia :asistencias="asistencias"/>
        <TablaEmpleados :empleados="empleados"/>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
        <TablaAlumnos :alumnos="alumnos"/>
        <UserCard :usuarios="usuarios"/>
      </div>
    </div>
  </div>
</template>
