import { ref } from 'vue'
import api from '@/axiosConfig'

export function useEmpleados() {
  const empleados = ref([])
  const loading = ref(false)
  const error = ref(null)

  const fetchEmpleados = async () => {
    loading.value = true
    error.value = null
    try {
      const res = await api.get('/personas?tipo=empleado')
      empleados.value = res.data
    } catch (err) {
      console.error(err)
      error.value = 'No se pudieron cargar los empleados'
    } finally {
      loading.value = false
    }
  }

  const crearEmpleado = async (nuevoEmpleado) => {
    try {
      const res = await api.post('/personas', { ...nuevoEmpleado, tipo_persona: 'empleado' })
      await fetchEmpleados()
      return res.data
    } catch (err) {
      throw err.response?.data || { error: 'Error al crear empleado' }
    }
  }

  const actualizarEmpleado = async (id, datos) => {
    try {
      const res = await api.put(`/personas/${id}`, datos)
      await fetchEmpleados()
      return res.data
    } catch (err) {
      throw err.response?.data || { error: 'Error al actualizar empleado' }
    }
  }

  const eliminarEmpleado = async (id) => {
    try {
      await api.delete(`/personas/${id}`)
      empleados.value = empleados.value.filter(e => e.id_persona !== id)
    } catch (err) {
      throw err.response?.data || { error: 'Error al eliminar empleado' }
    }
  }

  return {
    empleados,
    loading,
    error,
    fetchEmpleados,
    crearEmpleado,
    actualizarEmpleado,
    eliminarEmpleado
  }
}
