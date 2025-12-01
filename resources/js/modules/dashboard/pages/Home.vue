<script setup>
import { ref, onMounted, computed } from 'vue'
import api from '@/axiosConfig'
import { useTheme } from '@/composables/useTheme'
import { useAuth } from '@/composables/useAuth'

// --- Componentes del Dashboard ---
import Card from '../components/Card.vue'
import AsistenciaSemanalChart from '../components/AsistenciaSemanalChart.vue'
import EmpleadosPorMesChart from '../components/EmpleadosPorMesChart.vue'
import EstadoHoyChart from '../components/EstadoHoyChart.vue'
import AsistenciaAreaChart from '../components/AsistenciaAreaChart.vue'

const { theme } = useTheme()
const { isAdmin, isSupervisor, isDocente, fetchUsuarioActual } = useAuth()

const loading = ref(true)
const error = ref(null)

// --- Datos Reactivos para el Dashboard ---
const stats = ref({})
const asistenciaChartData = ref({ labels: [], datasets: [] })
const empleadosChartData = ref({ labels: [], datasets: [] })
const estadoHoyChartData = ref({ labels: [], datasets: [] })
const asistenciaAreaChartData = ref({ labels: [], datasets: [] })

/**
 * Carga todos los datos del dashboard desde la API en una sola llamada.
 */
const fetchDashboardData = async () => {
  loading.value = true
  error.value = null
  try {
    const response = await api.get('/dashboard-stats')
    const data = response.data

    // 1. Cargar estadísticas
    stats.value = data.stats || {}
    
    // 2. Gráfico de Asistencias Semanales
    asistenciaChartData.value = {
      labels: data.asistenciaChart.labels || [],
      datasets: [{
        label: 'Asistencias',
        backgroundColor: ['#3b82f6', '#10b981', '#8b5cf6', '#ef4444', '#f97316', '#14b8a6', '#6366f1'],
        borderRadius: 5,
        data: data.asistenciaChart.data || [],
      }],
    }

    // 3. Gráfico de Empleados/Estudiantes por Mes
    empleadosChartData.value = {
      labels: data.empleadosChart.labels || [],
      datasets: [{
        label: isDocente.value ? 'Nuevos Estudiantes' : 'Nuevos Empleados',
        borderColor: '#4ade80',
        pointBackgroundColor: '#4ade80',
        data: data.empleadosChart.data || [],
        fill: true,
      }],
    }

    // 4. Gráfico de Estado de Hoy (Dona)
    estadoHoyChartData.value = {
      labels: data.estadoHoyChart.labels || [],
      datasets: [{
        backgroundColor: ['#22c55e', '#facc15', '#ef4444'], // Verde, Amarillo, Rojo
        data: data.estadoHoyChart.data || [],
      }],
    }

    // 5. Gráfico de Asistencias por Área (Torta)
    asistenciaAreaChartData.value = {
        labels: data.asistenciaAreaChart.labels || [],
        datasets: [{
            backgroundColor: ['#6366f1', '#ec4899', '#f59e0b', '#10b981', '#3b82f6'],
            data: data.asistenciaAreaChart.data || [],
        }],
    }

  } catch (err) {
    console.error("Error cargando el dashboard:", err)
    error.value = "No se pudieron cargar los datos. Intente recargar la página."
  } finally {
    loading.value = false
  }
}

onMounted(async () => {
  await fetchUsuarioActual()
  await fetchDashboardData()
})
</script>

