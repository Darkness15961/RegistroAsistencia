import axios from '@/axiosConfig.js'; // Importa tu instancia configurada [cite: js/axiosConfig.js]

export const getAreas = () => {
  return axios.get('/areas'); // Ruta de tu API [cite: darkness15961/registroasistencia/RegistroAsistencia-d5691700e1e73898572c88e0dca5815fba828093/routes/api.php]
};

export const createArea = (data) => {
  // data = { nombre_area: '...', descripcion: '...' }
  return axios.post('/areas', data); // [cite: darkness15961/registroasistencia/RegistroAsistencia-d5691700e1e73898572c88e0dca5815fba828093/routes/api.php]
};

export const deleteArea = (id) => {
    return axios.delete(`/areas/${id}`); // [cite: darkness15961/registroasistencia/RegistroAsistencia-d5691700e1e73898572c88e0dca5815fba828093/routes/api.php]
};