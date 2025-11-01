<template>
  <div class="flex-1 p-6 overflow-x-hidden">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
      <div>
        <h1 class="text-2xl sm:text-3xl font-bold mb-2" :class="theme('cardTitle').value">
          <i class="fas fa-clock mr-2"></i>
          Horarios
        </h1>
        <p class="text-sm" :class="theme('cardSubtitle').value">
          Gestiona horarios de entrada y salida por área
        </p>
      </div>
      
      <button 
        @click="mostrarModal = true"
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
      :horarios="horariosFiltrados"
      @editar="editarHorario"
      @eliminar="eliminarHorario"
    />

    <FormularioHorarioModal 
      v-if="mostrarModal"
      @close="mostrarModal = false"
      @save="handleSaveHorario"
    />

  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useTheme } from '../composables/useTheme'
import TablaHorarios from '../components/TablaHorarios.vue'
import FormularioHorarioModal from '../components/FormularioHorarioModal.vue' // <-- 1. Importar modal

const { theme } = useTheme()

// --- 2. Lógica del Modal ---
const mostrarModal = ref(false)

const handleSaveHorario = (nuevoHorario) => {
  console.log('Guardando nuevo horario:', nuevoHorario)
  
  // Lógica simulada para añadir
  const nuevaId = Math.max(...horarios.value.map(h => h.id)) + 1
  const nuevoColor = 'from-gray-400 to-gray-600' // Color por defecto para nuevos
  
  horarios.value.push({
    id: nuevaId,
    ...nuevoHorario,
    color: nuevoColor
  })
  
  mostrarModal.value = false // Cerrar el modal
}
// --- Fin Lógica del Modal ---

const busqueda = ref('')

const horarios = ref([
  { id: 1, area: 'Primaria', entrada: '07:30', salida: '13:30', turno: 'Mañana', color: 'from-blue-400 to-blue-600' },
  { id: 2, area: 'Secundaria', entrada: '07:30', salida: '14:00', turno: 'Mañana', color: 'from-green-400 to-emerald-600' },
  { id: 3, area: 'Administración', entrada: '08:00', salida: '16:00', turno: 'Completo', color: 'from-purple-400 to-purple-600' },
  { id: 4, area: 'Docentes', entrada: '07:00', salida: '13:00', turno: 'Mañana', color: 'from-pink-400 to-pink-600' },
  { id: 5, area: 'Inicial', entrada: '08:00', salida: '12:30', turno: 'Mañana', color: 'from-orange-400 to-orange-600' },
  { id: 6, area: 'Secundaria Tarde', entrada: '13:00', salida: '18:00', turno: 'Tarde', color: 'from-indigo-400 to-indigo-600' },
  { id: 7, area: 'Limpieza', entrada: '06:00', salida: '14:00', turno: 'Completo', color: 'from-cyan-400 to-cyan-600' },
  { id: 8, area: 'Seguridad', entrada: '18:00', salida: '06:00', turno: 'Noche', color: 'from-slate-400 to-slate-600' },
])

const horariosFiltrados = computed(() => {
  if (!busqueda.value) return horarios.value
  
  return horarios.value.filter(h => 
    h.area.toLowerCase().includes(busqueda.value.toLowerCase()) ||
    h.turno.toLowerCase().includes(busqueda.value.toLowerCase())
  )
})

// Funciones placeholder para editar/eliminar (puedes conectarlas a modales también)
const editarHorario = (horario) => {
  console.log('Editando horario:', horario)
  // Aquí podrías abrir un modal de *edición*
}

const eliminarHorario = (id) => {
  console.log('Eliminando horario con ID:', id)
  // Aquí podrías mostrar un modal de *confirmación*
}
</script>