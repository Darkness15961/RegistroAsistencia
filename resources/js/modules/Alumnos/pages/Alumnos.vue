<template>
  <div class="flex-1 p-4 sm:p-6 overflow-x-hidden">

    <div v-if="loadingAlumnos || loadingAreas" class="text-center py-20" :class="theme('cardSubtitle').value">
      <i class="fas fa-spinner fa-spin text-4xl"></i>
      <p class="mt-3 text-lg">Cargando datos...</p>
    </div>

    <div v-else-if="errorAlumnos" class="text-red-400 text-center py-20">
      <i class="fas fa-exclamation-triangle text-4xl mb-3"></i>
      <p>{{ errorAlumnos }}</p>
    </div>

    <div v-else>
      <div v-if="!areaSeleccionada" class="space-y-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
          <div>
            <h1 class="text-3xl font-bold mb-2" :class="theme('cardTitle').value">
              Alumnos por Área
            </h1>
            <p class="text-sm" :class="theme('cardSubtitle').value">
              Selecciona un área para ver los alumnos
            </p>
          </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
          <AreaCard
            v-for="area in areasDeAlumnos"
            :key="area.id_area"
            :nombre="area.nombre_area"
            :descripcion="area.codigo || 'Sin código'"
            :icon="getAreaStyle(area.nombre_area).icon"
            :cantidadPersonas="area.cantidadPersonas"
            :iconClass="getAreaStyle(area.nombre_area).gradient"
            @click="seleccionarArea(area)"
          />
        </div>
      </div>
      
      <TablaAlumnos
        v-else
        :alumnos="alumnosDelArea"
        :nombreArea="areaSeleccionada.nombre_area"
        @volver="areaSeleccionada = null" 
        @nuevoAlumno="abrirModalNuevo"
        @editar="abrirModalEditar"
        @eliminar="handleEliminarAlumno"
      />
    </div>

    <FormularioAlumnoModal
      v-if="mostrarModal"
      :alumno="alumnoSeleccionado"
      :areas="areasDeAlumnos"
      @cerrar="cerrarModal"
      @actualizado="handleGuardado"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '@/axiosConfig'
import { useTheme } from '@/composables/useTheme'
import { useAlumnos } from '@/composables/useAlumnos' 
import AreaCard from '@/modules/empleados/components/AreaCard.vue' // Reutilizamos el AreaCard de empleados
import TablaAlumnos from '../components/TablaAlumnos.vue'
import FormularioAlumnoModal from '../components/FormularioAlumnoModal.vue'

const { theme } = useTheme()

// --- Lógica de Datos ---
const { 
  alumnos, 
  loading: loadingAlumnos, 
  error: errorAlumnos, 
  fetchAlumnos, 
  eliminarAlumno 
} = useAlumnos()
const areas = ref([])
const areaSeleccionada = ref(null)
const loadingAreas = ref(true)

onMounted(async () => {
  loadingAreas.value = true
  await fetchAlumnos() // Carga todos los alumnos
  try {
    const res = await api.get('/areas') // Carga todas las áreas
    areas.value = res.data
  } catch (e) {
    errorAlumnos.value = "No se pudieron cargar las áreas."
  } finally {
    loadingAreas.value = false
  }
})

// --- Lógica de UI ---
const mostrarModal = ref(false)
const alumnoSeleccionado = ref(null)

// ✅ FILTRO DE ÁREAS (Alumnos)
const areasDeAlumnos = computed(() => {
  return areas.value
    .filter(area => area.nombre_area.toLowerCase().includes('alumno'))
    .map(area => ({
      ...area,
      cantidadPersonas: alumnos.value.filter(e => e.id_area === area.id_area).length
    }))
})

const alumnosDelArea = computed(() => {
  if (!areaSeleccionada.value) return []
  return alumnos.value.filter(e => e.id_area === areaSeleccionada.value.id_area)
})

const seleccionarArea = (area) => {
  areaSeleccionada.value = area
}

// --- Lógica de Modal ---
const abrirModalNuevo = () => {
  alumnoSeleccionado.value = null
  mostrarModal.value = true
}
const abrirModalEditar = (alumno) => {
  alumnoSeleccionado.value = alumno
  mostrarModal.value = true
}
const cerrarModal = () => {
  mostrarModal.value = false
  alumnoSeleccionado.value = null
}
const handleGuardado = () => {
  cerrarModal()
  fetchAlumnos() // Recarga la lista de alumnos
}
const handleEliminarAlumno = async (id) => {
  if (!confirm('¿Seguro que deseas eliminar este alumno?')) return
  await eliminarAlumno(id)
}

// --- Estilos ---
const styleCatalog = {
  'Alumnos de Inicial':   { gradient: 'bg-gradient-to-br from-emerald-400 to-green-500', icon: 'fas fa-child' },
  'Alumnos de Primaria':  { gradient: 'bg-gradient-to-br from-blue-400 to-sky-500',     icon: 'fas fa-book-reader' },
  'Alumnos de Secundaria':{ gradient: 'bg-gradient-to-br from-purple-400 to-fuchsia-500', icon: 'fas fa-users' },
}
const defaultStyle = { gradient: 'bg-gradient-to-br from-gray-400 to-gray-600', icon: 'fas fa-user-graduate' }
const getAreaStyle = (nombre) => styleCatalog[nombre] ?? defaultStyle

</script>