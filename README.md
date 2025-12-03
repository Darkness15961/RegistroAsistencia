# 🎓 Sistema de Registro de Asistencias con Reconocimiento Facial

<p align="center">
  <img src="public/images/logo1.png" alt="Logo 4scan" width="120">
</p>

<p align="center">
  <strong>Sistema inteligente de control de asistencias con IA para instituciones educativas</strong>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.0-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel">
  <img src="https://img.shields.io/badge/Vue.js-3.5-4FC08D?style=for-the-badge&logo=vue.js&logoColor=white" alt="Vue.js">
  <img src="https://img.shields.io/badge/TailwindCSS-4.1-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="TailwindCSS">
  <img src="https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
  <img src="https://img.shields.io/badge/Face--API.js-0.22-FF6B6B?style=for-the-badge" alt="Face-API.js">
</p>

---

## 📋 Descripción

Sistema web moderno para el control y gestión de asistencias en instituciones educativas, que integra **reconocimiento facial con inteligencia artificial** para el registro automático de entrada y salida. Desarrollado con las últimas tecnologías web y procesamiento de IA en el navegador.

### ✨ Características Principales

- 🤖 **Reconocimiento Facial Automático** - Registro de asistencias mediante IA sin contacto
- 📊 **Dashboard Interactivo** - Estadísticas y gráficos en tiempo real
- ⏰ **Validación de Horarios** - Ventanas de tiempo configurables por área
- 👥 **Gestión de Personal** - CRUD completo de estudiantes y empleados
- 📅 **Vista Semanal** - Tabla de asistencias con navegación por semanas
- 🔐 **Sistema de Roles** - Admin, Supervisor y Docente con permisos diferenciados
- 📱 **Responsive Design** - Interfaz adaptable a cualquier dispositivo
- 🎨 **Tema Claro/Oscuro** - Modo visual personalizable
- 📈 **Reportes Visuales** - Gráficos con Chart.js
- 🚫 **Anti-Rebote** - Prevención de registros duplicados

---

## 🛠️ Tecnologías Utilizadas

### Backend
- **PHP 8.2** - Lenguaje del servidor
- **Laravel 12.0** - Framework PHP moderno
- **Laravel Sanctum 4.2** - Autenticación API con tokens
- **MySQL 8.0** - Base de datos relacional
- **Carbon 3.x** - Manejo avanzado de fechas

### Frontend
- **Vue.js 3.5** - Framework JavaScript reactivo (Composition API)
- **Vue Router 4.5** - Enrutamiento SPA
- **Axios 1.13** - Cliente HTTP
- **TailwindCSS 4.1** - Framework CSS utility-first
- **Vite 7.0** - Build tool y dev server ultrarrápido

### Inteligencia Artificial
- **Face-API.js 0.22** - Reconocimiento facial con TensorFlow.js
- **Modelos Pre-entrenados:**
  - `tiny_face_detector` - Detección de rostros
  - `face_landmark_68` - 68 puntos faciales
  - `face_recognition` - Descriptores de 128 dimensiones

### Visualización de Datos
- **Chart.js 4.5** - Gráficos interactivos
- **Vue-ChartJS 5.3** - Wrapper de Chart.js para Vue
- **Date-fns 4.1** - Utilidades de fechas
- **FontAwesome 7.1** - Librería de iconos

---

## 📦 Instalación

### Requisitos Previos

- PHP >= 8.2
- Composer
- Node.js >= 18.x
- MySQL >= 8.0
- Git

### Pasos de Instalación

1. **Clonar el repositorio**
```bash
git clone https://github.com/tu-usuario/app-RegistroAsistencia.git
cd app-RegistroAsistencia
```

2. **Instalar dependencias de PHP**
```bash
composer install
```

3. **Instalar dependencias de Node.js**
```bash
npm install
```

4. **Configurar variables de entorno**
```bash
cp .env.example .env
```

Edita el archivo `.env` y configura:
```env
APP_NAME="Sistema de Asistencias"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=registro_asistencia
DB_USERNAME=root
DB_PASSWORD=

SANCTUM_STATEFUL_DOMAINS=localhost:5173
SESSION_DOMAIN=localhost
```

5. **Generar clave de aplicación**
```bash
php artisan key:generate
```

6. **Crear base de datos**
```sql
CREATE DATABASE registro_asistencia CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

7. **Ejecutar migraciones**
```bash
php artisan migrate
```

8. **Ejecutar seeders (datos de prueba)**
```bash
php artisan db:seed --class=DemoDataSeeder
```

9. **Descargar modelos de Face-API.js**

Los modelos deben estar en `public/models/`:
- `tiny_face_detector_model-weights_manifest.json`
- `face_landmark_68_model-weights_manifest.json`
- `face_recognition_model-weights_manifest.json`

Descárgalos desde: [Face-API.js Models](https://github.com/justadudewhohacks/face-api.js/tree/master/weights)

10. **Crear enlace simbólico para storage**
```bash
php artisan storage:link
```

---

## 🚀 Uso

### Modo Desarrollo

**Terminal 1 - Backend Laravel:**
```bash
php artisan serve
```
El servidor estará en: `http://127.0.0.1:8000`

