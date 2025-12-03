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
          
          <!-- Card "Sin Asignar" solo visible para admin, secretaria y supervisor -->
          <GrupoCard
            v-if="(isAdmin || isSecretaria || isSupervisor) && alumnosSinGrupo.length > 0"
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

        <p v-if="!gruposDeAlumnosFiltrados.length" class="text-center py-12 text-lg" :class="theme('cardSubtitle').value">
          No se encontraron grupos.
        </p>

      </div>
      
      <TablaAlumnos
        v-else
        :alumnos="alumnosDelGrupo"
        :nombreGrupo="getNombreGrupo(grupoSeleccionado)"
        :modoSeleccion="grupoSeleccionado?.id_grupo === 'unassigned'"
        @volver="grupoSeleccionado = null" 
        @nuevoAlumno="abrirModalNuevo"
        @editar="abrirModalEditar"
        @eliminar="handleEliminarAlumno"
        @seleccionCambiada="alumnosSeleccionados = $event"
      />
    </div>

    <!-- Barra flotante estilo Gmail -->
    <Transition name="slide-up">
      <div 
        v-if="alumnosSeleccionados.length > 0"
        class="fixed bottom-6 left-1/2 -translate-x-1/2 backdrop-blur-xl border rounded-2xl shadow-2xl p-4 z-50 flex items-center gap-4"
        :class="isDark ? 'bg-gray-800/95 border-gray-700' : 'bg-white/95 border-gray-200'"
      >
        <span class="font-semibold" :class="isDark ? 'text-white' : 'text-gray-900'">
          {{ alumnosSeleccionados.length }} seleccionado(s)
        </span>
        <button 
          @click="mostrarModalGrupo = true"
          class="px-6 py-2 rounded-xl font-semibold shadow-lg hover:scale-105 transition-all duration-200"
          :class="theme('buttonPrimary').value"
        >
          <i class="fas fa-exchange-alt mr-2"></i>
          Cambiar de Grupo
        </button>
        <button 
          @click="alumnosSeleccionados = []"
          class="p-2 rounded-xl hover:bg-red-500/20 transition-colors"
          :class="isDark ? 'text-red-300' : 'text-red-600'"
        >
          <i class="fas fa-times"></i>
        </button>
      </div>
    </Transition>

    <FormularioAlumnoModal
      v-if="mostrarModal"
      :alumno="alumnoSeleccionado"
      :grupo="grupoSeleccionado"
      @cerrar="cerrarModal"
      @actualizado="handleGuardado"
    </FormularioAlumnoModal>

    <!-- Modal para seleccionar grupo destino -->
    <Teleport to="body">
      <Transition name="fade">
        <div 
          v-if="mostrarModalGrupo"
          class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-[100] p-4"
          @click.self="mostrarModalGrupo = false"
        >
          <div 
            class="backdrop-blur-xl border rounded-3xl shadow-2xl p-6 w-full max-w-md"
            :class="theme('card').value"
          >
            <h2 class="text-2xl font-bold mb-4" :class="theme('cardTitle').value">
              <i class="fas fa-exchange-alt mr-2"></i>
              Asignar a Grupo
            </h2>
            
            <p class="mb-4" :class="theme('cardSubtitle').value">
              Selecciona el grupo al que deseas asignar los {{ alumnosSeleccionados.length }} alumnos seleccionados:
            </p>

            <div class="space-y-2 max-h-96 overflow-y-auto mb-6">
              <div v-if="gruposDeAlumnos.length === 0" class="text-center py-8" :class="theme('cardSubtitle').value">
                <i class="fas fa-inbox text-4xl mb-2 opacity-50"></i>
                <p>No hay grupos disponibles</p>
              </div>
              <button
                v-for="grupo in gruposDeAlumnos"
                :key="grupo.id_grupo"
                @click="grupoDestino = grupo"
                class="w-full p-4 rounded-xl border-2 transition-all text-left"
                :class="[
                  grupoDestino?.id_grupo === grupo.id_grupo
                    ? 'border-purple-500 bg-white shadow-lg'
                    : (isDark ? 'border-gray-600 bg-gray-700/50 hover:bg-gray-700' : 'border-gray-300 bg-white hover:bg-gray-50')
                ]"
              >
                <div 
                  class="font-semibold"
                  :class="grupoDestino?.id_grupo === grupo.id_grupo ? 'text-purple-900' : (isDark ? 'text-gray-100' : 'text-gray-900')"
                >
                  {{ grupo.nivel || grupo.grado }}
                </div>
                <div 
                  class="text-sm"
                  :class="grupoDestino?.id_grupo === grupo.id_grupo ? 'text-purple-700' : (isDark ? 'text-gray-400' : 'text-gray-600')"
                >
                  {{ grupo.grado && grupo.nivel ? grupo.grado : (grupo.area?.nombre_area || 'Sin área') }}
                </div>
              </button>
            </div>

            <div class="flex gap-3">
              <button
                @click="asignarGrupoMasivo"
                :disabled="!grupoDestino"
                class="flex-1 px-6 py-3 rounded-xl font-semibold shadow-lg transition-all duration-200"
                :class="[
                  grupoDestino
                    ? theme('buttonPrimary').value + ' hover:scale-105'
                    : 'bg-gray-300 text-gray-500 cursor-not-allowed'
                ]"
              >
                <i class="fas fa-check mr-2"></i>
                Asignar
              </button>
              <button
                @click="mostrarModalGrupo = false"
                class="px-6 py-3 rounded-xl font-semibold shadow-lg hover:scale-105 transition-all duration-200"
                :class="theme('buttonSecondary').value"
              >
                Cancelar
              </button>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAuth } from '@/composables/useAuth'
