<template>
  <div class="flex-1 p-4 sm:p-6 overflow-x-hidden">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
      <div>
        <h1 class="text-2xl sm:text-3xl font-bold mb-2" :class="theme('cardTitle').value">
          <i class="fas fa-clock mr-2"></i>
          Gestión de Horarios
        </h1>
        <p class="text-sm" :class="theme('cardSubtitle').value">
          Gestiona horarios de entrada y salida por área
        </p>
      </div>
      
      <button 
        @click="abrirModal()"
        class="flex items-center gap-2 px-6 py-3 rounded-xl font-semibold shadow-lg hover:scale-105 transition-all duration-200 whitespace-nowrap"
        :class="theme('buttonPrimary').value"
      >
        <i class="fas fa-plus"></i>
        Nuevo Horario
      </button>
    </div>

    <div class="relative w-full sm:max-w-md mb-6">
      <input
        v-model="busqueda"
        type="text"
        placeholder="Buscar por área o turno..."
        class="w-full rounded-xl pl-10 pr-4 py-3 outline-none border transition-colors"
        :class="theme('input').value"
      />
      <i 
        class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2"
        :class="theme('cardSubtitle').value"
      ></i>
    </div>

    <TablaHorarios 
      v-if="!cargando"
      :horarios="horariosFiltrados"
      @editar="abrirModal"
      @eliminar="eliminarHorario"
    />
    
    <div v-if="cargando" class="text-center py-12" :class="theme('cardSubtitle').value">
      <i class="fas fa-spinner fa-spin text-3xl"></i>
      <p class="mt-2">Cargando horarios...</p>
    </div>

    <FormularioHorarioModal 
      v-if="modalVisible"
      :horario="horarioEditando"
      @cerrar="cerrarModal"
    />

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useTheme } from '@/composables/useTheme'
import api from '@/axiosConfig'
import TablaHorarios from '../components/TablaHorarios.vue'
// ✅ ¡IMPORTACIÓN CORREGIDA!
import FormularioHorarioModal from '../components/FormularioHorarioModal.vue'

const { theme } = useTheme()

// --- Lógica de Datos ---
const horarios = ref([])
const cargando = ref(true)
const busqueda = ref('')

const obtenerHorarios = async () => {
  cargando.value = true
  try {
    const res = await api.get('/horarios')
    horarios.value = res.data
  } catch (error) {
    console.error('Error cargando horarios:', error)
  } finally {
    cargando.value = false
  }
}

const eliminarHorario = async (id) => {
  if (!confirm('¿Seguro que deseas eliminar este horario?')) return
  try {
    await api.delete(`/horarios/${id}`)
    obtenerHorarios()
  } catch (error) {
    console.error('Error eliminando horario:', error)
  }
}

onMounted(obtenerHorarios)

// --- Lógica de Modal ---
const modalVisible = ref(false)
const horarioEditando = ref(null)

const abrirModal = (horario = null) => {
  horarioEditando.value = horario
  modalVisible.value = true
}

const cerrarModal = () => {
  modalVisible.value = false
  horarioEditando.value = null
  obtenerHorarios()
}

// --- Lógica de Filtro ---
const horariosFiltrados = computed(() => {
  if (!busqueda.value) return horarios.value
  
  const search = busqueda.value.toLowerCase()
  return horarios.value.filter(h => 
    (h.area?.nombre_area.toLowerCase().includes(search)) ||
    (h.turno.toLowerCase().includes(search))
  )
})
</script>