**Terminal 2 - Frontend Vue:**
```bash
npm run dev
```
El frontend estará en: `http://localhost:5173`

### Modo Producción

1. **Compilar assets**
```bash
npm run build
```

2. **Optimizar Laravel**
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

3. **Configurar servidor web** (Apache/Nginx)

Apuntar el document root a `public/`

---

## 📁 Estructura del Proyecto

```
app-RegistroAsistencia/
├── app/
│   ├── Http/
│   │   ├── Controllers/          # Controladores REST API
│   │   │   ├── AsistenciaController.php
│   │   │   ├── PersonaController.php
│   │   │   ├── AuthController.php
│   │   │   └── ...
│   │   └── Middleware/           # Middleware personalizado
│   └── Models/                   # Modelos Eloquent
│       ├── Asistencia.php
│       ├── Persona.php
│       ├── Usuario.php
│       └── ...
├── database/
│   ├── migrations/               # Migraciones de BD
│   └── seeders/                  # Datos de prueba
├── public/
│   ├── models/                   # Modelos de Face-API.js
│   └── images/                   # Imágenes públicas
├── resources/
│   └── js/
│       ├── app.vue               # Componente raíz
│       ├── router.js             # Configuración de rutas
│       ├── axiosConfig.js        # Configuración de Axios
│       ├── composables/          # Lógica reutilizable
│       │   ├── useAuth.js
│       │   ├── useAsistencias.js
│       │   └── useTheme.js
│       ├── layout/               # Componentes de layout
│       │   ├── Header.vue
│       │   ├── Sidebar.vue
│       │   └── Footer.vue
│       └── modules/              # Módulos por funcionalidad
│           ├── dashboard/        # Dashboard principal
│           ├── asistencias/      # Gestión de asistencias
│           ├── personal/         # Gestión de personal
│           ├── usuarios/         # Gestión de usuarios
│           ├── areas/            # Gestión de áreas
│           ├── horarios/         # Gestión de horarios
│           └── auth/             # Autenticación
├── routes/
│   └── api.php                   # Rutas del API REST
├── .env                          # Variables de entorno
├── composer.json                 # Dependencias PHP
├── package.json                  # Dependencias Node.js
└── vite.config.js                # Configuración de Vite
```

---

## 👤 Usuarios de Prueba

Después de ejecutar los seeders, puedes acceder con:

| Usuario | Contraseña | Rol |
|---------|------------|-----|
| admin@test.com | password | Administrador |
| supervisor@test.com | password | Supervisor |
| docente@test.com | password | Docente |

---

## 🔌 API Endpoints

### Autenticación
```
POST   /api/login                 # Iniciar sesión
POST   /api/register              # Registrar usuario
POST   /api/logout                # Cerrar sesión
GET    /api/usuario-actual        # Obtener usuario autenticado
```

### Asistencias
```
POST   /api/asistencias/registrar # Registrar asistencia (IA)
GET    /api/asistencias-semana    # Obtener asistencias semanales
GET    /api/asistencias           # Listar asistencias
POST   /api/asistencias           # Crear asistencia manual
PUT    /api/asistencias/{id}      # Actualizar asistencia
DELETE /api/asistencias/{id}      # Eliminar asistencia
```

### Personal
```
GET    /api/personas              # Listar personas
POST   /api/personas              # Crear persona
GET    /api/personas/{id}         # Obtener persona
PUT    /api/personas/{id}         # Actualizar persona
DELETE /api/personas/{id}         # Eliminar persona
```

### Reconocimiento Facial
```
GET    /api/reconocimientos/descriptores  # Obtener descriptores faciales
POST   /api/reconocimientos               # Guardar descriptor facial
DELETE /api/reconocimientos/{id}          # Eliminar descriptor
```

### Dashboard
```
GET    /api/dashboard/stats       # Estadísticas generales
GET    /api/dashboard/asistencias-area    # Asistencias por área
GET    /api/dashboard/asistencias-semana  # Asistencias semanales
```

---

## 🎯 Funcionalidades Detalladas

### 1. Reconocimiento Facial
- Detección de rostros en tiempo real con cámara web
- Comparación con descriptores almacenados (umbral 0.5)
- Registro automático de entrada/salida
- Feedback visual del estado de reconocimiento
- Validación de ventanas de tiempo

