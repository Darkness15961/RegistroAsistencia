<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4"
       @click.self="$emit('cerrar')">
    <div class="rounded-3xl w-full max-w-lg shadow-2xl border p-6" :class="theme('card').value">
      <h2 class="text-xl font-bold mb-6" :class="theme('cardTitle').value">
        Corregir Asistencia: {{ registro.dia }}
      </h2>

      <p class="text-sm mb-4" :class="theme('cardSubtitle').value">
        Persona: <span class="font-semibold">{{ registro.asistencia.persona.nombre_completo }}</span>
      </p>

      <form @submit.prevent="guardarCorreccion">
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">
            Estado
          </label>
          <select v-model="form.estado_asistencia"
                  class="w-full rounded-xl appearance-none pr-8"
                  :class="[theme('input').value, isDark ? 'bg-gray-900/50' : 'bg-white']"
                  required>
            <option value="Presente">Presente</option>
            <option value="Tarde">Tarde</option>
            <option value="Falta">Falta</option>
          </select>
        </div>

        <div class="mb-4">
          <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">
            Hora de Entrada (Manual)
          </label>
          <input v-model="form.hora_entrada" type="time" class="w-full rounded-xl" :class="theme('input').value" />
        </div>

        <div class="mb-6">
          <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">
            Hora de Salida (Opcional)
          </label>
          <input v-model="form.hora_salida" type="time" class="w-full rounded-xl" :class="theme('input').value" />
        </div>

        <div class="flex justify-end gap-3">
          <button type="button" @click="$emit('cerrar')" class="px-5 py-2.5 rounded-xl font-semibold" :class="theme('buttonSecondary').value">
            Cancelar
          </button>
          <button type="submit" :disabled="loading" class="px-5 py-2.5 rounded-xl font-semibold shadow-lg" :class="[theme('buttonPrimary').value, loading && 'opacity-50']">
            <i v-if="loading" class="fas fa-spinner animate-spin mr-2"></i>
            Guardar Corrección
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
  registro: { type: Object, required: true } // { asistencia, dia, estado }
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

// Calcula la fecha exacta según el día (lunes..viernes) y la semana actual
onMounted(() => {
  try {
    const baseDate = startOfWeek(new Date(), { weekStartsOn: 1 })
    const daysMap = { 'lunes': 0, 'martes': 1, 'miercoles': 2, 'jueves': 3, 'viernes': 4 }
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
      // No existe -> preparar creación (por defecto 'Falta' o según reporte)
      const estado = (props.registro.estado && props.registro.estado.toUpperCase() === 'F') ? 'Falta' : 'Presente'
      Object.assign(form, {
        id_asistencia: null,
        fecha,
        estado_asistencia: estado,
        hora_entrada: null,
        hora_salida: null
      })
    }
  } catch (err) {
    console.error('Error cargando registro especifico:', err)
    Object.assign(form, {
      id_asistencia: null,
      fecha: format(new Date(), 'yyyy-MM-dd'),
      estado_asistencia: 'Falta',
      hora_entrada: null,
      hora_salida: null
    })
  } finally {
    loading.value = false
  }
}

// helper para asegurar formato H:i:s o null
const formatHora = (hora) => {
  if (!hora) return null
  // hora puede venir 'HH:MM' => convertir a 'HH:MM:00'
  if (hora.length === 5) return `${hora}:00`
  return hora
}

const guardarCorreccion = async () => {
  loading.value = true
  try {
    const payload = {
      estado_asistencia: form.estado_asistencia,
      hora_entrada: formatHora(form.hora_entrada),
      hora_salida: formatHora(form.hora_salida),
      metodo_registro: 'Manual'
    }

    if (form.id_asistencia) {
      // Actualizar
      const res = await api.put(`/asistencias/${form.id_asistencia}`, payload)
      // opcional: podrías usar res.data para actualizar localmente si quisieras
    } else {
      // Crear (usamos ruta pública registrar)
      await api.post('/asistencias/registrar', {
        id_persona: props.registro.asistencia.id_persona,
        fecha: form.fecha,
        ...payload
      })
    }

    // Avisamos al parent que actualice datos
    emit('actualizado')
  } catch (err) {
    console.error('Error al guardar:', err.response?.data || err)
    // Muestra errores de validación del backend
    const text = err.response?.data?.message 
      || JSON.stringify(err.response?.data || err.message)
    alert('Error al guardar la corrección: ' + text)
  } finally {
    loading.value = false
  }
}
</script>