import api from '@/axiosConfig'
import { useTheme } from '@/composables/useTheme'
import { useAlumnos } from '@/composables/useAlumnos' 
import { useGrupos } from '@/composables/useGrupos'
import GrupoCard from '@/modules/personal/components/GrupoCard.vue'
import TablaAlumnos from '../components/TablaAlumnos.vue'
import FormularioAlumnoModal from '../components/FormularioAlumnoModal.vue'

const { theme } = useTheme()
const { isAdmin, isSecretaria, isSupervisor } = useAuth()

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

// Estados para selección múltiple
const alumnosSeleccionados = ref([])
const mostrarModalGrupo = ref(false)
const grupoDestino = ref(null)

// Objeto para el card "Sin Asignar"
const grupoSinAsignar = {
  id_grupo: 'unassigned',
  grado: 'Sin Asignar',
  nivel: '',
  seccion: '',
  area: {
    nombre_area: 'Sin Grupo'
  }
}

// Objeto "Falso" para la tarjeta "Sin Asignar" eliminado

const gruposConArea = computed(() => {
  return grupos.value.map(g => ({
    ...g,
    area: areas.value.find(a => a.id_area === g.id_area)
  }))
})

const gruposDeAlumnos = computed(() => {
  return gruposConArea.value
    .filter(g => g.area && g.area.tipo_area === 'alumnado')
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
  return alumnos.value.filter(a => !a.id_grupo && a.tipo_persona === 'estudiante')
})

const alumnosDelGrupo = computed(() => {
  if (!grupoSeleccionado.value) return []
  
  // Si es el grupo "Sin Asignar", mostrar alumnos sin grupo
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

const asignarGrupoMasivo = async () => {
  if (!grupoDestino.value || alumnosSeleccionados.value.length === 0) return
  
  try {
    const response = await api.post('/personas/asignar-grupo-masivo', {
      ids_personas: alumnosSeleccionados.value,
      id_grupo: grupoDestino.value.id_grupo
    })
    
    alert(response.data.message || 'Alumnos asignados correctamente')
    
    // Limpiar selecciones y cerrar modal
    alumnosSeleccionados.value = []
    mostrarModalGrupo.value = false
    grupoDestino.value = null
    
    // Recargar datos
    await fetchAlumnos()
    
  } catch (error) {
    console.error('Error asignando alumnos:', error)
    alert(error.response?.data?.message || 'Error al asignar alumnos al grupo')
  }
}

const getNombreGrupo = (grupo) => {
  if (!grupo) return ''
  if (grupo.grado || grupo.nivel) {
    return `${grupo.nivel || ''} ${grupo.grado || ''} "${grupo.seccion || ''}"`.trim()
  }
  return grupo.area?.nombre_area || 'Grupo'
}
</script>