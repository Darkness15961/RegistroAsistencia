import api from '@/axiosConfig'

export const getAreas = async () => {
  const response = await api.get('/areas')
  return response.data
}

export const getArea = async (id) => {
  const response = await api.get(`/areas/${id}`)
  return response.data
}
