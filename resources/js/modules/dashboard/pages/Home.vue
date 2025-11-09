<template>
  <div class="space-y-6">
    <!-- Título -->
    <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-100">
      Panel de Control
    </h1>

    <!-- Sección de tarjetas resumen -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <CardStats
        title="Asistencias Hoy"
        :value="asistenciasHoy"
        icon="fa-calendar-check"
      />
      <CardStats
        title="Empleados Activos"
        :value="empleadosActivos"
        icon="fa-user-tie"
      />
      <CardStats
        title="Alumnos Registrados"
        :value="alumnosRegistrados"
        icon="fa-user-graduate"
      />
      <CardStats
        title="Usuarios del Sistema"
        :value="usuariosSistema"
        icon="fa-user-cog"
      />
    </div>

    <!-- Sección de tablas -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <Card title="Asistencias Recientes">
        <TablaAsis />
      </Card>
      <Card title="Empleados Recientes">
        <TablaEmp />
      </Card>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import Card from '@/modules/dashboard/components/Card.vue'
import CardStats from '@/modules/dashboard/components/CardStats.vue'
import TablaAsis from '@/modules/dashboard/components/TablaAsis.vue'
import TablaEmp from '@/modules/dashboard/components/TablaEmp.vue'

// Datos temporales (luego se reemplazarán con consumo de API)
const asistenciasHoy = ref(18)
const empleadosActivos = ref(42)
const alumnosRegistrados = ref(120)
const usuariosSistema = ref(8)
</script>
