<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4"
       @click.self="$emit('cerrar')">
    <div class="rounded-3xl w-full max-w-lg shadow-2xl border px-8 sm:px-10 py-6 sm:py-8" :class="theme('card').value">
      
      <h2 class="text-xl font-bold mb-2 text-center capitalize" :class="theme('cardTitle').value">
        Corregir Asistencia: {{ registro.dia }}
      </h2>
      
      <p class="text-sm mb-6 font-mono opacity-80 text-center" :class="theme('cardSubtitle').value">
        Fecha: {{ form.fecha || 'Cargando...' }}
      </p>

      <p class="text-sm mb-6" :class="theme('cardSubtitle').value">
        Persona: <span class="font-semibold">{{ registro.asistencia.persona.nombre_completo }}</span>
      </p>

      <form @submit.prevent="guardarCorreccion">
        <div class="mb-4">
          <label class="block text-sm font-medium mb-2" :class="theme('cardSubtitle').value">
            Estado
          </label>
          <div class="relative">
            <select v-model="form.estado_asistencia"
                    class="w-full rounded-xl appearance-none border px-4 py-3 outline-none transition-colors"
                    :class="isDark 
                      ? 'bg-gray-800 border-gray-600 text-white focus:border-blue-500' 
                      : 'bg-white border-gray-200 text-gray-900 focus:border-blue-500'"
                    required>
              <option value="Presente">Presente</option>
              <option value="Tarde">Tarde</option>
              <option value="Falta">Falta</option>
              <option :value="null" class="text-red-500 font-bold bg-red-50 dark:bg-red-900/20">
                Anular Registro (Borrar)
              </option>
            </select>
            <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none" 
                 :class="isDark ? 'text-white' : 'text-gray-600'">
              <i class="fas fa-chevron-down text-xs"></i>
            </div>
          </div>
        </div>

        <div v-if="form.estado_asistencia !== null">
          <div class="mb-4">
            <label class="block text-sm font-medium mb-2" :class="theme('cardSubtitle').value">
              Hora de Entrada (Manual)
            </label>
            <input 
              v-model="form.hora_entrada" 
              type="time" 
              class="w-full rounded-xl border px-4 py-3 outline-none transition-colors" 
              :class="[
                isDark ? 'bg-gray-800 border-gray-600 text-white time-dark' : 'bg-white border-gray-200 text-gray-900'
              ]"
            />
          </div>

          <div class="mb-6">
            <label class="block text-sm font-medium mb-2" :class="theme('cardSubtitle').value">
              Hora de Salida (Opcional)
            </label>
            <input 
              v-model="form.hora_salida" 
              type="time" 
              class="w-full rounded-xl border px-4 py-3 outline-none transition-colors" 
              :class="[
                isDark ? 'bg-gray-800 border-gray-600 text-white time-dark' : 'bg-white border-gray-200 text-gray-900'
              ]"
            />
          </div>
        </div>
        
        <div v-else class="mb-6"></div>

        <div class="flex justify-end gap-3">
          <button type="button" @click="$emit('cerrar')" class="px-5 py-2.5 rounded-xl font-semibold" :class="theme('buttonSecondary').value">
            Cancelar
          </button>
          <button type="submit" :disabled="loading" class="px-5 py-2.5 rounded-xl font-semibold shadow-lg" :class="[theme('buttonPrimary').value, loading && 'opacity-50']">
            <i v-if="loading" class="fas fa-spinner animate-spin mr-2"></i>
            {{ form.estado_asistencia === null ? 'Borrar Registro' : 'Guardar Corrección' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useTheme } from '@/composables/useTheme'
import api from '@/axiosConfig'
import { startOfWeek, addDays, format } from 'date-fns'

const props = defineProps({
  registro: { type: Object, required: true }
})
const emit = defineEmits(['cerrar', 'actualizado'])
const { theme, isDark } = useTheme()
const loading = ref(false)

const form = reactive({
  id_asistencia: null,
  fecha: null,
  estado_asistencia: '',
  hora_entrada: null,
  hora_salida: null,
})

onMounted(() => {
  try {
    const baseDate = startOfWeek(new Date(), { weekStartsOn: 1 })
    const daysMap = { 
      'lunes': 0, 'martes': 1, 'miercoles': 2, 'jueves': 3, 
      'viernes': 4, 'sabado': 5, 'domingo': 6 
    }
    const dayIndex = daysMap[props.registro.dia] ?? 0
    const fechaExacta = addDays(baseDate, dayIndex)
    const fechaStr = format(fechaExacta, 'yyyy-MM-dd')
    
    fetchRegistroAsistencia(props.registro.asistencia.id_persona, fechaStr)
  } catch (e) {
    console.error(e)
  }
})

const fetchRegistroAsistencia = async (id_persona, fecha) => {
  loading.value = true
  try {
    const res = await api.get('/asistencias', { params: { id_persona, fecha } })
    const registroCompleto = res.data.data?.[0] || null

    if (registroCompleto) {
      Object.assign(form, {
        id_asistencia: registroCompleto.id_asistencia,
        fecha: registroCompleto.fecha,
        estado_asistencia: registroCompleto.estado_asistencia || 'Falta',
        hora_entrada: registroCompleto.hora_entrada ? registroCompleto.hora_entrada.substring(0,5) : null,
        hora_salida: registroCompleto.hora_salida ? registroCompleto.hora_salida.substring(0,5) : null
      })
    } else {
      let estadoInicial = 'Falta';
      if (props.registro.estado === 'P' || props.registro.estado === 'Presente') estadoInicial = 'Presente';
      else if (props.registro.estado === 'T' || props.registro.estado === 'Tarde') estadoInicial = 'Tarde';
      
      Object.assign(form, {
        id_asistencia: null,
        fecha,
        estado_asistencia: estadoInicial,
        hora_entrada: null,
        hora_salida: null
      })
    }
  } catch (err) {
    console.error('Error cargando registro:', err)
  } finally {
    loading.value = false
  }
}

const formatHora = (hora) => {
  if (!hora) return null
  if (hora.length === 5) return `${hora}:00`
  return hora
}

const guardarCorreccion = async () => {
  loading.value = true
  try {
    if (form.estado_asistencia === null) {
        if (form.id_asistencia) {
            await api.delete(`/asistencias/${form.id_asistencia}`)
        }
    } else {
        const payload = {
          estado_asistencia: form.estado_asistencia,
          hora_entrada: formatHora(form.hora_entrada),
          hora_salida: formatHora(form.hora_salida),
          metodo_registro: 'Manual'
        }

        if (form.id_asistencia) {
          await api.put(`/asistencias/${form.id_asistencia}`, payload)
        } else {
          await api.post('/asistencias/registrar', {
            id_persona: props.registro.asistencia.id_persona,
            fecha: form.fecha,
            ...payload
          })
        }
    }
    emit('actualizado')
  } catch (err) {
    console.error('Error al guardar:', err.response?.data || err)
    const text = err.response?.data?.message || JSON.stringify(err.response?.data || err.message)
    alert('Error al guardar: ' + text)
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
/* Truco CSS para invertir el color del ícono del reloj en modo oscuro 
  (WebKit browsers: Chrome, Edge, Safari)
*/
.time-dark::-webkit-calendar-picker-indicator {
    filter: invert(1);
    cursor: pointer;
}
</style>