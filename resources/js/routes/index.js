import { createRouter, createWebHistory } from 'vue-router';

// Páginas
import Login from '../pages/Login.vue';
import Home from '../pages/Home.vue';
import Horarios from '../pages/Horarios.vue';
import Asistencias from '../pages/Asistencias.vue';
import Usuarios from '../pages/Usuarios.vue';
import Empleados from '../pages/Empleados.vue';
import areas from '../pages/Areas.vue';
import RegistroAsistencia from '../pages/RegistroAsistencia.vue'; 
import MiPerfil from '../pages/MiPerfil.vue'; 
import Configuracion from '../pages/Configuracion.vue'; 
import NotFound from '../pages/NotFound.vue';

const routes = [
  // --- RUTAS PÚBLICAS (No requieren login) ---
  { 
    path: '/login', 
    name: 'login', 
    component: Login,
    meta: { requiresAuth: false } // Le decimos que esta NO requiere autenticación
  },
  { 
    path: '/registro', 
    name: 'registro', 
    component: RegistroAsistencia,
    meta: { requiresAuth: false } // Esta tampoco
  },
  
  // --- RUTAS PRIVADAS (Requieren login) ---
  { 
    path: '/', 
    redirect: '/home',
    meta: { requiresAuth: true } // Redirigir siempre requiere autenticación
  },
  { 
    path: '/home', 
    name: 'home', 
    component: Home,
    meta: { requiresAuth: true } // <-- AÑADIR ESTO
  },
  { 
    path: '/horarios', 
    name: 'horarios', 
    component: Horarios,
    meta: { requiresAuth: true } // <-- AÑADIR ESTO
  },
  { 
    path: '/asistencias', 
    name: 'asistencias', 
    component: Asistencias,
    meta: { requiresAuth: true } // <-- AÑADIR ESTO
  },
  { 
    path: '/usuarios', 
    name: 'usuarios', 
    component: Usuarios,
    meta: { requiresAuth: true } // <-- AÑADIR ESTO
  },
  { 
    path: '/empleados', 
    name: 'empleados', 
    component: Empleados,
    meta: { requiresAuth: true } // <-- AÑADIR ESTO
  },
  { 
    path: '/areas', 
    name: 'areas', 
    component: areas,
    meta: { requiresAuth: true } // <-- AÑADIR ESTO
  },
  { 
    path: '/perfil', 
    name: 'perfil', 
    component: MiPerfil,
    meta: { requiresAuth: true } // <-- AÑADIR ESTO
  },
  { 
    path: '/configuracion', 
    name: 'configuracion', 
    component: Configuracion,
    meta: { requiresAuth: true } // <-- AÑADIR ESTO
  },
  // --- RUTA 404 (Catch-All) ---
  {
    path: '/:pathMatch(.*)*', // Esto atrapa cualquier ruta no definida
    name: 'NotFound',
    component: NotFound,
    meta: { requiresAuth: false } // No requiere login para verla
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

// --- ¡EL GUARDIÁN DE RUTA! ---
// Esto se ejecuta ANTES de cada cambio de página
router.beforeEach((to, from, next) => {
  const isAuthenticated = !!localStorage.getItem('auth_token'); // Revisa si el token existe
  const requiresAuth = to.meta.requiresAuth; // Revisa si la ruta destino requiere login

  if (requiresAuth && !isAuthenticated) {
    // Si la ruta requiere login Y no hay token...
    // redirigir al login.
    next({ name: 'login' });
  } else if (!requiresAuth && isAuthenticated) {
    // Si la ruta es pública (como 'login') Y SÍ hay un token...
    // redirigir al home (evita ver el login si ya estás logueado).
    next({ name: 'home' });
  } else {
    // En cualquier otro caso, deja pasar.
    next();
  }
});

export default router;