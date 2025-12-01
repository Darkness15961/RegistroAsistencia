<template>
  <div 
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4"
    @click.self="$emit('close')"
  >
    <div 
      class="w-full max-w-2xl rounded-3xl border shadow-2xl px-8 sm:px-10 py-6 sm:py-8" 
      :class="theme('card').value"
    >
      <h2 class="text-xl font-bold mb-6 text-left" :class="theme('cardTitle').value">
        {{ isEditing ? 'Editar Usuario' : 'Nuevo Usuario' }}
      </h2>

      <form @submit.prevent="submit" class="space-y-5">
        
        <!-- Row 1: Email y Persona -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
          <!-- Email -->
          <div>
            <label class="block text-sm font-medium mb-2" :class="theme('cardSubtitle').value">
              Email de Acceso
            </label>
            <input 
              type="email" 
              v-model="form.email"
              required
              class="w-full rounded-xl border px-4 py-2.5 outline-none transition-colors"
              :class="isDark ? 'bg-gray-800 border-gray-600 text-white focus:border-blue-500' : 'bg-white border-gray-200 text-gray-900 focus:border-blue-500'"
              placeholder="usuario@dominio.com"
            />
          </div>

          <!-- Buscador de Personas -->
          <div>
            <label class="block text-sm font-medium mb-2" :class="theme('cardSubtitle').value">
              Vincular a Persona
            </label>
            <div class="relative">
              <input 
                type="text" 
                v-model="busquedaPersona"
                @input="buscarPersonas"
                class="w-full rounded-xl border px-4 py-2.5 pr-10 outline-none transition-colors"
                :class="[
                  isDark ? 'bg-gray-800 border-gray-600 text-white placeholder-gray-500 focus:border-blue-500' : 'bg-white border-gray-200 text-gray-900 focus:border-blue-500',
                  isEditing ? 'opacity-75 cursor-not-allowed bg-gray-100 dark:bg-gray-700' : ''
                ]"
                placeholder="Buscar por nombre..."
                :disabled="isEditing" 
              />
              <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none" :class="isDark ? 'text-white' : 'text-gray-600'">
                 <i class="fas fa-search opacity-70"></i>
              </div>

              <!-- Resultados de búsqueda -->
              <div 
                v-if="mostrarResultados && resultadosPersonas.length > 0"
                class="absolute z-20 w-full mt-1 rounded-xl shadow-xl max-h-48 overflow-auto border"
                :class="isDark ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200'"
              >
                <div 
                  v-for="persona in resultadosPersonas" 
                  :key="persona.id_persona"
                  @mousedown.prevent="seleccionarPersona(persona)"
                  class="px-4 py-2.5 cursor-pointer transition-colors flex justify-between items-center border-b last:border-0"
                  :class="isDark ? 'border-white/5 hover:bg-gray-700 text-gray-200' : 'border-gray-100 hover:bg-blue-50 text-gray-800'"
                >
                  <div>
                    <span class="font-medium block">{{ persona.nombre_completo }}</span>
                    <span class="text-xs opacity-70">{{ persona.dni }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Row 2: Rol y Estado -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
          <div>
            <label class="block text-sm font-medium mb-2" :class="theme('cardSubtitle').value">
              Rol del Usuario
            </label>
            <div class="relative">
              <select 
                v-model="form.rol"
                class="w-full rounded-xl appearance-none border px-4 py-2.5 pr-10 outline-none transition-colors"
                :class="isDark ? 'bg-gray-800 border-gray-600 text-white focus:border-blue-500' : 'bg-white border-gray-200 text-gray-900 focus:border-blue-500'"
              >
                <option value="docente">Docente</option>
                <option value="secretaria">Secretaria</option>
                <option value="supervisor">Supervisor</option>
                <option value="empleado">Otros</option>
              </select>
              <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none" :class="isDark ? 'text-white' : 'text-gray-600'">
                <i class="fas fa-chevron-down text-xs"></i>
              </div>
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium mb-2" :class="theme('cardSubtitle').value">
              Estado
            </label>
            <div class="relative">
              <select 
                v-model="form.estado"
                class="w-full rounded-xl appearance-none border px-4 py-2.5 pr-10 outline-none transition-colors"
                :class="isDark ? 'bg-gray-800 border-gray-600 text-white focus:border-blue-500' : 'bg-white border-gray-200 text-gray-900 focus:border-blue-500'"
              >
                <option value="activo">Activo</option>
                <option value="inactivo">Inactivo</option>
              </select>
              <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none" :class="isDark ? 'text-white' : 'text-gray-600'">
                <i class="fas fa-chevron-down text-xs"></i>
              </div>
            </div>
          </div>
        </div>

        <!-- Row 3: Password (solo crear) -->
        <div v-if="!isEditing" class="grid grid-cols-1 sm:grid-cols-2 gap-5">
          <div>
            <label class="block text-sm font-medium mb-2" :class="theme('cardSubtitle').value">
              Contraseña
            </label>
            <div class="relative">
                <input 
                    :type="showPassword ? 'text' : 'password'" 
                    v-model="form.password"
                    required
                    class="w-full rounded-xl border px-4 py-2.5 pr-10 outline-none transition-colors"
                    :class="isDark ? 'bg-gray-800 border-gray-600 text-white focus:border-blue-500' : 'bg-white border-gray-200 text-gray-900 focus:border-blue-500'"
                    placeholder="••••••••"
                />
                <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-400 hover:text-gray-600 transition-colors">
                    <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                </button>
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium mb-2" :class="theme('cardSubtitle').value">
              Confirmar Contraseña
            </label>
            <div class="relative">
                <input 
                    :type="showConfirmPassword ? 'text' : 'password'" 
                    v-model="form.password_confirmation"
                    required
                    class="w-full rounded-xl border px-4 py-2.5 pr-10 outline-none transition-colors"
                    :class="isDark ? 'bg-gray-800 border-gray-600 text-white focus:border-blue-500' : 'bg-white border-gray-200 text-gray-900 focus:border-blue-500'"
                    placeholder="••••••••"
                />
                <button type="button" @click="showConfirmPassword = !showConfirmPassword" class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-400 hover:text-gray-600 transition-colors">
                    <i :class="showConfirmPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                </button>
            </div>
          </div>
        </div>

        <!-- Botones -->
        <div class="flex justify-end gap-3 pt-6">
          <button 
            type="button"
            @click="$emit('close')"
            class="px-6 py-2.5 rounded-xl font-semibold transition-colors bg-gray-700 text-white hover:bg-gray-600"
          >
            Cancelar
          </button>
          <button 
            type="submit" 
            :disabled="loading"
            class="px-6 py-2.5 rounded-xl font-semibold shadow-lg flex items-center gap-2 transition-transform active:scale-95 bg-fuchsia-600 hover:bg-fuchsia-500 text-white"
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