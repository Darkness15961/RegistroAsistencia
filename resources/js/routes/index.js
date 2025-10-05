// resources/js/router/index.js
import { createRouter, createWebHistory } from 'vue-router';

// Páginas
import Login from '../pages/Login.vue';
import Home from '../pages/Home.vue';
import Horarios from '../pages/Horarios.vue';
import Asistencias from '../pages/Asistencias.vue';
import Usuarios from '../pages/Usuarios.vue';
import Empleados from '../pages/Empleados.vue';

const routes = [
  // Login sin layout
  { 
    path: '/login', 
    name: 'login', 
    component: Login 
  },
  
  // Ruta raíz redirige a home
  { 
    path: '/', 
    redirect: '/home' 
  },
  
  // Páginas con layout (sidebar + header)
  { 
    path: '/home', 
    name: 'home', 
    component: Home 
  },
  { 
    path: '/horarios', 
    name: 'horarios', 
    component: Horarios 
  },
  { 
    path: '/asistencias', 
    name: 'asistencias', 
    component: Asistencias 
  },
  { 
    path: '/usuarios', 
    name: 'usuarios', 
    component: Usuarios 
  },
  { 
    path: '/empleados', 
    name: 'empleados', 
    component: Empleados 
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;