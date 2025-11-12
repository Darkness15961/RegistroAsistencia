<template>
  <div class="flex-1 p-4 sm:p-6 overflow-x-hidden">

    <div v-if="loading" class="text-center py-20" :class="theme('cardSubtitle').value">
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
              Asistencias por Área
            </h1>
            <p class="text-sm" :class="theme('cardSubtitle').value">
              Selecciona un área para ver el resumen de asistencia semanal
            </p>
          </div>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
          <AreaCard
            v-for="area in areasConAsistencia"
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
      
      <TablaAsistencia
        v-else
        :asistencias="asistenciasDelArea"
        :nombreArea="areaSeleccionada.nombre_area"
        @volver="areaSeleccionada = null" 
      />
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '@/axiosConfig'
import { useTheme } from '@/composables/useTheme'
// Importamos los componentes
import AreaCard from '@/modules/empleados/components/AreaCard.vue' // Reutiliza el de empleados
import TablaAsistencia from '../components/TablaAsistencia.vue'

const { theme } = useTheme()

// --- Lógica de Datos ---
const asistencias = ref([]) // Los datos de /asistencias-semana
const areas = ref([])
const loading = ref(true)
const error = ref(null)
const areaSeleccionada = ref(null)

onMounted(async () => {
  loading.value = true
  error.value = null
  try {
    // Cargamos ambas fuentes de datos en paralelo
    const [resAsistencias, resAreas] = await Promise.all([
      api.get('/asistencias-semana'),
      api.get('/areas')
    ])
    
    asistencias.value = Array.isArray(resAsistencias.data) ? resAsistencias.data : []
    areas.value = resAreas.data
  } catch (e) {
    console.error(e)
    error.value = "No se pudieron cargar los datos de asistencia."
  } finally {
    loading.value = false
  }
})

// --- Lógica de UI ---

// Calcula cuántas personas de cada área están en el reporte semanal
const areasConAsistencia = computed(() => {
  return areas.value.map(area => ({
    ...area,
    cantidadPersonas: asistencias.value.filter(a => a.persona?.id_area === area.id_area).length
  }))
})

// Filtra las asistencias para la tabla cuando se selecciona un área
const asistenciasDelArea = computed(() => {
  if (!areaSeleccionada.value) return []
  return asistencias.value.filter(a => a.persona?.id_area === areaSeleccionada.value.id_area)
})

const seleccionarArea = (area) => {
  areaSeleccionada.value = area
}

// --- Estilos (copiados de Empleados.vue) ---
const styleCatalog = {
  'Dirección':            { gradient: 'bg-gradient-to-br from-green-400 to-emerald-600', icon: 'fas fa-building' },
  'Administración':       { gradient: 'bg-gradient-to-br from-pink-400 to-pink-600',    icon: 'fas fa-briefcase' },
  'Docentes de Primaria': { gradient: 'bg-gradient-to-br from-orange-400 to-orange-600', icon: 'fas fa-chalkboard-teacher' },
  'Docentes de Secundaria': { gradient: 'bg-gradient-to-br from-violet-400 to-indigo-500',  icon: 'fas fa-user-graduate' },
  'Alumnos de Inicial':   { gradient: 'bg-gradient-to-br from-emerald-400 to-green-500', icon: 'fas fa-child' },
  'Alumnos de Primaria':  { gradient: 'bg-gradient-to-br from-blue-400 to-sky-500',     icon: 'fas fa-book-reader' },
  'Alumnos de Secundaria':{ gradient: 'bg-gradient-to-br from-purple-400 to-fuchsia-500', icon: 'fas fa-users' },
  'Tutoría y Psicología': { gradient: 'bg-gradient-to-br from-red-400 to-pink-500',   icon: 'fas fa-hands-helping' },
  'Mantenimiento y Limpieza': { gradient: 'bg-gradient-to-br from-gray-400 to-gray-700',   icon: 'fas fa-broom' },
  'Seguridad':            { gradient: 'bg-gradient-to-br from-cyan-400 to-cyan-600',     icon: 'fas fa-shield-alt' },
  'Biblioteca':           { gradient: 'bg-gradient-to-br from-yellow-400 to-amber-500', icon: 'fas fa-book' },
  'Laboratorio':          { gradient: 'bg-gradient-to-br from-green-400 to-lime-500',   icon: 'fas fa-flask' },
  'Coordinación Académica': { gradient: 'bg-gradient-to-br from-teal-400 to-cyan-500', icon: 'fas fa-graduation-cap' },
  'Servicio Médico':      { gradient: 'bg-gradient-to-br from-red-400 to-rose-500',     icon: 'fas fa-medkit' },
}
const defaultStyle = { gradient: 'bg-gradient-to-br from-slate-400 to-slate-600', icon: 'fas fa-university' }
const getAreaStyle = (nombre) => styleCatalog[nombre] ?? defaultStyle
</script>