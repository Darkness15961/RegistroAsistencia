<template>
  <div class="flex-1 p-4 sm:p-6 overflow-x-hidden">

    <div v-if="loadingEmpleados || loadingGrupos || loadingAreas" class="text-center py-20" :class="theme('cardSubtitle').value">
      <i class="fas fa-spinner fa-spin text-4xl"></i>
      <p class="mt-3 text-lg">Cargando datos...</p>
    </div>

    <div v-else-if="errorEmpleados" class="text-red-400 text-center py-20">
      <i class="fas fa-exclamation-triangle text-4xl mb-3"></i>
      <p>{{ errorEmpleados }}</p>
    </div>

    <div v-else>
      <div v-if="!grupoSeleccionado" class="space-y-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
          <div>
            <h1 class="text-3xl font-bold mb-2" :class="theme('cardTitle').value">
              Empleados por Grupo
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
            Nuevo Empleado
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
            v-if="empleadosSinGrupo.length > 0"
            :grupo="grupoSinAsignar"
            :cantidadPersonas="empleadosSinGrupo.length"
            @click="seleccionarGrupo(grupoSinAsignar)"
          />
          
          <GrupoCard
            v-for="grupo in gruposDeEmpleadosFiltrados"
            :key="grupo.id_grupo"
            :grupo="grupo"
            :cantidadPersonas="grupo.cantidadPersonas"
            @click="seleccionarGrupo(grupo)"
          />
        </div>

        <p v-if="!gruposDeEmpleadosFiltrados.length && !empleadosSinGrupo.length" class="text-center py-12 text-lg" :class="theme('cardSubtitle').value">
          No se encontraron grupos ni empleados sin asignar.
        </p>

      </div>
      
      <TablaEmpleados
        v-else
        :empleados="empleadosDelGrupo"
        :nombreGrupo="getNombreGrupo(grupoSeleccionado)"
        @volver="grupoSeleccionado = null" 
        @nuevoEmpleado="abrirModalNuevo"
        @editar="abrirModalEditar"
        @eliminar="handleEliminarEmpleado"
      />
    </div>

    <FormularioEmpleadoModal
      v-if="mostrarModal"
      :empleado="empleadoSeleccionado"
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
import { useGrupos } from '@/composables/useGrupos'
import GrupoCard from '../components/GrupoCard.vue'
import TablaEmpleados from '../components/TablaEmpleados.vue'
import FormularioEmpleadoModal from '../components/FormularioEmpleadoModal.vue'

const { theme } = useTheme()

// --- Lógica de Datos ---
const { 
  empleados, 
  loading: loadingEmpleados, 
  error: errorEmpleados, 
  fetchEmpleados, 
  eliminarEmpleado 
} = useEmpleados()
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
    fetchEmpleados(),
    fetchGrupos(),
    api.get('/areas').then(res => areas.value = res.data)
  ]).catch(e => {
    console.error(e)
    errorEmpleados.value = "No se pudieron cargar los datos."
  })
  loadingAreas.value = false
})

// --- Lógica de UI ---
const mostrarModal = ref(false)
const empleadoSeleccionado = ref(null)
const grupoSeleccionado = ref(null)
const busquedaGrupo = ref('')

// ✅ Objeto "Falso" para la tarjeta "Sin Asignar"
const grupoSinAsignar = {
  id_grupo: 'unassigned', // ID especial
  nivel: 'Empleados',
  grado: 'Sin Asignar',
  seccion: '',
  area: {
    nombre_area: 'Sin Grupo' // Esto usará el icono y color por defecto
  }
}

const gruposConArea = computed(() => {
  return grupos.value.map(g => ({
    ...g,
    area: areas.value.find(a => a.id_area === g.id_area)
  }))
})

const gruposDeEmpleados = computed(() => {
  return gruposConArea.value
    .filter(g => g.area && !g.area.nombre_area.toLowerCase().includes('alumno'))
    .map(g => ({
      ...g,
      cantidadPersonas: empleados.value.filter(e => e.id_grupo === g.id_grupo).length
    }))
})

const gruposDeEmpleadosFiltrados = computed(() => {
  if (!busquedaGrupo.value) {
    return gruposDeEmpleados.value
  }
  const search = busquedaGrupo.value.toLowerCase()
  return gruposDeEmpleados.value.filter(g => 
    (g.area?.nombre_area.toLowerCase().includes(search)) ||
    (g.nivel && g.nivel.toLowerCase().includes(search)) ||
    (g.grado && g.grado.toLowerCase().includes(search))
  )
})

// ✅ Computed para empleados con id_grupo == null
const empleadosSinGrupo = computed(() => {
  return empleados.value.filter(e => !e.id_grupo)
})

// ✅ Computed actualizado para manejar el grupo "unassigned"
const empleadosDelGrupo = computed(() => {
  if (!grupoSeleccionado.value) return []
  // Si se selecciona la tarjeta especial
  if (grupoSeleccionado.value.id_grupo === 'unassigned') {
    return empleadosSinGrupo.value
  }
  // Lógica normal
  return empleados.value.filter(e => e.id_grupo === grupoSeleccionado.value.id_grupo)
})

const seleccionarGrupo = (grupo) => {
  grupoSeleccionado.value = grupo
}

// --- Lógica de Modal ---
const abrirModalNuevo = () => {
  empleadoSeleccionado.value = null
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
  fetchEmpleados() 
}
const handleEliminarEmpleado = async (id) => {
  if (!confirm('¿Seguro que deseas eliminar este empleado?')) return
  await eliminarEmpleado(id)
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