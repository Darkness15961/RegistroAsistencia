import { createRouter, createWebHistory } from 'vue-router'

// --- Tu nuevo Lazy Loading  ---
const Login = () => import('@/modules/auth/pages/Login.vue')
const Home = () => import('@/modules/dashboard/pages/Home.vue')
const Asistencias = () => import('@/modules/asistencias/pages/Asistencias.vue')
const Empleados = () => import('@/modules/empleados/pages/Empleados.vue')
const Alumnos = () => import('@/modules/alumnos/pages/Alumnos.vue')
const Areas = () => import('@/modules/areas/pages/Areas.vue')
const Horarios = () => import('@/modules/horarios/pages/Horarios.vue')
const Usuarios = () => import('@/modules/usuarios/pages/Usuarios.vue')
const MiPerfil = () => import('@/modules/perfil/pages/MiPerfil.vue')
const Configuracion = () => import('@/modules/perfil/pages/Configuracion.vue')
const NotFound = () => import('@/modules/common/pages/NotFound.vue')
const RegistroAsistencia = () => import('@/modules/asistencias/pages/RegistroAsistencia.vue')


// --- Tu lógica de rutas de 'js/routes/index.js' [cite: js/routes/index.js] (con 'meta' y 'name') ---
const routes = [
  // --- RUTAS PÚBLICAS (No requieren login) ---
  { 
    path: '/login', 
    name: 'Login', // Corregido de 'login' a 'Login' para coincidir con tu app.vue
    component: Login,
    meta: { requiresAuth: false }
  },
  { 
    path: '/registro', 
    name: 'registro', 
    component: RegistroAsistencia,
    meta: { requiresAuth: false }
  },
  
  // --- RUTAS PRIVADAS (Requieren login) ---
  { 
    path: '/', 
    redirect: '/home',
    meta: { requiresAuth: true }
  },
  { 
    path: '/home', 
    name: 'Home', // Corregido de 'home' a 'Home'
    component: Home,
    meta: { requiresAuth: true }
  },
  { 
    path: '/horarios', 
    name: 'Horarios', 
    component: Horarios,
    meta: { requiresAuth: true }
  },
  { 
    path: '/asistencias', 
    name: 'Asistencias', 
    component: Asistencias,
    meta: { requiresAuth: true }
  },
  { 
    path: '/usuarios', 
    name: 'Usuarios', 
    component: Usuarios,
    meta: { requiresAuth: true }
  },
  { 
    path: '/empleados', 
    name: 'Empleados', 
    component: Empleados,
    meta: { requiresAuth: true }
  },
  { 
    path: '/areas', 
    name: 'Areas', 
    component: Areas,
    meta: { requiresAuth: true }
  },
  { 
    path: '/alumnos', 
    name: 'Alumnos', 
    component: Alumnos,
    meta: { requiresAuth: true }
  },
  { 
    path: '/perfil', 
    name: 'MiPerfil', 
    component: MiPerfil,
    meta: { requiresAuth: true }
  },
  { 
    path: '/configuracion', 
    name: 'configuracion', 
    component: Configuracion,
    meta: { requiresAuth: true }
  },
  
  // --- RUTA 404 (Catch-All) ---
  {
    path: '/:pathMatch(.*)*',
    name: 'NotFound',
    component: NotFound,
    meta: { requiresAuth: false }
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

// --- TU GUARDIÁN DE RUTAS (esencial para la seguridad) [cite: js/routes/index.js] ---
router.beforeEach((to, from, next) => {
  const isAuthenticated = !!localStorage.getItem('auth_token');
  const requiresAuth = to.meta.requiresAuth;

  if (requiresAuth && !isAuthenticated) {
    next({ name: 'Login' });
  } else if (!requiresAuth && isAuthenticated && to.name === 'Login') {
    // Evita ver el login si ya estás logueado
    next({ name: 'Home' });
  } else {
    next();
  }
});

export default router;