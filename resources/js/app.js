import { createApp } from 'vue'
import App from './app.vue'
import router from './routes/index.js' // importa el router
import ComponentsPlugin from './components.js' // Importa el plugin de componentes
import '../css/app.css' // importa Tailwind

const app = createApp(App)
app.use(ComponentsPlugin) // Registra todos los componentes globalmente
app.use(router)
app.mount('#app')
