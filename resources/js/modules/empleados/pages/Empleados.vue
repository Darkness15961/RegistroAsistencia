<template>
  <div class="flex-1 p-4 sm:p-6 overflow-x-hidden">

    <div v-if="loading || loadingAreas" class="text-center py-20" :class="theme('cardSubtitle').value">
      <i class="fas fa-spinner fa-spin text-4xl"></i>
      <p class="mt-3 text-lg">Cargando datos...</p>
    </div>

    <div v-else-if="error" class="text-red-400 text-center py-20">
      <i class="fas fa-exclamation-triangle text-4xl mb-3"></i>
      <p>{{ error }}</p>
    </div>

    <div v-else>
      <div v-if="!areaSeleccionada" class="space-y-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
          <div>
            <h1 class="text-3xl font-bold mb-2" :class="theme('cardTitle').value">
              Empleados por Área
            </h1>
            <p class="text-sm" :class="theme('cardSubtitle').value">
              Selecciona un área para ver el personal
            </p>
          </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
          <AreaCard
            v-for="area in areasDeEmpleados"
            :key="area.id_area"
            :nombre="area.nombre_area"
            :descripcion="area.codigo || 'Sin código'"
            :cantidadPersonas="area.cantidadPersonas"
            @click="seleccionarArea(area)"
          />
        </div>
      </div>
      
      <TablaEmpleados
        v-else
        :empleados="empleadosDelArea"
        :nombreArea="areaSeleccionada.nombre_area"
        @volver="areaSeleccionada = null" 
        @nuevoEmpleado="abrirModalNuevo"
        @editar="abrirModalEditar"
        @eliminar="handleEliminarEmpleado"
      />
    </div>

    <FormularioEmpleadoModal
      v-if="mostrarModal"
      :empleado="empleadoSeleccionado"
      :areas="areasDeEmpleados"
      @cerrar="cerrarModal"
      @actualizado="handleGuardado"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '@/axiosConfig'
import { useTheme } from '@/composables/useTheme'
import { useEmpleados } from '../composables/useEmpleados' 
import AreaCard from '../components/AreaCard.vue' 
import TablaEmpleados from '../components/TablaEmpleados.vue'
import FormularioEmpleadoModal from '../components/FormularioEmpleadoModal.vue'

const { theme } = useTheme()

// --- Lógica de Datos ---
const { empleados, loading, error, fetchEmpleados, eliminarEmpleado } = useEmpleados()
const areas = ref([])
const areaSeleccionada = ref(null)

const loadingAreas = ref(true)
onMounted(async () => {
  loadingAreas.value = true
  await fetchEmpleados() // Carga todos los empleados
  try {
    const res = await api.get('/areas') // Carga todas las áreas
    areas.value = res.data
  } catch (e) {
    error.value = "No se pudieron cargar las áreas."
  } finally {
    loadingAreas.value = false
  }
})

// --- Lógica de UI ---
const mostrarModal = ref(false)
const empleadoSeleccionado = ref(null)

// ✅ FILTRO DE ÁREAS (Empleados)
// Filtra las áreas para EXCLUIR las que son de alumnos
const areasDeEmpleados = computed(() => {
  return areas.value
    .filter(area => !area.nombre_area.toLowerCase().includes('alumno'))
    .map(area => ({
      ...area,
      cantidadPersonas: empleados.value.filter(e => e.id_area === area.id_area).length
    }))
})

const empleadosDelArea = computed(() => {
  if (!areaSeleccionada.value) return []
  return empleados.value.filter(e => e.id_area === areaSeleccionada.value.id_area)
})

const seleccionarArea = (area) => {
  areaSeleccionada.value = area
}

// --- Lógica de Modal ---
const abrirModalNuevo = () => {
  empleadoSeleccionado.value = null // Asegura que esté vacío
  mostrarModal.value = true
}
const abrirModalEditar = (empleado) => {
  empleadoSeleccionado.value = empleado
  mostrarModal.value = true
}
const cerrarModal = () => {
  mostrarModal.value = false
  empleadoSeleccionado.value = null
}
const handleGuardado = () => {
  cerrarModal()
  // No es necesario fetchEmpleados() porque useEmpleados lo hace
}
const handleEliminarEmpleado = async (id) => {
  if (!confirm('¿Seguro que deseas eliminar este empleado?')) return
  await eliminarEmpleado(id)
  // No es necesario fetchEmpleados() porque useEmpleados ya actualiza la lista
}
</script>