import { ref } from 'vue'
import api from '@/axiosConfig'

export function useGrupos() {
  const grupos = ref([])
  const loading = ref(false)
  const error = ref(null)

  const fetchGrupos = async () => {
    loading.value = true
    error.value = null
    try {
      const res = await api.get('/grupos')
      // Ajusta según la estructura de tu API (si es paginada o no)
      grupos.value = res.data.data || res.data 
    } catch (err) {
      console.error(err)
      error.value = 'No se pudieron cargar los grupos'
    } finally {
      loading.value = false
    }
  }

  const crearGrupo = async (nuevoGrupo) => {
    try {
      const res = await api.post('/grupos', nuevoGrupo)
      await fetchGrupos() // Recarga la lista
      return res.data
    } catch (err) {
      console.error("Error al crear grupo:", err.response?.data)
      throw err.response?.data || { error: 'Error al crear grupo' }
    }
  }

  const actualizarGrupo = async (id, datos) => {
    try {
      const res = await api.put(`/grupos/${id}`, datos)
      await fetchGrupos() // Recarga la lista
      return res.data
    } catch (err) {
      console.error("Error al actualizar grupo:", err.response?.data)
      throw err.response?.data || { error: 'Error al actualizar grupo' }
    }
  }

  const eliminarGrupo = async (id) => {
    try {
      await api.delete(`/grupos/${id}`)
      // Actualiza la lista localmente para evitar recarga
      grupos.value = grupos.value.filter(g => g.id_grupo !== id)
    } catch (err) {
      console.error("Error al eliminar grupo:", err.response?.data)
      throw err.response?.data || { error: 'Error al eliminar grupo' }
    }
  }

  // Asegúrate de exportar todo
  return {
    grupos,
    loading,
    error,
    fetchGrupos,
    crearGrupo,
    actualizarGrupo,
    eliminarGrupo
  }
}