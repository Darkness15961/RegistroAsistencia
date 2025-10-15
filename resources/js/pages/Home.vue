<script setup>
import Card from '@/components/Card.vue'
import AttendanceChart from '@/components/TablaAsis.vue'
import EmployeesChart from '@/components/TablaEmp.vue'
import { useTheme } from '../composables/useTheme'

const { theme } = useTheme()
</script>

<template>
  <div class="flex-1 p-4 sm:p-6 overflow-x-hidden">
    <!-- Tarjetas de estadísticas -->
    <section class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-10">
      <Card 
        title="Empleados" 
        value="25" 
        icon="users"
        gradient-class="bg-gradient-to-br from-blue-400 to-blue-600"
      />
      <Card 
        title="Asistencias Hoy" 
        value="21" 
        icon="calendar-check"
        gradient-class="bg-gradient-to-br from-green-400 to-emerald-600"
      />
      <Card 
        title="Horarios Activos" 
        value="5" 
        icon="clock"
        gradient-class="bg-gradient-to-br from-purple-400 to-pink-600"
      />
      <Card 
        title="Usuarios del Sistema" 
        value="4" 
        icon="user-shield"
        gradient-class="bg-gradient-to-br from-orange-400 to-red-600"
      />
    </section>

    <!-- Gráficos -->
    <section class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
      <div
        class="backdrop-blur-xl rounded-3xl shadow-md p-4 sm:p-6 transition-colors"
        :class="theme('card').value"
      >
        <AttendanceChart />
      </div>
      <div
        class="backdrop-blur-xl rounded-3xl shadow-md p-4 sm:p-6 transition-colors"
        :class="theme('card').value"
      >
        <EmployeesChart />
      </div>
    </section>
  </div>
</template>