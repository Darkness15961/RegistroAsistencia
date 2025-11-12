// resources/js/modules/usuarios/composables/useUsuarios.js
import { ref } from 'vue'
import api from '@/axiosConfig'

export function useUsuarios() {
  const usuarios = ref([])
  const loading = ref(false)
  const error = ref(null)

  const fetchUsuarios = async () => {
    loading.value = true
    try {
      const res = await api.get('/usuarios')
      usuarios.value = res.data.data || res.data // Laravel paginator o array directo
    } catch (err) {
      console.error(err)
      error.value = 'No se pudieron cargar los usuarios'
    } finally {
      loading.value = false
    }
  }

  const crearUsuario = async (nuevoUsuario) => {
    try {
      const res = await api.post('/usuarios', nuevoUsuario)
      await fetchUsuarios()
      return res.data
    } catch (err) {
      throw err.response?.data || { error: 'Error al crear usuario' }
    }
  }

  const actualizarUsuario = async (id, datos) => {
    try {
      const res = await api.put(`/usuarios/${id}`, datos)
      await fetchUsuarios()
      return res.data
    } catch (err) {
      throw err.response?.data || { error: 'Error al actualizar usuario' }
    }
  }

  const eliminarUsuario = async (id) => {
    try {
      await api.delete(`/usuarios/${id}`)
      usuarios.value = usuarios.value.filter(u => u.id_usuario !== id)
    } catch (err) {
      throw err.response?.data || { error: 'Error al eliminar usuario' }
    }
  }

  return {
    usuarios,
    loading,
    error,
    fetchUsuarios,
    crearUsuario,
    actualizarUsuario,
    eliminarUsuario
  }
}
