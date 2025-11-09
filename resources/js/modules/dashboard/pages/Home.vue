<script setup>
import { ref, onMounted } from 'vue'


import { useTheme } from '@/composables/useTheme'
import api from '@/axiosConfig'
import Card from '../components/Card.vue'
import AttendanceChart from '../components/TablaAsis.vue'
import EmployeesChart from '../components/TablaEmp.vue'

// --- FIN RUTAS ADAPTADAS ---

const { theme } = useTheme()

// --- Estados para manejar los datos de la API ---
const stats = ref(null) // Para las 4 tarjetas
const attendanceData = ref(null) // Para el gráfico de asistencias
const employeesData = ref(null) // Para el gráfico de empleados
const loading = ref(true) // Estado de carga
const error = ref(null) // Manejo de errores

// --- Función para cargar los datos ---
const fetchDashboardData = async () => {
  loading.value = true
  error.value = null
  try {
    // Esta llamada ahora usa la instancia 'api' importada de axiosConfig.js
    const response = await api.get('/dashboard-stats')
    
    // Asignamos los datos a nuestros 'refs'
    stats.value = response.data.stats 
    attendanceData.value = response.data.attendanceChart
    employeesData.value = response.data.employeesChart

  } catch (err) {
    console.error("Error al cargar datos del dashboard:", err)
    error.value = "No se pudieron cargar las estadísticas. Intente más tarde."
  } finally {
    loading.value = false
  }
}

// --- Cargar los datos cuando el componente se monta ---
onMounted(() => {
  fetchDashboardData()
})
</script>

<template>
  <div class="flex-1 p-4 sm:p-6 overflow-x-hidden">
    
    <div v-if="loading" class="text-center py-20" :class="theme('cardSubtitle').value">
      <i class="fas fa-spinner fa-spin text-4xl"></i>
      <p class="mt-4">Cargando estadísticas...</p>
    </div>

    <div v-else-if="error" class="bg-red-500/10 border border-red-500/20 text-red-300 p-6 rounded-2xl text-center">
      <i class="fas fa-exclamation-triangle text-4xl mb-4"></i>
      <h3 class="font-bold text-lg">Error</h3>
      <p>{{ error }}</p>
    </div>

    <div v-else-if="stats">
      <section class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-10">
        <Card 
          title="EmpleADOS" 
          :value="stats.empleados" 
          icon="users"
          gradient-class="bg-gradient-to-br from-blue-400 to-blue-600"
        />
        <Card 
          title="Asistencias Hoy" 
          :value="stats.asistenciasHoy" 
          icon="calendar-check"
          gradient-class="bg-gradient-to-br from-green-400 to-emerald-600"
        />
        <Card 
          title="Horarios Activos" 
          :value="stats.horariosActivos" 
          icon="clock"
          gradient-class="bg-gradient-to-br from-purple-400 to-pink-600"
        />
        <Card 
          title="Usuarios del Sistema" 
          :value="stats.usuarios" 
          icon="user-shield"
          gradient-class="bg-gradient-to-br from-orange-400 to-red-600"
        />
      </section>

      <section class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
        <div
          class="backdrop-blur-xl rounded-3xl shadow-md p-4 sm:p-6 transition-colors"
          :class="theme('card').value"
        >
          <AttendanceChart v-if="attendanceData" :chart-data="attendanceData" />
        </div>
        <div
          class="backdrop-blur-xl rounded-3xl shadow-md p-4 sm:p-6 transition-colors"
          :class="theme('card').value"
        >
          <EmployeesChart v-if="employeesData" :chart-data="employeesData" />
        </div>
      </section>
    </div>

  </div>
</template>