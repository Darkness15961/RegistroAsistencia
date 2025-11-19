<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4"
       @click.self="$emit('cerrar')">
    <div class="rounded-3xl w-full max-w-lg shadow-2xl border p-6" :class="theme('card').value">
      
      <h2 class="text-xl font-bold mb-6" :class="theme('cardTitle').value">
        <i class="fas fa-clock mr-2"></i>
        {{ horario ? 'Editar Horario' : 'Registrar Horario' }}
      </h2>

      <form @submit.prevent="guardarHorario">
        <div class="space-y-4">
          
          <div>
            <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">
              Área
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
              <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none" 
                   :class="isDark ? 'text-white' : 'text-gray-600'">
                <i class="fas fa-chevron-down text-xs"></i>
              </div>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">
              Turno
            </label>
            <div class="relative">
              <select 
                v-model="form.turno"
                class="w-full rounded-xl appearance-none border px-3 py-2 pr-8 outline-none transition-colors"
                :class="isDark ? 'bg-gray-800 border-gray-600 text-white focus:border-blue-500' : 'bg-white border-gray-200 text-gray-900 focus:border-blue-500'"
                required
              >
                <option value="Mañana">Mañana</option>
                <option value="Tarde">Tarde</option>
                <option value="Noche">Noche</option>
                <option value="Completo">Completo</option>
              </select>
              <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none" 
                   :class="isDark ? 'text-white' : 'text-gray-600'">
                <i class="fas fa-chevron-down text-xs"></i>
              </div>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">
                Hora Entrada
              </label>
              <input 
                v-model="form.hora_entrada" 
                type="time" 
                class="w-full rounded-xl border px-3 py-2 outline-none transition-colors"
                :class="isDark ? 'bg-gray-800 border-gray-600 text-white focus:border-blue-500 time-dark' : 'bg-white border-gray-200 text-gray-900 focus:border-blue-500'"
                required
              />
            </div>

            <div>
              <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">
                Hora Salida
              </label>
              <input 
                v-model="form.hora_salida" 
                type="time" 
                class="w-full rounded-xl border px-3 py-2 outline-none transition-colors"
                :class="isDark ? 'bg-gray-800 border-gray-600 text-white focus:border-blue-500 time-dark' : 'bg-white border-gray-200 text-gray-900 focus:border-blue-500'"
                required
              />
            </div>
          </div>

        </div>

        <div class="flex justify-end gap-3 mt-8">
          <button type="button" @click="$emit('cerrar')" class="px-5 py-2.5 rounded-xl font-semibold" :class="theme('buttonSecondary').value">
            Cancelar
          </button>
          <button type="submit" :disabled="loading" class="px-5 py-2.5 rounded-xl font-semibold shadow-lg" :class="[theme('buttonPrimary').value, loading && 'opacity-50']">
            <i v-if="loading" class="fas fa-spinner animate-spin mr-2"></i>
            {{ horario ? 'Actualizar' : 'Guardar' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, watch } from 'vue'
import { useTheme } from '@/composables/useTheme'
import api from '@/axiosConfig'

const props = defineProps({
  horario: { type: [Object, null], default: null }
})
const emit = defineEmits(['cerrar', 'actualizado'])
const { theme, isDark } = useTheme()
const loading = ref(false)
const areas = ref([])

const form = reactive({
  id_area: '',
  turno: 'Mañana',
  hora_entrada: '',
  hora_salida: ''
})

// --- Helper para limpiar la hora (Quitar segundos :00) ---
const formatHoraInput = (hora) => {
  if (!hora) return ''
  // Si viene 08:00:00, lo cortamos a 08:00 para el input type="time"
  if (hora.length > 5) {
    return hora.substring(0, 5)
  }
  return hora
}

// Cargar Áreas para el select
onMounted(async () => {
  try {
    const res = await api.get('/areas')
    areas.value = res.data
  } catch (e) {
    console.error("Error cargando áreas", e)
  }
})

// Rellenar formulario al editar
watch(() => props.horario, (nuevo) => {
  if (nuevo) {
    form.id_area = nuevo.id_area
    form.turno = nuevo.turno
    // Aplicamos el formato correcto al cargar
    form.hora_entrada = formatHoraInput(nuevo.hora_entrada)
    form.hora_salida = formatHoraInput(nuevo.hora_salida)
  } else {
    // Reset
    form.id_area = ''
    form.turno = 'Mañana'
    form.hora_entrada = ''
    form.hora_salida = ''
  }
}, { immediate: true })

const guardarHorario = async () => {
  loading.value = true
  try {
    // Preparamos el payload asegurando el formato HH:MM
    const payload = {
      id_area: form.id_area,
      turno: form.turno,
      hora_entrada: formatHoraInput(form.hora_entrada),
      hora_salida: formatHoraInput(form.hora_salida)
    }

    if (props.horario) {
      await api.put(`/horarios/${props.horario.id_horario}`, payload)
    } else {
      await api.post('/horarios', payload)
    }

    emit('actualizado')
    emit('cerrar')
  } catch (err) {
    console.error('Error guardando horario:', err)
    // Mostrar error legible si viene del backend
    const msg = err.response?.data?.message || 'Error al guardar el horario.'
    alert(msg)
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
/* Invertir color del icono de reloj en modo oscuro */
.time-dark::-webkit-calendar-picker-indicator {
    filter: invert(1);
    cursor: pointer;
}
</style>