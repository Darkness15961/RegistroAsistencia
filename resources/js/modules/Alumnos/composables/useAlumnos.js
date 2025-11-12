// resources/js/modules/Alumnos/composables/useAlumnos.js
import { ref } from 'vue'
import api from '@/axiosConfig'

export function useAlumnos() {
  const alumnos = ref([])

  const cargar = async () => {
    const res = await api.get('/personas')
    alumnos.value = res.data.filter(p => p.tipo_persona === 'alumno')
  }

  return { alumnos, cargar }
}
