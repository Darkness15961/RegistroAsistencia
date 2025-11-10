import api from '@/axiosConfig'

export const getAsistencias = async () => {
  const response = await api.get('/asistencias')
  return response.data
}

export const registrarAsistencia = async (payload) => {
  const response = await api.post('/asistencias/registrar', payload)
  return response.data
}
