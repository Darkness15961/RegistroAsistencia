import { createApp } from 'vue'
import App from './app.vue'
import router from './routes/index.js' 
import ComponentsPlugin from './components.js' 
import '../css/app.css' 
import '@fortawesome/fontawesome-free/css/all.min.css'

const app = createApp(App)
app.use(ComponentsPlugin) 
app.use(router)
app.mount('#app')
