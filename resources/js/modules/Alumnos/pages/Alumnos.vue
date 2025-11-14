<template>
  <div class="flex-1 p-4 sm:p-6 overflow-x-hidden">

    <div v-if="loadingAlumnos || loadingGrupos || loadingAreas" class="text-center py-20" :class="theme('cardSubtitle').value">
      <i class="fas fa-spinner fa-spin text-4xl"></i>
      <p class="mt-3 text-lg">Cargando datos...</p>
    </div>

    <div v-else-if="errorAlumnos" class="text-red-400 text-center py-20">
      <i class="fas fa-exclamation-triangle text-4xl mb-3"></i>
      <p>{{ errorAlumnos }}</p>
    </div>

    <div v-else>
      <div v-if="!grupoSeleccionado" class="space-y-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
          <div>
            <h1 class="text-3xl font-bold mb-2" :class="theme('cardTitle').value">
              Alumnos por Grupo
            </h1>
            <p class="text-sm" :class="theme('cardSubtitle').value">
              Selecciona un grupo para ver los alumnos
            </p>
          </div>
          <button 
            @click="abrirModalNuevo"
            class="flex items-center justify-center gap-2 px-6 py-3 rounded-xl font-semibold shadow-lg"
            :class="theme('buttonPrimary').value"
          >
            <i class="fas fa-plus"></i>
            Nuevo Alumno
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
            v-if="gruposDeAlumnosFiltrados.length > 0 || alumnosSinGrupo.length > 0"
            :grupo="grupoSinAsignar"
            :cantidadPersonas="alumnosSinGrupo.length"
            @click="seleccionarGrupo(grupoSinAsignar)"
          />
          
          <GrupoCard
            v-for="grupo in gruposDeAlumnosFiltrados"
            :key="grupo.id_grupo"
            :grupo="grupo"
            :cantidadPersonas="grupo.cantidadPersonas"
            @click="seleccionarGrupo(grupo)"
          />
        </div>

        <p v-if="!gruposDeAlumnosFiltrados.length && alumnosSinGrupo.length === 0" class="text-center py-12 text-lg" :class="theme('cardSubtitle').value">
          No se encontraron grupos ni alumnos sin asignar.
        </p>

      </div>
      
      <TablaAlumnos
        v-else
        :alumnos="alumnosDelGrupo"
        :nombreGrupo="getNombreGrupo(grupoSeleccionado)"
        @volver="grupoSeleccionado = null" 
        @nuevoAlumno="abrirModalNuevo"
        @editar="abrirModalEditar"
        @eliminar="handleEliminarAlumno"
      />
    </div>

    <FormularioAlumnoModal
      v-if="mostrarModal"
      :alumno="alumnoSeleccionado"
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
import { useGrupos } from '@/composables/useGrupos'
import GrupoCard from '@/modules/empleados/components/GrupoCard.vue'
import TablaAlumnos from '../components/TablaAlumnos.vue'
import FormularioAlumnoModal from '../components/FormularioAlumnoModal.vue'

const { theme } = useTheme()

const { 
  alumnos, 
  loading: loadingAlumnos, 
  error: errorAlumnos, 
  fetchAlumnos, 
  eliminarAlumno 
} = useAlumnos()
const { 
  grupos, 
  loading: loadingGrupos, 
  fetchGrupos 
} = useGrupos()
const areas = ref([])
const loadingAreas = ref(true)

onMounted(async () => {
  loadingAreas.value = true
  await Promise.all([
    fetchAlumnos(),
    fetchGrupos(),
    api.get('/areas').then(res => areas.value = res.data)
  ]).catch(e => {
    console.error(e)
    errorAlumnos.value = "No se pudieron cargar los datos."
  })
  loadingAreas.value = false
})

const mostrarModal = ref(false)
const alumnoSeleccionado = ref(null)
const grupoSeleccionado = ref(null)
const busquedaGrupo = ref('')

const grupoSinAsignar = {
  id_grupo: 'unassigned',
  nivel: 'Alumnos',
  grado: 'Sin Asignar',
  seccion: '',
  area: {
    nombre_area: 'Sin Grupo'
  }
}

const gruposConArea = computed(() => {
  return grupos.value.map(g => ({
    ...g,
    area: areas.value.find(a => a.id_area === g.id_area)
  }))
})

const gruposDeAlumnos = computed(() => {
  return gruposConArea.value
    .filter(g => g.area && g.area.nombre_area.toLowerCase().includes('alumno'))
    .map(g => ({
      ...g,
      cantidadPersonas: alumnos.value.filter(e => e.id_grupo === g.id_grupo).length
    }))
})

const gruposDeAlumnosFiltrados = computed(() => {
  if (!busquedaGrupo.value) {
    return gruposDeAlumnos.value
  }
  const search = busquedaGrupo.value.toLowerCase()
  return gruposDeAlumnos.value.filter(g => 
    (g.area?.nombre_area.toLowerCase().includes(search)) ||
    (g.nivel && g.nivel.toLowerCase().includes(search)) ||
    (g.grado && g.grado.toLowerCase().includes(search))
  )
})

const alumnosSinGrupo = computed(() => {
  return alumnos.value.filter(a => !a.id_grupo)
})

const alumnosDelGrupo = computed(() => {
  if (!grupoSeleccionado.value) return []
  if (grupoSeleccionado.value.id_grupo === 'unassigned') {
    return alumnosSinGrupo.value
  }
  return alumnos.value.filter(a => a.id_grupo === grupoSeleccionado.value.id_grupo)
})

const seleccionarGrupo = (grupo) => {
  grupoSeleccionado.value = grupo
}

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
  fetchAlumnos() 
}
const handleEliminarAlumno = async (id) => {
  if (!confirm('¿Seguro que deseas eliminar este alumno?')) return
  await eliminarAlumno(id)
}

const getNombreGrupo = (grupo) => {
  if (!grupo) return ''
  if (grupo.grado || grupo.nivel) {
    return `${grupo.nivel || ''} ${grupo.grado || ''} "${grupo.seccion || ''}"`.trim()
  }
  return grupo.area?.nombre_area || 'Grupo'
}
</script>