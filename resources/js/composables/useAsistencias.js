import { ref, computed } from 'vue'
import api from '@/axiosConfig'
// npm i date-fns
import { startOfWeek, addDays, format, subDays } from 'date-fns'

export function useAsistencias() {
  // Estado que representa un día dentro de la semana visible
  const currentDate = ref(new Date())
  const asistencias = ref([]) // arreglo de resúmenes por persona: { id_persona, persona: {...}, lunes:'P' ... }
  const loading = ref(false)
  const error = ref(null)

  // Rango visible de la semana (Lunes a Viernes)
  const weekRange = computed(() => {
    const start = startOfWeek(currentDate.value, { weekStartsOn: 1 }) // Monday
    const friday = addDays(start, 4)
    return {
      start,
      end: friday,
      display: `Semana del ${format(start, 'dd MMM')} al ${format(friday, 'dd MMM yyyy')}`,
      apiDate: format(start, 'yyyy-MM-dd') // enviamos Lunes como referencia
    }
  })

  const normalizeResumen = (arr) => {
    // Asegura la estructura esperada y valores de días
    return (arr || []).map(item => {
      const copy = { ...item }
      // Normalizar persona
      copy.persona = copy.persona || {}
      // Asegurar claves de días (lunes..domingo)
      ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'].forEach(d => {
        let val = copy[d]

        // Estructura base por defecto (Falta)
        let normalized = {
          estado: 'F',
          hora_entrada: null
        }

        if (val) {
          // Si viene objeto (nuevo backend)
          if (typeof val === 'object') {
            normalized = { ...val }
            // Normalizar estado
            let v = String(normalized.estado || '').trim().toLowerCase()
            if (v === 'presente' || v === 'p') normalized.estado = 'P'
            else if (v === 'tarde' || v === 't') normalized.estado = 'T'
            else if (v === 'falta' || v === 'f') normalized.estado = 'F'
            else normalized.estado = String(normalized.estado || 'F').toUpperCase().substring(0, 1)
          }
          // Si viene string (viejo backend o fallback)
          else {
            let v = String(val).trim().toLowerCase()
            if (v === 'presente' || v === 'p') normalized.estado = 'P'
            else if (v === 'tarde' || v === 't') normalized.estado = 'T'
            else if (v === 'falta' || v === 'f') normalized.estado = 'F'
            else normalized.estado = String(val).toUpperCase().substring(0, 1)
          }
        }

        copy[d] = normalized
      })
      return copy
    })
  }

  const fetchAsistenciasSemana = async (opts = {}) => {
    // opts: { dateString } // dateString = 'yyyy-MM-dd' lunes referencial
    loading.value = true
    error.value = null
    try {
      const dateToSend = opts.dateString || weekRange.value.apiDate
      const res = await api.get('/asistencias-semana', {
        params: { date: dateToSend, group_id: opts.group_id } // backend puede usar group_id si lo soportas
      })

      // si tu backend devuelve un objeto { data: [...] } o devuelve directamente un array,
      // normalizamos ambos casos.
      const payload = Array.isArray(res.data) ? res.data : (res.data.data || res.data || [])
      asistencias.value = normalizeResumen(payload)
      return asistencias.value
    } catch (err) {
      console.error('Error cargando asistencias:', err)
      error.value = 'No se pudieron cargar las asistencias.'
      asistencias.value = []
      throw err
    } finally {
      loading.value = false
    }
  }

  const navigateWeek = (direction) => {
    if (direction === 'prev') {
      currentDate.value = subDays(currentDate.value, 7)
    } else if (direction === 'next') {
      currentDate.value = addDays(currentDate.value, 7)
    }
    // Retorna la promesa para que el caller pueda esperarla
    return fetchAsistenciasSemana()
  }

  // Forzar recarga explícita (útil desde parent)
  const refreshWeek = (opts = {}) => {
    return fetchAsistenciasSemana(opts)
  }

  return {
    asistencias,
    loading,
    error,
    weekRange,
    navigateWeek,
    fetchAsistenciasSemana,
    refreshWeek
  }
}
