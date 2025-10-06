<template>
  <div>
    <!-- Vista de áreas (grid de cards) -->
    <div v-if="!areaSeleccionada" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
      <AreaCard
        v-for="area in areasConPersonal"
        :key="area.id"
        :nombre="area.nombre"
        :descripcion="area.descripcion"
        :icon="area.icon"
        :gradientClass="area.color"
        :cantidadPersonas="area.cantidadPersonas"
        @click="seleccionarArea(area)"
      />
    </div>

    <!-- Vista de empleados de área seleccionada -->
    <TablaEmpleados
      v-else
      :empleados="empleadosDelArea"
      :nombreArea="areaSeleccionada.nombre"
      :iconoArea="areaSeleccionada.icon"
      @volver="areaSeleccionada = null"
      @nuevoEmpleado="abrirModalNuevo"
      @editar="editarEmpleado"
      @eliminar="eliminarEmpleado"
    />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import AreaCard from '@/components/AreaCard.vue'
import TablaEmpleados from '@/components/TablaEmpleados.vue'

const areaSeleccionada = ref(null)

// Datos de ejemplo - reemplazar con tu API
const areas = ref([
  { id: 1, nombre: 'Dirección', descripcion: 'Supervisión general y gestión institucional', icon: 'user-tie', color: 'from-purple-400 to-purple-600' },
  { id: 2, nombre: 'Administración', descripcion: 'Gestión administrativa, contable y de recursos', icon: 'briefcase', color: 'from-blue-400 to-blue-600' },
  { id: 3, nombre: 'Docentes de Primaria', descripcion: 'Profesores del nivel de educación primaria', icon: 'chalkboard-teacher', color: 'from-green-400 to-emerald-600' },
  { id: 4, nombre: 'Docentes de Secundaria', descripcion: 'Profesores del nivel de educación secundaria', icon: 'graduation-cap', color: 'from-pink-400 to-pink-600' },
  { id: 5, nombre: 'Alumnos de Secundaria', descripcion: 'Estudiantes del nivel secundaria', icon: 'user-graduate', color: 'from-orange-400 to-orange-600' },
  { id: 8, nombre: 'Tutoría y Psicología', descripcion: 'Orientación y apoyo emocional', icon: 'heart', color: 'from-red-400 to-red-600' },
  { id: 9, nombre: 'Mantenimiento y Limpieza', descripcion: 'Personal de mantenimiento', icon: 'broom', color: 'from-cyan-400 to-cyan-600' },
  { id: 10, nombre: 'Seguridad', descripcion: 'Personal de vigilancia', icon: 'shield-alt', color: 'from-indigo-400 to-indigo-600' },
])

const empleados = ref([
  { id: 1, nombre: 'Carlos Ramírez López', cargo: 'Estudiante 5to Secundaria', correo: 'carlos.ramirez@colegio.edu', telefono: '987654321', estado: 'Activo', areaId: 5 },
  { id: 2, nombre: 'Ana García Torres', cargo: 'Estudiante 4to Secundaria', correo: 'ana.garcia@colegio.edu', telefono: '987654322', estado: 'Activo', areaId: 5 },
  { id: 3, nombre: 'María López Quispe', cargo: 'Docente de Matemáticas', correo: 'maria.lopez@colegio.edu', telefono: '987654323', estado: 'Activo', areaId: 4 },
  { id: 4, nombre: 'Juan Pérez Silva', cargo: 'Coordinador Académico', correo: 'juan.perez@colegio.edu', telefono: '987654324', estado: 'Activo', areaId: 1 },
])

// Computed para agregar cantidad de personas a cada área
const areasConPersonal = computed(() => {
  return areas.value.map(area => ({
    ...area,
    cantidadPersonas: empleados.value.filter(e => e.areaId === area.id).length
  }))
})

// Empleados del área seleccionada
const empleadosDelArea = computed(() => {
  if (!areaSeleccionada.value) return []
  return empleados.value.filter(e => e.areaId === areaSeleccionada.value.id)
})

const seleccionarArea = (area) => {
  areaSeleccionada.value = area
}

const abrirModalNuevo = () => {
  console.log('Abrir modal nuevo empleado')
}

const editarEmpleado = (empleado) => {
  console.log('Editar', empleado)
}

const eliminarEmpleado = (id) => {
  console.log('Eliminar', id)
}
</script>