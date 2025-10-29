<template>
  <div class="flex-1 p-4 sm:p-6 overflow-x-hidden">
    <div v-if="!areaSeleccionada" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
      <AreaCard
        v-for="area in areasConPersonal"
        :key="area.id"
        :nombre="area.nombre"
        :descripcion="area.descripcion"
        :icon="area.icon"
        :cantidadPersonas="area.cantidadPersonas"
        :iconClass="iconGradients[area.nombre]"
        @click="seleccionarArea(area)"
      />
    </div>
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

const iconGradients = {
  'Dirección': 'bg-gradient-to-br from-green-400 to-emerald-600 text-white',
  'Administración': 'bg-gradient-to-br from-pink-400 to-pink-600 text-white',
  'Docentes de Primaria': 'bg-gradient-to-br from-orange-400 to-orange-600 text-white',
  'Docentes de Secundaria': 'bg-gradient-to-br from-violet-400 to-indigo-500 text-white',
  'Alumnos de Secundaria': 'bg-gradient-to-br from-cyan-400 to-blue-600 text-white',
  'Tutoría y Psicología': 'bg-gradient-to-br from-red-400 to-pink-500 text-white',
  'Mantenimiento y Limpieza': 'bg-gradient-to-br from-gray-400 to-gray-700 text-white',
  'Seguridad': 'bg-gradient-to-br from-cyan-400 to-cyan-600 text-white',
}

const areaSeleccionada = ref(null)
const areas = ref([
  { id: 1, nombre: 'Dirección', descripcion: 'Supervisión general y gestión institucional', icon: 'user-tie' },
  { id: 2, nombre: 'Administración', descripcion: 'Gestión administrativa, contable y de recursos', icon: 'briefcase' },
  { id: 3, nombre: 'Docentes de Primaria', descripcion: 'Profesores del nivel de educación primaria', icon: 'chalkboard-teacher' },
  { id: 4, nombre: 'Docentes de Secundaria', descripcion: 'Profesores del nivel de educación secundaria', icon: 'graduation-cap' },
  { id: 5, nombre: 'Alumnos de Secundaria', descripcion: 'Estudiantes del nivel secundaria', icon: 'user-graduate' },
  { id: 8, nombre: 'Tutoría y Psicología', descripcion: 'Orientación y apoyo emocional', icon: 'heart' },
  { id: 9, nombre: 'Mantenimiento y Limpieza', descripcion: 'Personal de mantenimiento', icon: 'broom' },
  { id: 10, nombre: 'Seguridad', descripcion: 'Personal de vigilancia', icon: 'shield-alt' },
])
const empleados = ref([
  { id: 1, nombre: 'Carlos Ramírez López', cargo: 'Estudiante 5to Secundaria', correo: 'carlos.ramirez@colegio.edu', telefono: '987654321', estado: 'Activo', areaId: 5 },
  { id: 2, nombre: 'Ana García Torres', cargo: 'Estudiante 4to Secundaria', correo: 'ana.garcia@colegio.edu', telefono: '987654322', estado: 'Activo', areaId: 5 },
  { id: 3, nombre: 'María López Quispe', cargo: 'Docente de Matemáticas', correo: 'maria.lopez@colegio.edu', telefono: '987654323', estado: 'Activo', areaId: 4 },
  { id: 4, nombre: 'Juan Pérez Silva', cargo: 'Coordinador Académico', correo: 'juan.perez@colegio.edu', telefono: '987654324', estado: 'Activo', areaId: 1 },
])
const areasConPersonal = computed(() => {
  return areas.value.map(area => ({
    ...area,
    cantidadPersonas: empleados.value.filter(e => e.areaId === area.id).length
  }))
})
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