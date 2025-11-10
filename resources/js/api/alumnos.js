import api from '@/axiosConfig'

export const getAlumnos = async () => {
  const response = await api.get('/personas') // Filtrar por tipo_persona = 'alumno' luego en Home.vue
  return response.data
}

export const getAlumno = async (id) => {
  const response = await api.get(`/personas/${id}`)
  return response.data
}
