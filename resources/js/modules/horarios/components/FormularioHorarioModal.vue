<template>
  <div 
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4"
    @click.self="$emit('cerrar')"
  >
    <div 
      class="w-full max-w-md rounded-3xl border shadow-2xl p-6" 
      :class="theme('card').value"
    >
      <h3 class="text-xl font-bold mb-6 flex items-center" :class="theme('cardTitle').value">
        <i class="fas fa-clock mr-3"></i>
        {{ horario ? 'Editar Horario' : 'Nuevo Horario' }}
      </h3>

      <form @submit.prevent="guardarHorario" class="space-y-4">
        
        <div>
          <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">
            Área
          </label>
          <select v-model="form.id_area" class="w-full rounded-xl appearance-none pr-8" :class="[theme('input').value, isDark ? 'bg-gray-900/50' : 'bg-white']" required>
            <option value="">Seleccione un área</option>
            <option v-for="a in areas" :key="a.id_area" :value="a.id_area" :class="isDark ? 'bg-gray-800' : 'bg-white'">
              {{ a.nombre_area }}
            </option>
          </select>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">
              Hora de Entrada
            </label>
            <input 
              v-model="form.hora_entrada"
              type="time" 
              class="w-full rounded-xl"
              :class="theme('input').value"
              required
            >
          </div>
          <div>
            <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">
              Hora de Salida
            </label>
            <input 
              v-model="form.hora_salida"
              type="time" 
              class="w-full rounded-xl"
              :class="theme('input').value"
              required
            >
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">
            Turno
          </label>
          <select v-model="form.turno" class="w-full rounded-xl appearance-none pr-8" :class="[theme('input').value, isDark ? 'bg-gray-900/50' : 'bg-white']" required>
            <option value="">Seleccione turno</option>
            <option value="mañana" :class="isDark ? 'bg-gray-800' : 'bg-white'">Mañana</option>
            <option value="tarde" :class="isDark ? 'bg-gray-800' : 'bg-white'">Tarde</option>
            <option value="noche" :class="isDark ? 'bg-gray-800' : 'bg-white'">Noche</option>
            <option value="completo" :class="isDark ? 'bg-gray-800' : 'bg-white'">Completo</option>
          </select>
        </div>

        <div class="flex justify-end gap-3 pt-4">
          <button 
            @click="$emit('cerrar')"
            type="button"
            class="px-5 py-2.5 rounded-xl font-semibold"
            :class="theme('buttonSecondary').value"
          >
            Cancelar
          </button>
          <button 
            type="submit"
            :disabled="loading"
            class="flex items-center gap-2 px-5 py-2.5 rounded-xl font-semibold shadow-lg"
            :class="[theme('buttonPrimary').value, loading && 'opacity-50']"
          >
            <i v-if="loading" class="fas fa-spinner animate-spin"></i>
            {{ horario ? 'Actualizar' : 'Guardar' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { useTheme } from '@/composables/useTheme'
import api from '@/axiosConfig'

const props = defineProps({ horario: Object })
const emit = defineEmits(['cerrar'])

const { theme, isDark } = useTheme()
const loading = ref(false)
const areas = ref([])

const form = ref({
  id_area: '',
  hora_entrada: '',
  hora_salida: '',
  turno: ''
})

const obtenerAreas = async () => {
  try {
    const res = await api.get('/areas')
    areas.value = res.data
  } catch (error) {
    console.error('Error cargando áreas:', error)
  }
}

watch(
  () => props.horario,
  (val) => {
    if (val) {
      form.value = {
        id_area: val.id_area,
        hora_entrada: val.hora_entrada,
        hora_salida: val.hora_salida,
        turno: val.turno
      }
    } else {
      form.value = { id_area: '', hora_entrada: '', hora_salida: '', turno: '' }
    }
  },
  { immediate: true }
)

onMounted(obtenerAreas)

const guardarHorario = async () => {
  loading.value = true
  try {
    if (props.horario) {
      // Esta es la llamada PUT para ACTUALIZAR
      await api.put(`/horarios/${props.horario.id_horario}`, form.value)
    } else {
      // Esta es la llamada POST para CREAR
      await api.post('/horarios', form.value)
    }
    emit('cerrar')
  } catch (error) {
    console.error('Error guardando horario:', error)
    alert(error.response?.data?.message || 'Error al guardar.')
  } finally {
    loading.value = false
  }
}
</script>