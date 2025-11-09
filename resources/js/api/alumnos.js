import axios from '@/axiosConfig'

// ✅ Obtener lista de alumnos
export const getAlumnos = async () => {
  const { data } = await axios.get('/alumnos')
  return data
}

// ✅ Registrar nuevo alumno
export const createAlumno = async (payload) => {
  const { data } = await axios.post('/alumnos', payload)
  return data
}

// ✅ Actualizar alumno
export const updateAlumno = async (id, payload) => {
  const { data } = await axios.put(`/alumnos/${id}`, payload)
  return data
}

// ✅ Eliminar alumno
export const deleteAlumno = async (id) => {
  await axios.delete(`/alumnos/${id}`)
}
