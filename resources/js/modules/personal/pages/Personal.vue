<template>
  <div class="flex-1 p-4 sm:p-6 overflow-x-hidden">

    <div v-if="loadingPersonal || loadingGrupos || loadingAreas" class="text-center py-20" :class="theme('cardSubtitle').value">
      <i class="fas fa-spinner fa-spin text-4xl"></i>
      <p class="mt-3 text-lg">Cargando datos...</p>
    </div>

    <div v-else-if="errorPersonal" class="text-red-400 text-center py-20">
      <i class="fas fa-exclamation-triangle text-4xl mb-3"></i>
      <p>{{ errorPersonal }}</p>
    </div>

    <div v-else>
      
      <div v-if="!grupoSeleccionado" class="space-y-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
          <div>
            <h1 class="text-3xl font-bold mb-2" :class="theme('cardTitle').value">
              Personal por Grupo
            </h1>
            <p class="text-sm" :class="theme('cardSubtitle').value">
              Selecciona un grupo para ver el personal
            </p>
          </div>
          <button 
            @click="abrirModalNuevo"
            class="flex items-center justify-center gap-2 px-6 py-3 rounded-xl font-semibold shadow-lg"
            :class="theme('buttonPrimary').value"
          >
            <i class="fas fa-plus"></i>
            Nuevo Personal
          </button>
        </div>
        
        <div class="relative w-full sm:max-w-md">
          <input
            v-model="busquedaGrupo"
            type="text"
            placeholder="Buscar por grupo o área..."
            class="w-full rounded-xl pl-10 pr-4 py-3 outline-none border transition-colors"
            :class="theme('input').value"
          />
          <i 
            class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2"
            :class="theme('cardSubtitle').value"
          ></i>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
          
          <GrupoCard
            v-if="personalSinGrupo.length > 0"
            :grupo="grupoSinAsignar"
            :cantidadPersonas="personalSinGrupo.length"
            @click="seleccionarGrupo(grupoSinAsignar)"
          />
          
          <GrupoCard
            v-for="grupo in gruposDePersonalFiltrados"
            :key="grupo.id_grupo"
            :grupo="grupo"
            :cantidadPersonas="grupo.cantidadPersonas"
            @click="seleccionarGrupo(grupo)"
          />
        </div>

        <p v-if="!gruposDePersonalFiltrados.length && !personalSinGrupo.length" class="text-center py-12 text-lg" :class="theme('cardSubtitle').value">
          No se encontraron grupos ni personal sin asignar.
        </p>
      </div>
      
      <TablaPersonal
        v-else
        :personal="personalDelGrupo"
        :nombreGrupo="getNombreGrupo(grupoSeleccionado)"
        @volver="grupoSeleccionado = null" 
        @nuevoPersonal="abrirModalNuevo"
        @editar="abrirModalEditar"
        @eliminar="handleEliminarPersonal"
      />
    </div>

    <FormularioPersonalModal
      v-if="mostrarModal"
      :empleado="personalSeleccionado"
      @cerrar="cerrarModal"
      @actualizado="handleGuardado"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '@/axiosConfig'
import { useTheme } from '@/composables/useTheme'
import { usePersonal } from '../composables/usePersonal' 
import { useGrupos } from '@/composables/useGrupos'
// Asegúrate de que las rutas de importación sean correctas
import GrupoCard from '../components/GrupoCard.vue'
import TablaPersonal from '../components/TablaPersonal.vue'
import FormularioPersonalModal from '../components/FormularioPersonalModal.vue'

const { theme } = useTheme()

// --- Lógica de Datos ---
const { 
  personal, // Renombrado de 'empleados'
  loading: loadingPersonal, 
  error: errorPersonal, 
  fetchPersonal, // Renombrado de 'fetchEmpleados'
  eliminarPersonal // Renombrado de 'eliminarEmpleado'
} = usePersonal()

const { 
  grupos, 
  loading: loadingGrupos, 
  fetchGrupos 
} = useGrupos()

const areas = ref([])
const loadingAreas = ref(true)

// Carga inicial
onMounted(async () => {
  loadingAreas.value = true
  try {
    await Promise.all([
      fetchPersonal(),
      fetchGrupos(),
      api.get('/areas').then(res => areas.value = res.data)
    ])
  } catch (e) {
    console.error(e)
    errorPersonal.value = "No se pudieron cargar los datos."
  } finally {
    loadingAreas.value = false
  }
})

// --- Lógica de UI ---
const mostrarModal = ref(false)
const personalSeleccionado = ref(null)
const grupoSeleccionado = ref(null)
const busquedaGrupo = ref('')

// Objeto "Falso" para la tarjeta "Sin Asignar"
const grupoSinAsignar = {
  id_grupo: 'unassigned',
  nivel: 'Personal',
  grado: 'Sin Asignar',
  seccion: '',
  area: {
    nombre_area: 'Sin Grupo'
  }
}

// Helper para unir grupos con sus áreas
const gruposConArea = computed(() => {
  return grupos.value.map(g => ({
    ...g,
    area: areas.value.find(a => a.id_area === g.id_area) || {}
  }))
})

// Filtra grupos que NO son de alumnos y cuenta su personal
const gruposDePersonal = computed(() => {
  return gruposConArea.value
    .filter(g => g.area && g.area.nombre_area && !g.area.nombre_area.toLowerCase().includes('alumno'))
    .map(g => ({
      ...g,
      cantidadPersonas: personal.value.filter(p => p.id_grupo === g.id_grupo).length
    }))
})

// Filtra los grupos según el buscador
const gruposDePersonalFiltrados = computed(() => {
  if (!busquedaGrupo.value) return gruposDePersonal.value
  const search = busquedaGrupo.value.toLowerCase()
  
  return gruposDePersonal.value.filter(g => 
    (g.area?.nombre_area && g.area.nombre_area.toLowerCase().includes(search)) ||
    (g.nivel && g.nivel.toLowerCase().includes(search)) ||
    (g.grado && g.grado.toLowerCase().includes(search))
  )
})

// Personal sin grupo asignado
const personalSinGrupo = computed(() => {
  return personal.value.filter(p => !p.id_grupo)
})

// Personal del grupo seleccionado actualmente
const personalDelGrupo = computed(() => {
  if (!grupoSeleccionado.value) return []
  
  if (grupoSeleccionado.value.id_grupo === 'unassigned') {
    return personalSinGrupo.value
  }
  return personal.value.filter(p => p.id_grupo === grupoSeleccionado.value.id_grupo)
})

const seleccionarGrupo = (grupo) => {
  grupoSeleccionado.value = grupo
}

// --- Lógica de Modal ---
const abrirModalNuevo = () => {
  personalSeleccionado.value = null
  mostrarModal.value = true
}
const abrirModalEditar = (persona) => {
  personalSeleccionado.value = persona
  mostrarModal.value = true
}
const cerrarModal = () => {
  mostrarModal.value = false
  personalSeleccionado.value = null
}
const handleGuardado = () => {
  cerrarModal()
  fetchPersonal() // Recargar lista
}
const handleEliminarPersonal = async (id) => {
  if (!confirm('¿Seguro que deseas eliminar este registro?')) return
  await eliminarPersonal(id)
}

// --- Utilidad ---
const getNombreGrupo = (grupo) => {
  if (!grupo) return ''
  if (grupo.grado || grupo.nivel) {
    return `${grupo.nivel || ''} ${grupo.grado || ''} "${grupo.seccion || ''}"`.trim()
  }
  return grupo.area?.nombre_area || 'Grupo'
}
</script>