import { createApp } from 'vue'
import App from './app.vue'
import router from './routes/index.js' // importa el router
import '../css/app.css' // importa Tailwind

createApp(App).use(router).mount('#app');