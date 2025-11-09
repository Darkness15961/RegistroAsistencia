<template>
  <div class="flex-1 p-4 sm:p-6 overflow-x-hidden">
    <!-- Muestra las tarjetas de área si no hay un área seleccionada -->
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
    
    <!-- Muestra la tabla de empleados si hay un área seleccionada -->
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

    <FormularioEmpleadoModal
      v-if="mostrarModal"
      @close="mostrarModal = false"
      @save="handleSaveEmpleado"
    />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
// Corregidas las rutas de importación de componentes (asumiendo que están en la raíz de components)
import AreaCard from '../components/AreaCard.vue' 
import TablaEmpleados from '../components/TablaEmpleados.vue'
import FormularioEmpleadoModal from '../components/FormularioEmpleadoModal.vue'
import { useTheme } from '../composables/useTheme' 

const mostrarModal = ref(false)

// --- Lógica del Modal ---
const abrirModalNuevo = () => {
  console.log('Abrir modal nuevo empleado')
  mostrarModal.value = true
}

const handleSaveEmpleado = (nuevoEmpleado) => {
  console.log('Guardando nuevo empleado:', nuevoEmpleado)
  // Lógica de simulación para añadir nuevo empleado
  const nuevaId = Math.max(...empleados.value.map(e => e.id)) + 1
  empleados.value.push({
    ...nuevoEmpleado,
    id: nuevaId,
    areaId: areaSeleccionada.value.id
  })
  mostrarModal.value = false
}
// --- Fin Lógica del Modal ---

const iconGradients = {
  'Dirección': 'bg-gradient-to-br from-green-400 to-emerald-600 text-white',
  'Administración': 'bg-gradient-to-br from-pink-400 to-pink-600 text-white',
  'Docentes de Primaria': 'bg-gradient-to-br from-orange-400 to-orange-600 text-white',
  'Docentes de Secundaria': 'bg-gradient-to-br from-violet-400 to-indigo-500 text-white',
  // 'Alumnos de Secundaria' ha sido eliminada
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
  // Eliminamos el área de alumnos aquí: { id: 5, nombre: 'Alumnos de Secundaria', descripcion: 'Estudiantes del nivel secundaria', icon: 'user-graduate' },
  { id: 8, nombre: 'Tutoría y Psicología', descripcion: 'Orientación y apoyo emocional', icon: 'heart' },
  { id: 9, nombre: 'Mantenimiento y Limpieza', descripcion: 'Personal de mantenimiento', icon: 'broom' },
  { id: 10, nombre: 'Seguridad', descripcion: 'Personal de vigilancia', icon: 'shield-alt' },
])

// Solo dejamos empleados (personal)
const empleados = ref([
  // Empleados que eran Alumnos (id 1 y 2) eliminados o reasignados
  { id: 3, nombre: 'María López Quispe', cargo: 'Docente de Matemáticas', correo: 'maria.lopez@colegio.edu', telefono: '987654323', estado: 'Activo', areaId: 4 },
  { id: 4, nombre: 'Juan Pérez Silva', cargo: 'Coordinador Académico', correo: 'juan.perez@colegio.edu', telefono: '987654324', estado: 'Activo', areaId: 1 },
  // Añadimos algunos empleados para llenar los demás campos
  { id: 5, nombre: 'Laura Maza Reyes', cargo: 'Contadora Principal', correo: 'laura.maza@colegio.edu', telefono: '987654325', estado: 'Activo', areaId: 2 },
  { id: 6, nombre: 'Pedro Ortiz Ramos', cargo: 'Vigilante Nocturno', correo: 'pedro.ortiz@colegio.edu', telefono: '987654326', estado: 'Activo', areaId: 10 },
])

// Funciones computadas de la vista empleados
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

const editarEmpleado = (empleado) => {
  // Lógica de simulación de edición
  console.log('Simular edición de empleado', empleado)
  // Aquí se abriría el modal con los datos del empleado
}
const eliminarEmpleado = (id) => {
  // Lógica de simulación de eliminación
  console.log('Simular eliminación de empleado ID', id)
  empleados.value = empleados.value.filter(e => e.id !== id)
}
</script>