<template>
  <div class="p-6">
    <!-- Componente de cabecera que probablemente ya tienes -->
    <PageHeader 
      title="Gestión de Alumnos" 
      subtitle="Administración y registro de estudiantes del sistema (Datos Simulados)." 
    />

    <div class="mt-6 bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg">
      <div class="flex justify-end mb-4">
        <button 
          @click="openModal(null)"
          class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-150"
        >
          <i class="fas fa-plus mr-2"></i> Nuevo Alumno
        </button>
      </div>

      <!-- Indicadores de estado -->
      <div v-if="loading" class="text-center py-10 text-gray-500 dark:text-gray-400">
        <i class="fas fa-spinner fa-spin mr-2"></i> Cargando lista de alumnos...
      </div>
      <div v-else-if="error" class="bg-red-100 dark:bg-red-900 border border-red-400 text-red-700 dark:text-red-300 px-4 py-3 rounded relative">
        <strong class="font-bold">Error:</strong>
        <span class="block sm:inline"> {{ error }}</span>
      </div>
      <div v-else>
        <!-- Componente de tabla reutilizado -->
        <ResponsiveTable 
          :columns="tableColumns" 
          :data="alumnos"
          :title="'Alumnos Registrados'"
        >
          <template #actions="{ row }">
            <button @click="openModal(row)" class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-700 mr-3 transition duration-150" title="Editar">
              <i class="fas fa-edit"></i>
            </button>
            <button @click="confirmDelete(row.id_persona)" class="text-red-600 dark:text-red-400 hover:text-red-700 transition duration-150" title="Eliminar">
              <i class="fas fa-trash"></i>
            </button>
          </template>
        </ResponsiveTable>
      </div>
    </div>

    <!-- Modal para crear/editar -->
    <FormularioEmpleadoModal 
      :show="isModalOpen"
      :persona-data="currentAlumno"
      @close="closeModal"
      @saved="handleSaved"
      :is-alumno-mode="true"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { alumnoService } from '../api' 
import PageHeader from '../components/PageHeader.vue' 
import ResponsiveTable from '../components/ResponsiveTable.vue'
import FormularioEmpleadoModal from '../components/FormularioEmpleadoModal.vue'

const alumnos = ref([])
const loading = ref(true)
const error = ref(null)
const isModalOpen = ref(false)
const currentAlumno = ref(null)

const tableColumns = [
  { key: 'nombre_completo', label: 'Nombre' },
  { key: 'dni', label: 'DNI' },
  { key: 'cargo_puesto', label: 'Tipo' },
  // Las siguientes claves acceden a objetos anidados, que son simulados en alumnos.js
  { key: 'grupo.nombre_grupo', label: 'Grupo/Curso' }, 
  { key: 'horario.nombre', label: 'Horario' },
  { key: 'actions', label: 'Acciones', slot: true },
]

// --- Lógica de Carga de Datos (Simulada) ---
const fetchAlumnos = async () => {
  loading.value = true
  error.value = null
  try {
    // Llama a la función que devuelve el array MOCK_ALUMNOS de alumnos.js
    alumnos.value = await alumnoService.getAlumnos() 
  } catch (err) {
    error.value = 'Error de simulación al cargar alumnos.'
    console.error(err)
  } finally {
    loading.value = false
  }
}

// --- Lógica del Modal ---
const openModal = (alumno = null) => {
  // Cuando editamos, mandamos el objeto de alumno para pre-llenar el formulario
  currentAlumno.value = alumno ? { ...alumno } : null 
  isModalOpen.value = true
}

const closeModal = () => {
  isModalOpen.value = false
  currentAlumno.value = null
}

// Se llama después de guardar (crear o editar)
const handleSaved = () => {
  closeModal()
  fetchAlumnos() // Recargar la lista simulada
}

// --- Lógica de Eliminación (Simulada) ---
const confirmDelete = async (id) => {
  if (confirm('¿Estás seguro de que deseas eliminar este alumno? (Simulación)')) {
    try {
      // Usa la simulación del servicio de alumnos
      await alumnoService.deleteAlumno(id) 
      alert('Alumno eliminado (Simulado) con éxito.')
      await fetchAlumnos()
    } catch (err) {
      alert('Error de simulación al eliminar el alumno.')
      console.error(err)
    }
  }
}

onMounted(fetchAlumnos)
</script>