<template>
  <div class="p-4 sm:p-6">
    <div v-if="loading" 
         class="flex flex-col items-center justify-center h-96" 
         :class="theme('cardSubtitle').value">
      <i class="fas fa-spinner fa-spin text-5xl"></i>
      <p class="mt-4 text-xl">Cargando dashboard...</p>
    </div>

    <div v-else-if="error" 
         class="text-center bg-red-100 border border-red-300 p-6 rounded-lg dark:bg-red-900/30 dark:border-red-500/50">
      <p class="text-lg font-semibold text-red-700 dark:text-red-400">¡Oops! Algo salió mal</p>
      <p :class="theme('cardSubtitle').value">{{ error }}</p>
    </div>

    <div v-else class="space-y-6">
      <!-- Cards para Admin -->
      <div v-if="isAdmin" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
        <Card title="Empleados" :value="stats.empleados" icon="users" gradient-class="bg-gradient-to-br from-blue-500 to-blue-700"/>
        <Card title="Tasa Asistencia Hoy" :value="stats.asistenciaRateHoy + '%'" icon="chart-line" gradient-class="bg-gradient-to-br from-indigo-500 to-indigo-700"/>
        <Card title="Alumnos" :value="stats.alumnos" icon="user-graduate" gradient-class="bg-gradient-to-br from-cyan-500 to-cyan-600"/>
        <Card title="Asistencias Hoy" :value="stats.asistenciasHoy" icon="calendar-check" gradient-class="bg-gradient-to-br from-green-500 to-emerald-600"/>
        <Card title="Horarios" :value="stats.horariosActivos" icon="clock" gradient-class="bg-gradient-to-br from-purple-500 to-pink-600"/>
        <Card title="Usuarios" :value="stats.usuarios" icon="user-shield" gradient-class="bg-gradient-to-br from-orange-500 to-red-600"/>
      </div>

      <!-- Cards para Supervisor -->
      <div v-else-if="isSupervisor" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <Card title="Personal" :value="stats.empleados" icon="users" gradient-class="bg-gradient-to-br from-blue-500 to-blue-700"/>
        <Card title="Tasa Asistencia Hoy" :value="stats.asistenciaRateHoy + '%'" icon="chart-line" gradient-class="bg-gradient-to-br from-indigo-500 to-indigo-700"/>
        <Card title="Asistencias Hoy" :value="stats.asistenciasHoy" icon="calendar-check" gradient-class="bg-gradient-to-br from-green-500 to-emerald-600"/>
        <Card title="Horarios Activos" :value="stats.horariosActivos" icon="clock" gradient-class="bg-gradient-to-br from-purple-500 to-pink-600"/>
      </div>

      <!-- Cards para Docente -->
      <div v-else-if="isDocente" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <Card title="Mis Alumnos" :value="stats.alumnos" icon="user-graduate" gradient-class="bg-gradient-to-br from-cyan-500 to-cyan-600"/>
        <Card title="Asistencias Hoy" :value="stats.asistenciasHoy" icon="calendar-check" gradient-class="bg-gradient-to-br from-green-500 to-emerald-600"/>
        <Card title="Tasa Asistencia" :value="stats.asistenciaRateHoy + '%'" icon="chart-line" gradient-class="bg-gradient-to-br from-indigo-500 to-indigo-700"/>
      </div>

      <!-- Gráficos para Admin -->
      <template v-if="isAdmin">
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
          <div class="lg:col-span-3 p-4 sm:p-6 rounded-2xl border" :class="theme('card').value">
            <h3 class="text-lg font-semibold mb-4" :class="theme('cardTitle').value">
              Asistencias Semanales
            </h3>
            <div class="h-80">
              <AsistenciaSemanalChart :chart-data="asistenciaChartData" />
            </div>
          </div>
          <div class="lg:col-span-2 p-4 sm:p-6 rounded-2xl border" :class="theme('card').value">
            <h3 class="text-lg font-semibold mb-4" :class="theme('cardTitle').value">
              Empleados Registrados por Mes
            </h3>
            <div class="h-80">
              <EmpleadosPorMesChart :chart-data="empleadosChartData" />
            </div>
          </div>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <div class="p-4 sm:p-6 rounded-2xl border" :class="theme('card').value">
            <h3 class="text-lg font-semibold mb-4" :class="theme('cardTitle').value">
              Estado de Asistencia (Hoy)
            </h3>
            <div class="h-72 flex items-center justify-center">
              <EstadoHoyChart :chart-data="estadoHoyChartData" />
            </div>
          </div>
          <div class="p-4 sm:p-6 rounded-2xl border" :class="theme('card').value">
            <h3 class="text-lg font-semibold mb-4" :class="theme('cardTitle').value">
              Asistencias de Hoy por Área
            </h3>
            <div class="h-72 flex items-center justify-center">
              <AsistenciaAreaChart :chart-data="asistenciaAreaChartData" />
            </div>
          </div>
        </div>
      </template>

      <!-- Gráficos para Supervisor -->
      <template v-else-if="isSupervisor">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <div class="p-4 sm:p-6 rounded-2xl border" :class="theme('card').value">
            <h3 class="text-lg font-semibold mb-4" :class="theme('cardTitle').value">
              Asistencias Semanales del Personal
            </h3>
            <div class="h-80">
              <AsistenciaSemanalChart :chart-data="asistenciaChartData" />
            </div>
          </div>
          <div class="p-4 sm:p-6 rounded-2xl border" :class="theme('card').value">
            <h3 class="text-lg font-semibold mb-4" :class="theme('cardTitle').value">
              Estado de Asistencia (Hoy)
            </h3>
            <div class="h-80 flex items-center justify-center">
              <EstadoHoyChart :chart-data="estadoHoyChartData" />
            </div>
          </div>
        </div>
      </template>

      <!-- Gráficos para Docente -->
      <template v-else-if="isDocente">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <div class="p-4 sm:p-6 rounded-2xl border" :class="theme('card').value">
            <h3 class="text-lg font-semibold mb-4" :class="theme('cardTitle').value">
              Asistencias Semanales de Mis Alumnos
            </h3>
            <div class="h-80">
              <AsistenciaSemanalChart :chart-data="asistenciaChartData" />
            </div>
          </div>
          <div class="p-4 sm:p-6 rounded-2xl border" :class="theme('card').value">
            <h3 class="text-lg font-semibold mb-4" :class="theme('cardTitle').value">
              Estado de Asistencia Hoy
            </h3>
            <div class="h-80 flex items-center justify-center">
              <EstadoHoyChart :chart-data="estadoHoyChartData" />
            </div>
          </div>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <div class="p-4 sm:p-6 rounded-2xl border" :class="theme('card').value">
            <h3 class="text-lg font-semibold mb-4" :class="theme('cardTitle').value">
              Estudiantes Registrados por Mes
            </h3>
            <div class="h-72">
              <EmpleadosPorMesChart :chart-data="empleadosChartData" />
            </div>
          </div>
          <div class="p-4 sm:p-6 rounded-2xl border" :class="theme('card').value">
            <h3 class="text-lg font-semibold mb-4" :class="theme('cardTitle').value">
              Asistencias por Grupo
            </h3>
            <div class="h-72 flex items-center justify-center">
              <AsistenciaAreaChart :chart-data="asistenciaAreaChartData" />
            </div>
          </div>
        </div>
      </template>
    </div>
  </div>
</template>