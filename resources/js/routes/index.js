import { createRouter, createWebHistory } from 'vue-router';

// Importa cada vista con su nombre correcto
import Login from '../pages/Login.vue';
import Home from '../pages/Home.vue';
import Horarios from '../pages/Horarios.vue';
import Areas from '../pages/Areas.vue';
import Asistencias from '../pages/Asistencias.vue';
import Usuarios from '../pages/Usuarios.vue';
import Empleados from '../pages/Empleados.vue';

const routes = [
  { path: '/', redirect: '/login' }, 
  { path: '/login', name: 'login', component: Login },
  { path: '/home', name: 'home', component: Home },
  { path: '/horarios', name: 'horarios', component: Horarios },
  { path: '/areas', name: 'areas', component: Areas },
  { path: '/asistencias', name: 'asistencias', component: Asistencias },
  { path: '/usuarios', name: 'usuarios', component: Usuarios },
  { path: '/empleados', name: 'empleados', component: Empleados },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
