<template>
  <div class="p-4 sm:p-6">

    <div v-if="cargandoAreas || cargandoGrupos" class="text-center py-12" :class="theme('cardSubtitle').value">
      <i class="fas fa-spinner fa-spin text-3xl"></i>
      <p class="mt-2">Cargando datos...</p>
    </div>
    
    <div v-else>
      
      <TablaGrupos
        v-if="areaSeleccionada"
        :grupos="gruposDelArea"
        :nombreArea="areaSeleccionada.nombre_area"
        @volver="areaSeleccionada = null"
        @nuevoGrupo="abrirModalGrupo(null)"
        @editar="abrirModalGrupo"
        @eliminar="handleEliminarGrupo"
      />

      <div v-else>
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
          <h1 class="text-3xl font-bold" :class="theme('cardTitle').value">
            Gestión de Áreas
          </h1>
          <button
            @click="abrirModalArea(null)"
            class="px-5 py-2.5 rounded-xl font-semibold w-full md:w-auto"
            :class="theme('buttonPrimary').value"
          >
            <i class="fas fa-plus mr-2"></i>
            Nueva Área
          </button>
        </div>
        <div class="mb-6">
          <div class="relative">
            <input 
              v-model="busqueda"
              type="text" 
              placeholder="Buscar por nombre o descripción..."
              class="w-full md:w-1/2 rounded-xl pl-10 pr-4 py-3"
              :class="theme('input').value"
            />
            <span class="absolute left-3 top-1/2 -translate-y-1/2" :class="theme('cardSubtitle').value">
              <i class="fas fa-search"></i>
            </span>
          </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
          <AreaCard
            v-for="area in areasFiltradas"
            :key="area.id_area"
            :area="area"
            :cantidadGrupos="area.cantidadGrupos"
            @click="areaSeleccionada = area"
            @editar="abrirModalArea(area)" 
            @eliminar="handleEliminarArea(area.id_area)"
          />
        </div>
      </div>
    </div>

    <FormularioAreaModal
      v-if="modalAreaVisible"
      :area="areaEditando"
      @cerrar="cerrarModalArea"
      @actualizado="handleGuardadoArea"
    />
    <FormularioGrupoModal
      v-if="modalGrupoVisible"
      :grupo="grupoEditando"
      :area="areaSeleccionada" @cerrar="cerrarModalGrupo"
      @actualizado="handleGuardadoGrupo"
    />

  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useTheme } from '@/composables/useTheme'
import { useGrupos } from '@/composables/useGrupos'
import api from '@/axiosConfig'
import AreaCard from '../components/AreaCard.vue'
import TablaGrupos from '../components/TablaGrupos.vue'
import FormularioAreaModal from '../components/FormularioAreaModal.vue'
import FormularioGrupoModal from '../components/FormularioGrupoModal.vue'

const { theme } = useTheme()

// --- ESTADO PRINCIPAL ---
const areas = ref([])
const cargandoAreas = ref(true)
const busqueda = ref('')
const areaSeleccionada = ref(null)

// --- LÓGICA DE ÁREAS ---
const modalAreaVisible = ref(false)
const areaEditando = ref(null)

const obtenerAreas = async () => {
  cargandoAreas.value = true
  try {
    const res = await api.get('/areas')
    areas.value = res.data
  } catch (error) {
    console.error('Error cargando áreas:', error)
  } finally {
    cargandoAreas.value = false
  }
}
const abrirModalArea = (area = null) => {
  areaEditando.value = area
  modalAreaVisible.value = true
}
const cerrarModalArea = () => {
  modalAreaVisible.value = false
  areaEditando.value = null
}
const handleGuardadoArea = () => {
  cerrarModalArea()
  obtenerAreas() // Recargamos
}
const handleEliminarArea = async (id) => {
  if (!confirm('¿Seguro que deseas eliminar esta área? (Se eliminarán grupos y personas asociadas)')) return
  try {
    await api.delete(`/areas/${id}`)
    obtenerAreas()
  } catch (error) {
    console.error('Error eliminando área:', error)
  }
}

// --- LÓGICA DE GRUPOS ---
const { grupos, loading: cargandoGrupos, fetchGrupos, eliminarGrupo } = useGrupos()
const modalGrupoVisible = ref(false)
const grupoEditando = ref(null)

const gruposDelArea = computed(() => {
  if (!areaSeleccionada.value) return []
  return grupos.value.filter(g => g.id_area === areaSeleccionada.value.id_area)
})
const abrirModalGrupo = (grupo = null) => {
  grupoEditando.value = grupo
  modalGrupoVisible.value = true
}
const cerrarModalGrupo = () => {
  modalGrupoVisible.value = false
  grupoEditando.value = null
}
const handleGuardadoGrupo = () => {
  cerrarModalGrupo()
  fetchGrupos() // Recargamos
}
const handleEliminarGrupo = async (id) => {
  if (!confirm('¿Seguro que deseas eliminar este grupo?')) return
  await eliminarGrupo(id)
}

// --- COMPUTED PROPS ---
const areasFiltradas = computed(() => {
  const search = busqueda.value.toLowerCase()
  
  return areas.value
    .filter(area => 
      !search ||
      (area.nombre_area.toLowerCase().includes(search)) ||
      (area.descripcion && area.descripcion.toLowerCase().includes(search))
    )
    .map(area => {
      const count = grupos.value.filter(g => g.id_area === area.id_area).length
      return {
        ...area,
        cantidadGrupos: count 
      }
    })
})

// --- INICIALIZACIÓN ---
onMounted(() => {
  obtenerAreas()
  fetchGrupos() // Cargamos ambos al inicio
})
</script>