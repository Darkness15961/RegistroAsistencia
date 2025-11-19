<template>
  <div 
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4"
    @click.self="$emit('close')"
  >
    <div 
      class="w-full max-w-xl rounded-3xl border shadow-2xl p-6" 
      :class="theme('card').value"
    >
      <h3 class="text-xl font-bold mb-6" :class="theme('cardTitle').value">
        {{ isEditing ? 'Editar Usuario' : 'Nuevo Usuario' }}
      </h3>

      <form @submit.prevent="submit" class="space-y-4">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">
              Email de Acceso
            </label>
            <input 
              v-model="form.email" 
              type="email"
              placeholder="usuario@dominio.com" 
              required
              class="w-full rounded-xl border px-3 py-2 outline-none transition-colors"
              :class="isDark ? 'bg-gray-800 border-gray-600 text-white placeholder-gray-500 focus:border-blue-500' : 'bg-white border-gray-200 text-gray-900 focus:border-blue-500'"
            />
          </div>
          
          <div class="relative">
            <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">
              Vincular a Persona
            </label>
            
            <div class="relative">
              <input 
                v-model="busquedaPersona" 
                type="text"
                placeholder="Buscar por nombre..." 
                @input="buscarPersonas"
                @focus="mostrarResultados = true"
                class="w-full rounded-xl border px-3 py-2 pr-8 outline-none transition-colors"
                :class="[
                  isDark ? 'bg-gray-800 border-gray-600 text-white placeholder-gray-500 focus:border-blue-500' : 'bg-white border-gray-200 text-gray-900 focus:border-blue-500',
                  personaSeleccionada ? 'border-green-500 focus:border-green-500' : ''
                ]"
              />
              
              <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                <i v-if="personaSeleccionada" class="fas fa-check text-green-500"></i>
                <i v-else class="fas fa-search" :class="theme('cardSubtitle').value"></i>
              </div>
            </div>

            <div v-if="mostrarResultados && resultadosPersonas.length > 0" 
                 class="absolute z-10 w-full mt-1 rounded-xl shadow-lg max-h-60 overflow-auto border"
                 :class="isDark ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200'">
              
              <div 
                v-for="persona in resultadosPersonas" 
                :key="persona.id_persona"
                @click="seleccionarPersona(persona)"
                class="px-4 py-2 cursor-pointer transition-colors flex justify-between items-center"
                :class="isDark ? 'hover:bg-gray-700 text-gray-200' : 'hover:bg-blue-50 text-gray-800'"
              >
                <span class="font-medium">{{ persona.nombre_completo }}</span>
                <span class="text-xs opacity-70">{{ persona.cargo_grado }}</span>
              </div>
            </div>
            
            <div v-if="mostrarResultados && busquedaPersona && resultadosPersonas.length === 0 && !personaSeleccionada" 
                 class="absolute z-10 w-full mt-1 p-2 text-sm text-center rounded-xl border"
                 :class="isDark ? 'bg-gray-800 border-gray-700 text-gray-400' : 'bg-white border-gray-200 text-gray-500'">
              No se encontraron personas.
            </div>
            
            <input type="hidden" v-model="form.id_persona">
          </div>

          <div>
            <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">
              Rol del Usuario
            </label>
            <div class="relative">
              <select 
                v-model="form.rol" 
                class="w-full rounded-xl appearance-none border px-3 py-2 pr-8 outline-none transition-colors"
                :class="isDark ? 'bg-gray-800 border-gray-600 text-white focus:border-blue-500' : 'bg-white border-gray-200 text-gray-900 focus:border-blue-500'"
              >
                <option value="administrador" :class="isDark ? 'bg-gray-800' : 'bg-white'">Administrador</option>
                <option value="empleado" :class="isDark ? 'bg-gray-800' : 'bg-white'">Empleado</option>
                <option value="docente" :class="isDark ? 'bg-gray-800' : 'bg-white'">Docente</option>
              </select>
              <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none" 
                   :class="isDark ? 'text-white' : 'text-gray-600'">
                <i class="fas fa-chevron-down text-xs"></i>
              </div>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">
              Estado
            </label>
            <div class="relative">
              <select 
                v-model="form.estado" 
                class="w-full rounded-xl appearance-none border px-3 py-2 pr-8 outline-none transition-colors"
                :class="isDark ? 'bg-gray-800 border-gray-600 text-white focus:border-blue-500' : 'bg-white border-gray-200 text-gray-900 focus:border-blue-500'"
              >
                <option value="activo" :class="isDark ? 'bg-gray-800' : 'bg-white'">Activo</option>
                <option value="inactivo" :class="isDark ? 'bg-gray-800' : 'bg-white'">Inactivo</option>
              </select>
              <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none" 
                   :class="isDark ? 'text-white' : 'text-gray-600'">
                <i class="fas fa-chevron-down text-xs"></i>
              </div>
            </div>
          </div>
        </div>

        <div v-if="!isEditing" class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">
              Contraseña
            </label>
            <div class="relative">
              <input 
                v-model="form.password" 
                :type="showPassword ? 'text' : 'password'" 
                placeholder="••••••••" 
                required
                class="w-full rounded-xl border px-3 py-2 pr-10 outline-none transition-colors"
                :class="isDark ? 'bg-gray-800 border-gray-600 text-white placeholder-gray-500 focus:border-blue-500' : 'bg-white border-gray-200 text-gray-900 focus:border-blue-500'"
              />
              <button 
                type="button" 
                @click="showPassword = !showPassword"
                class="absolute inset-y-0 right-0 px-3 flex items-center text-sm"
                :class="theme('cardSubtitle').value"
              >
                <i class="fas" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
              </button>
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">
              Confirmar Contraseña
            </label>
            <div class="relative">
              <input 
                v-model="form.password_confirmation" 
                :type="showConfirmPassword ? 'text' : 'password'" 
                placeholder="••••••••" 
                required
                class="w-full rounded-xl border px-3 py-2 pr-10 outline-none transition-colors"
                :class="isDark ? 'bg-gray-800 border-gray-600 text-white placeholder-gray-500 focus:border-blue-500' : 'bg-white border-gray-200 text-gray-900 focus:border-blue-500'"
              />
              <button 
                type="button" 
                @click="showConfirmPassword = !showConfirmPassword"
                class="absolute inset-y-0 right-0 px-3 flex items-center text-sm"
                :class="theme('cardSubtitle').value"
              >
                <i class="fas" :class="showConfirmPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
              </button>
            </div>
          </div>
        </div>

        <div class="flex gap-3 justify-end pt-4">
          <button 
            type="button" 
            @click="$emit('close')" 
            class="px-5 py-2 rounded-xl font-semibold"
            :class="theme('buttonSecondary').value"
          >
            Cancelar
          </button>
          <button 
            type="submit" 
            :disabled="loading"
            class="px-5 py-2 rounded-xl font-semibold flex items-center gap-2"
            :class="[theme('buttonPrimary').value, loading && 'opacity-50']"
          >
            <i v-if="loading" class="fas fa-spinner animate-spin"></i>
            {{ isEditing ? 'Guardar Cambios' : 'Crear Usuario' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { reactive, computed, watch, ref, onMounted } from 'vue'
import { useTheme } from '@/composables/useTheme'
import api from '@/axiosConfig'

const props = defineProps({
  usuario: { type: [Object, null], default: null }
})
const emit = defineEmits(['close', 'saved'])

const { theme, isDark } = useTheme()
const loading = ref(false)
const showPassword = ref(false)
const showConfirmPassword = ref(false)

// --- Lógica de Búsqueda de Personas ---
const busquedaPersona = ref('')
const resultadosPersonas = ref([])
const mostrarResultados = ref(false)
const personaSeleccionada = ref(null)

// Cargar lista completa de personas (o usar un endpoint de búsqueda)
// Nota: Si son muchos usuarios, mejor haz un endpoint de búsqueda en el backend.
// Aquí asumimos que podemos cargar todos o los primeros 20.
let todasLasPersonas = [] 

onMounted(async () => {
  try {
    // Cargamos personas para el autocompletado
    const res = await api.get('/personas') //
    todasLasPersonas = res.data.data || res.data
  } catch (e) {
    console.error("Error cargando personas:", e)
  }
})

const buscarPersonas = () => {
  if (!busquedaPersona.value) {
    resultadosPersonas.value = []
    mostrarResultados.value = false
    // Si borra el texto, limpiamos la selección
    if (personaSeleccionada.value) {
        personaSeleccionada.value = null
        form.id_persona = null
    }
    return
  }
  
  // Filtro simple en cliente
  const term = busquedaPersona.value.toLowerCase()
  resultadosPersonas.value = todasLasPersonas.filter(p => 
    (p.nombre_completo && p.nombre_completo.toLowerCase().includes(term)) ||
    (p.dni && p.dni.includes(term))
  ).slice(0, 5) // Limitamos a 5 resultados
  
  mostrarResultados.value = true
}

const seleccionarPersona = (persona) => {
  busquedaPersona.value = persona.nombre_completo
  form.id_persona = persona.id_persona
  personaSeleccionada.value = persona
  mostrarResultados.value = false
}

// ---------------------------------------

const getInitialForm = () => ({
  id_persona: props.usuario?.id_persona || null,
  email: props.usuario?.email || '',
  rol: props.usuario?.rol || 'empleado',
  estado: props.usuario?.estado || 'activo',
  password: '',
  password_confirmation: ''
})

const form = reactive(getInitialForm())
const isEditing = computed(() => !!props.usuario)

watch(() => props.usuario, (nuevo) => {
  Object.assign(form, getInitialForm())
  // Si estamos editando, intentamos pre-llenar el nombre de la persona
  if (nuevo && nuevo.persona) {
     busquedaPersona.value = nuevo.persona.nombre_completo
     personaSeleccionada.value = nuevo.persona
  } else {
     busquedaPersona.value = ''
     personaSeleccionada.value = null
  }
}, { immediate: true })

const submit = async () => {
  loading.value = true
  try {
    const payload = {
      email: form.email,
      rol: form.rol,
      estado: form.estado
    }
    if (form.id_persona) {
      payload.id_persona = form.id_persona
    }
    
    if (isEditing.value) {
      await api.put(`/usuarios/${props.usuario.id_usuario}`, payload)
    } else {
      payload.password = form.password
      payload.password_confirmation = form.password_confirmation
      await api.post('/usuarios', payload)
    }
    emit('saved')
    emit('close')
  } catch (err) {
    console.error('Error en modal usuario:', err)
    // Manejo de errores de validación
    const msg = err.response?.data?.message || err.response?.data?.error || 'Error al guardar.'
    alert(msg)
  } finally {
    loading.value = false
  }
}
</script>