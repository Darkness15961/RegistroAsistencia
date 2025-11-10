import api from '@/axiosConfig'

export const getUsuarios = async () => {
  const response = await api.get('/usuarios')
  return response.data
}

export const getUsuario = async (id) => {
  const response = await api.get(`/usuarios/${id}`)
  return response.data
}
