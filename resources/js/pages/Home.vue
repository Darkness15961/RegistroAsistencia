<script setup>
import Card from '@/components/Card.vue'
import AttendanceChart from '@/components/TablaAsis.vue'
import EmployeesChart from '@/components/TablaEmp.vue'
</script>

<template>
  <div class="flex-1 p-4 sm:p-6 overflow-x-hidden">
    <!-- Tarjetas de estadísticas -->
    <section
      class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 mb-8"
    >
       <Card 
        title="Empleados" 
        value="25" 
        icon="users" 
        subtitle="Nuevos registros esta semana" 
      />
      <Card 
        title="Asistencias Hoy" 
        value="21" 
        icon="calendar-check" 
        subtitle="Última actualización 10:00 AM" 
      />
      <Card 
        title="Horarios Activos" 
        value="5" 
        icon="clock" 
        subtitle="Turnos configurados correctamente" 
      />
      <Card 
        title="Usuarios del Sistema" 
        value="4" 
        icon="user-shield" 
        subtitle="Roles sincronizados con permisos" 
      />
    </section>

    <!-- Gráficos -->
    <section class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
      <div
        class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl shadow-md p-4 sm:p-6"
      >
        <AttendanceChart />
      </div>
      <div
        class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl shadow-md p-4 sm:p-6"
      >
        <EmployeesChart />
      </div>
    </section>
  </div>
</template>
