// resources/js/app.js
import { createApp } from 'vue'
import App from './app.vue' // Tu archivo principal
import router from './routes/index.js' // Tu router
import '../css/app.css'
import '@fortawesome/fontawesome-free/css/all.min.css'

// Importa tu config de Axios para registrar los interceptores
import './axiosConfig.js' 

const app = createApp(App)
app.use(router)
app.mount('#app')

