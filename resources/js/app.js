import { createApp } from 'vue'
import App from './app.vue'
import router from './routes/index.js' // importa el router
import '../css/app.css' // importa Tailwind
import '@fortawesome/fontawesome-free/css/all.min.css'

createApp(App).use(router).mount('#app');