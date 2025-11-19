<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4"
       @click.self="$emit('cerrar')">
    <div class="rounded-3xl w-full max-w-lg shadow-2xl border p-6" :class="theme('card').value">
      
      <h2 class="text-xl font-bold mb-6" :class="theme('cardTitle').value">
        <i class="fas fa-layer-group mr-2"></i>
        {{ grupo ? 'Editar Grupo' : 'Nuevo Grupo' }}
      </h2>

      <form @submit.prevent="guardarGrupo">
        <div class="space-y-5">
          
          <div>
            <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">
              Área de Pertenencia
            </label>
            <div class="relative">
              <select 
                v-model="form.id_area"
                class="w-full rounded-xl appearance-none border px-3 py-2 pr-8 outline-none transition-colors"
                :class="isDark ? 'bg-gray-800 border-gray-600 text-white focus:border-blue-500' : 'bg-white border-gray-200 text-gray-900 focus:border-blue-500'"
                required
              >
                <option value="" disabled>Seleccionar área</option>
                <option v-for="area in areas" :key="area.id_area" :value="area.id_area">
                  {{ area.nombre_area }}
                </option>
              </select>
              <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none" :class="isDark ? 'text-white' : 'text-gray-600'">
                <i class="fas fa-chevron-down text-xs"></i>
              </div>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">
              Tipo de Gestión
            </label>
            <div class="relative">
              <select 
                v-model="tipoGestion"
                @change="onTipoGestionChange"
                class="w-full rounded-xl appearance-none border px-3 py-2 pr-8 outline-none transition-colors"
                :class="isDark ? 'bg-gray-800 border-gray-600 text-white focus:border-blue-500' : 'bg-white border-gray-200 text-gray-900 focus:border-blue-500'"
                required
              >
                <option value="" disabled>Seleccione el tipo de grupo</option>
                <option value="alumnos">Gestión de Alumnado</option>
                <option value="docentes">Gestión de Docentes</option>
                <option value="administrativos">Administrativos</option>
                <option value="otros">Otros</option>
              </select>
              <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none" :class="isDark ? 'text-white' : 'text-gray-600'">
                <i class="fas fa-list-ul text-xs"></i>
              </div>
            </div>
          </div>

          <div v-if="tipoGestion" class="animate-fade-in">
            <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">
              Nombre del Grupo / Nivel
            </label>
            <input 
              v-model="form.nivel" 
              :placeholder="placeholderNivel"
              class="w-full rounded-xl border px-3 py-2 outline-none transition-colors"
              :class="isDark ? 'bg-gray-800 border-gray-600 text-white placeholder-gray-500 focus:border-blue-500' : 'bg-white border-gray-200 text-gray-900 focus:border-blue-500'"
              required
            />
          </div>

          <div v-if="tipoGestion === 'alumnos'" class="grid grid-cols-2 gap-4 animate-fade-in">
            <div>
              <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">
                Grado
              </label>
              <input 
                v-model="form.grado" 
                placeholder="Ej: 5to"
                class="w-full rounded-xl border px-3 py-2 outline-none transition-colors"
                :class="isDark ? 'bg-gray-800 border-gray-600 text-white focus:border-blue-500' : 'bg-white border-gray-200 text-gray-900 focus:border-blue-500'"
                required
              />
            </div>
            <div>
              <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">
                Sección
              </label>
              <input 
                v-model="form.seccion" 
                placeholder="Ej: A"
                class="w-full rounded-xl border px-3 py-2 outline-none transition-colors"
                :class="isDark ? 'bg-gray-800 border-gray-600 text-white focus:border-blue-500' : 'bg-white border-gray-200 text-gray-900 focus:border-blue-500'"
                required
              />
            </div>
          </div>

          <div class="relative md:col-span-2">
            <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">
              {{ tipoGestion === 'alumnos' ? 'Tutor de Aula' : 'Encargado del Grupo' }} (Opcional)
            </label>
            
            <div class="relative">
              <input 
                v-model="busquedaTutor" 
                type="text"
                :placeholder="tipoGestion === 'alumnos' ? 'Buscar docente tutor...' : 'Buscar encargado...'"
                @input="buscarTutores"
                @focus="mostrarResultados = true"
                class="w-full rounded-xl border px-3 py-2 pr-10 outline-none transition-colors"
                :class="[
                  isDark ? 'bg-gray-800 border-gray-600 text-white placeholder-gray-500 focus:border-blue-500' : 'bg-white border-gray-200 text-gray-900 focus:border-blue-500',
                  tutorSeleccionado ? 'border-green-500 focus:border-green-500' : ''
                ]"
              />
              <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                 <i v-if="tutorSeleccionado" class="fas fa-check text-green-500"></i>
                 <i v-else class="fas fa-search opacity-50" :class="isDark ? 'text-white' : 'text-gray-600'"></i>
              </div>
            </div>
            
            <div v-if="mostrarResultados && resultadosTutores.length > 0" 
                 class="absolute z-20 w-full mt-1 rounded-xl shadow-xl max-h-48 overflow-auto border"
                 :class="isDark ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200'">
              <div 
                v-for="tutor in resultadosTutores" 
                :key="tutor.id_persona"
                @mousedown.prevent="seleccionarTutor(tutor)"
                class="px-4 py-2.5 cursor-pointer transition-colors flex justify-between items-center border-b last:border-0"
                :class="isDark ? 'border-white/5 hover:bg-gray-700 text-gray-200' : 'border-gray-100 hover:bg-blue-50 text-gray-800'"
              >
                <div>
                  <span class="font-medium block">{{ tutor.nombre_completo }}</span>
                  <span class="text-xs opacity-70">{{ tutor.cargo_grado || 'Sin cargo' }}</span>
                </div>
                <span v-if="tutor.dni" class="text-xs font-mono bg-black/10 dark:bg-white/10 px-1.5 py-0.5 rounded">
                  {{ tutor.dni }}
                </span>
              </div>
            </div>

            <div v-if="mostrarResultados && busquedaTutor && resultadosTutores.length === 0 && !tutorSeleccionado" 
                 class="absolute z-20 w-full mt-1 p-3 text-sm text-center rounded-xl border"
                 :class="isDark ? 'bg-gray-800 border-gray-700 text-gray-400' : 'bg-white border-gray-200 text-gray-500'">
              No se encontraron personas.
            </div>

            <input type="hidden" v-model="form.id_tutor">
          </div>

        </div>

        <div class="flex justify-end gap-3 pt-8">
          <button type="button" @click="$emit('cerrar')" class="px-5 py-2.5 rounded-xl font-semibold" :class="theme('buttonSecondary').value">
            Cancelar
          </button>
          <button type="submit" :disabled="loading" class="px-5 py-2.5 rounded-xl font-semibold shadow-lg" :class="[theme('buttonPrimary').value, loading && 'opacity-50']">
            <i v-if="loading" class="fas fa-spinner animate-spin mr-2"></i>
            {{ grupo ? 'Actualizar' : 'Guardar' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, watch, computed } from 'vue'
import { useTheme } from '@/composables/useTheme'
import api from '@/axiosConfig'

const props = defineProps({
  grupo: { type: [Object, null], default: null }
})
const emit = defineEmits(['cerrar', 'actualizado'])
const { theme, isDark } = useTheme()
const loading = ref(false)
const areas = ref([])
let todasLasPersonas = []

// --- Estado UI ---
const tipoGestion = ref('') // 'alumnos', 'docentes', 'administrativos', 'otros'

// --- Estado Formulario ---
const form = reactive({
  id_area: '',
  nivel: '',   // Se usará para "Nombre del Grupo"
  grado: '',   // Se usará para "Grado" (solo alumnos)
  seccion: '', // Se usará para "Sección" (solo alumnos)
  id_tutor: null
})

// --- Buscador ---
const busquedaTutor = ref('')
const resultadosTutores = ref([])
const mostrarResultados = ref(false)
const tutorSeleccionado = ref(null)

// --- Helpers Visuales ---
const placeholderNivel = computed(() => {
  if (tipoGestion.value === 'alumnos') return 'Ej: Primaria / Secundaria'
  if (tipoGestion.value === 'docentes') return 'Ej: Plana Docente de Ciencias'
  if (tipoGestion.value === 'administrativos') return 'Ej: Equipo de Administración'
  return 'Nombre del Grupo'
})

// --- Funciones Helpers (Definidas antes de usarse) ---
const resetForm = () => {
  form.id_area = ''
  form.nivel = ''
  form.grado = ''
  form.seccion = ''
  form.id_tutor = null
  tipoGestion.value = ''
  busquedaTutor.value = ''
  tutorSeleccionado.value = null
}

const seleccionarTutor = (tutor) => {
  tutorSeleccionado.value = tutor
  busquedaTutor.value = tutor.nombre_completo
  form.id_tutor = tutor.id_persona
  mostrarResultados.value = false
}

const buscarTutores = () => {
  if (!busquedaTutor.value) {
    resultadosTutores.value = []
    tutorSeleccionado.value = null
    form.id_tutor = null
    mostrarResultados.value = false
    return
  }
  
  const term = busquedaTutor.value.toLowerCase()
  resultadosTutores.value = todasLasPersonas.filter(p => 
    (p.nombre_completo && p.nombre_completo.toLowerCase().includes(term)) ||
    (p.dni && p.dni.includes(term)) ||
    (p.cargo_grado && p.cargo_grado.toLowerCase().includes(term))
  ).slice(0, 7)
  mostrarResultados.value = resultadosTutores.value.length > 0
}

// Limpia campos si cambiamos de 'alumnos' a 'docentes' por error
const onTipoGestionChange = () => {
  if (tipoGestion.value !== 'alumnos') {
    form.grado = ''
    form.seccion = ''
  }
}

// --- Carga Inicial ---
onMounted(async () => {
  try {
    const [resAreas, resPersonas] = await Promise.all([
        api.get('/areas'),
        api.get('/personas') 
    ])
    areas.value = resAreas.data
    todasLasPersonas = resPersonas.data.data || resPersonas.data
  } catch (e) {
    console.error("Error cargando datos base:", e)
  }
})


// --- Watcher Principal ---
watch(() => props.grupo, (nuevo) => {
  if (nuevo) {
    form.id_area = nuevo.id_area || ''
    form.nivel = nuevo.nivel || ''
    form.grado = nuevo.grado || ''
    form.seccion = nuevo.seccion || ''
    form.id_tutor = nuevo.id_tutor || null

    // Lógica inteligente para detectar el tipo de gestión al editar
    if (nuevo.grado || nuevo.seccion) {
        // Si tiene grado o sección, es Alumno
        tipoGestion.value = 'alumnos'
    } else {
        // Si no tiene, asumimos que es personal (Docente/Admin)
        // Podríamos afinar esto si tuviéramos un campo específico, 
        // pero 'docentes' es un buen default para editar si no es alumno.
        tipoGestion.value = 'docentes'
    }

    if (nuevo.id_tutor && todasLasPersonas.length > 0) {
       const tutor = todasLasPersonas.find(p => p.id_persona === nuevo.id_tutor)
       if (tutor) seleccionarTutor(tutor)
    } else if (nuevo.tutor) { 
       seleccionarTutor(nuevo.tutor)
    }
  } else {
    resetForm()
  }
}, { immediate: true })


// --- Guardado ---
const guardarGrupo = async () => {
  loading.value = true
  
  // Limpieza final de datos antes de enviar
  if (tipoGestion.value !== 'alumnos') {
      form.grado = null // Para personal, estos campos van nulos a la BD
      form.seccion = null
  }
  if (!form.id_tutor) form.id_tutor = null

  try {
    if (props.grupo) {
      await api.put(`/grupos/${props.grupo.id_grupo}`, form)
    } else {
      await api.post('/grupos', form)
    }
    emit('actualizado')
    emit('cerrar')
  } catch (err) {
    console.error('Error guardando grupo:', err)
    const msg = err.response?.data?.message || 'Error al guardar el grupo.'
    const errorData = err.response?.data
    if (errorData && errorData.errors) {
       const primerError = Object.values(errorData.errors).map(e => e[0]).join(', ')
       alert(`Error: ${primerError}`)
    } else {
       alert(msg)
    }
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-5px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
  animation: fadeIn 0.3s ease-out forwards;
}
</style>