import { ref } from 'vue'
import api from '@/axiosConfig'

export function useAlumnos() {
  const alumnos = ref([])
  const loading = ref(false)
  const error = ref(null)

  const fetchAlumnos = async () => {
    loading.value = true
    error.value = null
    try {
      // ✅ CORREGIDO: de 'alumno' a 'estudiante'
      const res = await api.get('/personas?tipo=estudiante') 
      alumnos.value = res.data.data || res.data
    } catch (err) {
      console.error(err)
      error.value = 'No se pudieron cargar los alumnos'
    } finally {
      loading.value = false
    }
  }

  const crearAlumno = async (nuevoAlumno) => {
    try {
      // ✅ CORREGIDO: de 'alumno' a 'estudiante'
      const res = await api.post('/personas', { ...nuevoAlumno, tipo_persona: 'estudiante' })
      await fetchAlumnos()
      return res.data
    } catch (err) {
      console.error("Error al crear alumno:", err.response?.data)
      throw err.response?.data || { error: 'Error al crear alumno' }
    }
  }

  const actualizarAlumno = async (id, datos) => {
    try {
      const res = await api.put(`/personas/${id}`, datos)
      await fetchAlumnos()
      return res.data
    } catch (err) {
      console.error("Error al actualizar alumno:", err.response?.data)
      throw err.response?.data || { error: 'Error al actualizar alumno' }
    }
  }

  const eliminarAlumno = async (id) => {
    try {
      await api.delete(`/personas/${id}`)
      alumnos.value = alumnos.value.filter(e => e.id_persona !== id)
    } catch (err) {
      console.error("Error al eliminar alumno:", err.response?.data)
      throw err.response?.data || { error: 'Error al eliminar alumno' }
    }
  }

  return {
    alumnos,
    loading,
    error,
    fetchAlumnos,
    crearAlumno,
    actualizarAlumno,
    eliminarAlumno
  }
}