### 2. Gestión de Asistencias
- Vista semanal con navegación
- Edición manual de registros
- Filtros por grupo y área
- Estados: Presente (P), Tarde (T), Falta (F)
- Vista separada de salidas (solo personal)

### 3. Dashboard
- Tarjetas de estadísticas (total personal, estudiantes, asistencias)
- Gráfico de barras: Asistencias por área
- Gráfico de líneas: Tendencia semanal
- Gráfico circular: Estado del día
- Tablas de últimas asistencias y personal

### 4. Gestión de Personal
- CRUD completo de personas
- Captura de rostro con validación
- Asignación de grupos y áreas
- Búsqueda y filtros
- Carga de foto de perfil

### 5. Sistema de Roles
- **Admin:** Acceso total al sistema
- **Supervisor:** Gestión de personal y asistencias
- **Docente:** Solo sus grupos asignados

---

## ⚙️ Configuración Avanzada

### Ventanas de Tiempo

Las ventanas de tiempo se configuran en `AsistenciaController.php`:

**Estudiantes:**
- Pueden marcar desde **1 hora antes** de la hora de entrada
- Hasta **15 minutos después** (marcado como "Tarde")
- No marcan salida

**Personal:**
- Pueden marcar desde **30 minutos antes** de la hora de entrada
- Hasta **15 minutos después** (marcado como "Tarde")
- Marcan entrada y salida
- Salida hasta **15 minutos después** de la hora programada

### Horarios por Área

Los horarios se configuran en el módulo de Horarios:
1. Ir a **Configuración → Horarios**
2. Crear/editar horario
3. Asignar área
4. Definir hora de entrada y salida

### Umbral de Reconocimiento Facial

En `RegistroAsistencia.vue`, línea ~251:
```javascript
return new faceapi.FaceMatcher(labeled, 0.5)
//                                      ^^^
//                                      Umbral (0.0 - 1.0)
```

- **0.0 - 0.4:** Muy estricto (puede rechazar rostros válidos)
- **0.5 - 0.6:** Recomendado (balance entre seguridad y usabilidad)
- **0.7 - 1.0:** Muy permisivo (puede aceptar rostros incorrectos)

---

## 🌐 Despliegue

### Hostinger (Producción)

1. **Subir archivos** vía FTP o Git
2. **Configurar .env** con credenciales de producción
3. **Ejecutar migraciones**
```bash
php artisan migrate --force
```
4. **Compilar assets**
```bash
npm run build
```
5. **Optimizar**
```bash
php artisan optimize
```

### Variables de Entorno (Producción)
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tu-dominio.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=tu_base_datos
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña_segura

SANCTUM_STATEFUL_DOMAINS=tu-dominio.com
SESSION_DOMAIN=tu-dominio.com
```

---

## 🐛 Solución de Problemas

### Error: "No hay rostros registrados"
- Asegúrate de haber registrado al menos un rostro en el módulo de Personal
- Verifica que los modelos de Face-API.js estén en `public/models/`

### Error: "No hay horario configurado"
- Crea un horario en **Configuración → Horarios**
- Asigna el horario al área correspondiente
- Verifica que las personas tengan área asignada

### Error: "Tiempo de marcado expirado"
- Verifica que la hora actual esté dentro de la ventana permitida
- Revisa la configuración del horario del área
- Considera ajustar las ventanas de tiempo en el código

### La cámara no funciona
- Verifica permisos del navegador para acceder a la cámara
- Usa HTTPS en producción (requerido por navegadores modernos)
- Prueba en un navegador diferente

---

## 📚 Documentación Adicional

- [Informe Final del Proyecto](INFORME_FINAL.md)
- [Laravel Documentation](https://laravel.com/docs)
- [Vue.js Documentation](https://vuejs.org/)
- [Face-API.js Documentation](https://github.com/justadudewhohacks/face-api.js)
- [TailwindCSS Documentation](https://tailwindcss.com/)

---

## 🤝 Contribuciones

Las contribuciones son bienvenidas. Por favor:

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

---

## 📄 Licencia

Este proyecto está bajo la Licencia MIT. Ver el archivo `LICENSE` para más detalles.

---

## 👨‍💻 Autor

**[Tu Nombre]**
- Email: [tu-email@ejemplo.com]
- GitHub: [@tu-usuario](https://github.com/tu-usuario)

---

## 🙏 Agradecimientos

- Laravel Framework por la excelente base backend
- Vue.js por el framework frontend reactivo
- Face-API.js por hacer el reconocimiento facial accesible
- TailwindCSS por el sistema de diseño moderno
- La comunidad open source por las herramientas increíbles

---

<p align="center">
  Hecho con ❤️ para mejorar la gestión educativa
</p>

<p align="center">
  <strong>⭐ Si te gusta este proyecto, dale una estrella en GitHub ⭐</strong>
</p>
