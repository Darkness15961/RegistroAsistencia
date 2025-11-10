import api from '@/axiosConfig'

export const getEmpleados = async () => {
  const response = await api.get('/personas') // o /empleados si tu backend tiene ese endpoint
  return response.data
}

export const getEmpleado = async (id) => {
  const response = await api.get(`/personas/${id}`)
  return response.data
}
