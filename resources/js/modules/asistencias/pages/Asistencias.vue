<template>
  <div class="flex-1 p-4 sm:p-6 overflow-x-hidden">
    <div v-if="loadingAsistencias || loadingGrupos || loadingAreas" class="text-center py-20" :class="theme('cardSubtitle').value">
      <i class="fas fa-spinner fa-spin text-4xl"></i>
      <p class="mt-3 text-lg">Cargando datos...</p>
    </div>

    <div v-else-if="errorAsistencias" class="text-red-400 text-center py-20">
      <i class="fas fa-exclamation-triangle text-4xl mb-3"></i>
      <p>{{ errorAsistencias }}</p>
    </div>

    <div v-else>
      <div class="flex items-center justify-between p-4 rounded-xl border mb-6" :class="theme('card').value">
        <button @click="navigateWeek('prev')" class="px-3 py-1.5 rounded-lg" :class="theme('buttonSecondary').value">
          <i class="fas fa-chevron-left"></i>
        </button>
        <span class="font-semibold" :class="theme('cardTitle').value">{{ weekRange.display }}</span>
        <button @click="navigateWeek('next')" class="px-3 py-1.5 rounded-lg" :class="theme('buttonSecondary').value">
          <i class="fas fa-chevron-right"></i>
        </button>
      </div>

      <div v-if="!grupoSeleccionado" class="space-y-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
          <div>
            <h1 class="text-3xl font-bold mb-2" :class="theme('cardTitle').value">Asistencia por Grupo</h1>
            <p class="text-sm" :class="theme('cardSubtitle').value">Selecciona un grupo para ver el reporte de asistencia semanal</p>
          </div>
        </div>

        <div class="relative w-full sm:max-w-md">
          <input v-model="busquedaGrupo" type="text" placeholder="Buscar por grupo o área..." class="w-full rounded-xl pl-10 pr-4 py-3 outline-none border transition-colors" :class="theme('input').value" />
          <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2" :class="theme('cardSubtitle').value"></i>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
          <GrupoCard
            v-for="grupo in gruposFiltrados"
            :key="grupo.id_grupo"
            :grupo="grupo"
            :cantidadPersonas="grupo.cantidadPersonasConAsistencia"
            @click="seleccionarGrupo(grupo)"
          />
        </div>
      </div>

      <TablaAsistencia
        v-else
        :asistencias="asistenciasDelGrupo"
        :nombreGrupo="getNombreGrupo(grupoSeleccionado)"
        @volver="grupoSeleccionado = null"
        @editar-asistencia="abrirModalEdicion"
      />
    </div>

    <FormularioAsistenciaModal
      v-if="modalEdicionVisible"
      :registro="registroEditando"
      @cerrar="cerrarModalEdicion"
      @actualizado="handleActualizacion"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '@/axiosConfig'
import { useTheme } from '@/composables/useTheme'
import { useAsistencias } from '@/composables/useAsistencias'
import { useGrupos } from '@/composables/useGrupos'
import GrupoCard from '@/modules/empleados/components/GrupoCard.vue'
import TablaAsistencia from '../components/TablaAsistencia.vue'
import FormularioAsistenciaModal from '../components/FormularioAsistenciaModal.vue'

const { theme } = useTheme()

// --- Lógica de Datos ---
const {
  asistencias,
  loading: loadingAsistencias,
  error: errorAsistencias,
  fetchAsistenciasSemana,
  weekRange,
  navigateWeek,
  refreshWeek
} = useAsistencias()

const {
  grupos,
  loading: loadingGrupos,
  fetchGrupos
} = useGrupos()

const areas = ref([])
const loadingAreas = ref(true)

// Modal
const modalEdicionVisible = ref(false)
const registroEditando = ref(null)

// Carga inicial
onMounted(async () => {
  loadingAreas.value = true
  try {
    await Promise.all([
      fetchAsistenciasSemana(), // carga semanal
      fetchGrupos(),
      api.get('/areas').then(res => areas.value = res.data)
    ])
  } catch (e) {
    console.error(e)
    errorAsistencias.value = 'No se pudieron cargar los datos.'
  } finally {
    loadingAreas.value = false
  }
})

// UI
const grupoSeleccionado = ref(null)
const busquedaGrupo = ref('')

const gruposConArea = computed(() => {
  return grupos.value.map(g => ({
    ...g,
    area: areas.value.find(a => a.id_area === g.id_area)
  }))
})

const gruposConAsistencia = computed(() => {
  return gruposConArea.value.map(g => ({
    ...g,
    cantidadPersonasConAsistencia: asistencias.value.filter(a => a.persona?.id_grupo === g.id_grupo).length
  }))
})

const gruposFiltrados = computed(() => {
  if (!busquedaGrupo.value) return gruposConAsistencia.value
  const search = busquedaGrupo.value.toLowerCase()
  return gruposConAsistencia.value.filter(g =>
    (g.area?.nombre_area || '').toLowerCase().includes(search) ||
    (g.nivel || '').toLowerCase().includes(search) ||
    (g.grado || '').toLowerCase().includes(search)
  )
})

const asistenciasDelGrupo = computed(() => {
  if (!grupoSeleccionado.value) return []
  // retornamos un array de resúmenes (lunes..viernes) para el grupo seleccionado
  return asistencias.value.filter(a => a.persona?.id_grupo === grupoSeleccionado.value.id_grupo)
})

const seleccionarGrupo = async (grupo) => {
  grupoSeleccionado.value = grupo;
  await fetchAsistenciasSemana({ group_id: grupo.id_grupo });
};

// Edición
const abrirModalEdicion = (data) => {
  // data = { asistencia, dia, estado }
  registroEditando.value = data
  modalEdicionVisible.value = true
}

const cerrarModalEdicion = () => {
  modalEdicionVisible.value = false
  registroEditando.value = null
}

const handleActualizacion = async () => {
  try {
    if (grupoSeleccionado.value) {
      await refreshWeek({ group_id: grupoSeleccionado.value.id_grupo });
    } else {
      await refreshWeek({ group_id: grupoSeleccionado.value.id_grupo });
    }
  } catch (e) {
    console.error('Error recargando semana después de actualizar:', e)
  } finally {
    cerrarModalEdicion()
  }
}

// Util
const getNombreGrupo = (grupo) => {
  if (!grupo) return ''
  if (grupo.grado || grupo.nivel) {
    return `${grupo.nivel || ''} ${grupo.grado || ''} "${grupo.seccion || ''}"`.trim()
  }
  return grupo.area?.nombre_area || 'Grupo'
}
</script>
