import { ref } from 'vue'
import api from '@/axiosConfig'

export function usePersonal() {
  const personal = ref([])
  const loading = ref(false)
  const error = ref(null)

  const fetchPersonal = async () => {
    loading.value = true
    error.value = null
    try {
      // Asumiendo que tu API sigue usando 'tipo=empleado' para filtrar
      // Si cambiaste el backend a 'personal', actualiza aquí
      const res = await api.get('/personas?tipo=empleado')
      personal.value = res.data
    } catch (err) {
      console.error(err)
      error.value = 'No se pudo cargar el personal'
    } finally {
      loading.value = false
    }
  }

  const crearPersonal = async (nuevoPersonal) => {
    try {
      const res = await api.post('/personas', { ...nuevoPersonal, tipo_persona: 'empleado' })
      await fetchPersonal()
      return res.data
    } catch (err) {
      throw err.response?.data || { error: 'Error al crear personal' }
    }
  }

  const actualizarPersonal = async (id, datos) => {
    try {
      const res = await api.put(`/personas/${id}`, datos)
      await fetchPersonal()
      return res.data
    } catch (err) {
      throw err.response?.data || { error: 'Error al actualizar personal' }
    }
  }

  const eliminarPersonal = async (id) => {
    try {
      await api.delete(`/personas/${id}`)
      personal.value = personal.value.filter(p => p.id_persona !== id)
    } catch (err) {
      throw err.response?.data || { error: 'Error al eliminar personal' }
    }
  }

  return {
    personal,
    loading,
    error,
    fetchPersonal,
    crearPersonal,
    actualizarPersonal,
    eliminarPersonal
  }
}