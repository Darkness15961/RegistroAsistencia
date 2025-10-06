<template>
  <div class="flex-1 p-4 sm:p-6 overflow-x-hidden">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
      <div>
        <h1 class="text-3xl font-bold text-white mb-2">Usuarios del Sistema</h1>
        <p class="text-gray-400">Gestiona los usuarios con acceso al sistema de asistencias</p>
      </div>
      <button class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-xl transition-colors duration-200 flex items-center gap-2 shadow-lg">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
        </svg>
        Nuevo Usuario
      </button>
    </div>

    <!-- Grid de Cards de Usuarios -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
      <!-- Usuario 1 -->
      <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-6 hover:bg-white/10 transition-all duration-300 hover:shadow-2xl hover:scale-105">
        <div class="flex items-start justify-between mb-4">
          <div class="relative">
            <div class="w-16 h-16 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center">
              <span class="text-white text-xl font-bold">MB</span>
            </div>
            <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-green-500 border-2 border-gray-900 rounded-full"></div>
          </div>
          <span class="bg-blue-500/20 text-blue-300 text-xs px-3 py-1 rounded-full font-semibold">Administrador</span>
        </div>

        <div class="mb-4">
          <h3 class="text-white font-bold text-lg mb-1">María Benítez</h3>
          <p class="text-gray-400 text-sm">Directora</p>
        </div>

        <div class="space-y-2 mb-4">
          <div class="flex items-center gap-2 text-gray-300 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            <span>m.benitez@escuela.edu</span>
          </div>
          <div class="flex items-center gap-2 text-gray-300 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>Último acceso: Hoy, 09:15 AM</span>
          </div>
        </div>

        <div class="flex gap-2">
          <button class="flex-1 bg-white/10 hover:bg-white/20 text-white py-2 rounded-lg transition-colors text-sm font-medium">Ver Perfil</button>
          <button class="bg-white/10 hover:bg-white/20 text-white p-2 rounded-lg transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Usuario 2 -->
      <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-6 hover:bg-white/10 transition-all duration-300 hover:shadow-2xl hover:scale-105">
        <div class="flex items-start justify-between mb-4">
          <div class="relative">
            <div class="w-16 h-16 bg-gradient-to-br from-purple-400 to-purple-600 rounded-full flex items-center justify-center">
              <span class="text-white text-xl font-bold">JR</span>
            </div>
            <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-green-500 border-2 border-gray-900 rounded-full"></div>
          </div>
          <span class="bg-purple-500/20 text-purple-300 text-xs px-3 py-1 rounded-full font-semibold">Docente</span>
        </div>

        <div class="mb-4">
          <h3 class="text-white font-bold text-lg mb-1">Juan Rodríguez</h3>
          <p class="text-gray-400 text-sm">Profesor de Matemáticas</p>
        </div>

        <div class="space-y-2 mb-4">
          <div class="flex items-center gap-2 text-gray-300 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            <span>j.rodriguez@escuela.edu</span>
          </div>
          <div class="flex items-center gap-2 text-gray-300 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>Último acceso: Hoy, 08:30 AM</span>
          </div>
        </div>

        <div class="flex gap-2">
          <button class="flex-1 bg-white/10 hover:bg-white/20 text-white py-2 rounded-lg transition-colors text-sm font-medium">Ver Perfil</button>
          <button class="bg-white/10 hover:bg-white/20 text-white p-2 rounded-lg transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Usuario 3 -->
      <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-6 hover:bg-white/10 transition-all duration-300 hover:shadow-2xl hover:scale-105">
        <div class="flex items-start justify-between mb-4">
          <div class="relative">
            <div class="w-16 h-16 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center">
              <span class="text-white text-xl font-bold">AS</span>
            </div>
            <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-yellow-500 border-2 border-gray-900 rounded-full"></div>
          </div>
          <span class="bg-green-500/20 text-green-300 text-xs px-3 py-1 rounded-full font-semibold">Docente</span>
        </div>

        <div class="mb-4">
          <h3 class="text-white font-bold text-lg mb-1">Ana Silva</h3>
          <p class="text-gray-400 text-sm">Profesora de Historia</p>
        </div>

        <div class="space-y-2 mb-4">
          <div class="flex items-center gap-2 text-gray-300 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            <span>a.silva@escuela.edu</span>
          </div>
          <div class="flex items-center gap-2 text-gray-300 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>Último acceso: Ayer, 05:20 PM</span>
          </div>
        </div>

        <div class="flex gap-2">
          <button class="flex-1 bg-white/10 hover:bg-white/20 text-white py-2 rounded-lg transition-colors text-sm font-medium">Ver Perfil</button>
          <button class="bg-white/10 hover:bg-white/20 text-white p-2 rounded-lg transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Usuario 4 -->
      <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-6 hover:bg-white/10 transition-all duration-300 hover:shadow-2xl hover:scale-105">
        <div class="flex items-start justify-between mb-4">
          <div class="relative">
            <div class="w-16 h-16 bg-gradient-to-br from-orange-400 to-orange-600 rounded-full flex items-center justify-center">
              <span class="text-white text-xl font-bold">CP</span>
            </div>
            <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-gray-500 border-2 border-gray-900 rounded-full"></div>
          </div>
          <span class="bg-orange-500/20 text-orange-300 text-xs px-3 py-1 rounded-full font-semibold">Secretaría</span>
        </div>

        <div class="mb-4">
          <h3 class="text-white font-bold text-lg mb-1">Carlos Pérez</h3>
          <p class="text-gray-400 text-sm">Secretario Académico</p>
        </div>

        <div class="space-y-2 mb-4">
          <div class="flex items-center gap-2 text-gray-300 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            <span>c.perez@escuela.edu</span>
          </div>
          <div class="flex items-center gap-2 text-gray-300 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>Último acceso: Hace 2 días</span>
          </div>
        </div>

        <div class="flex gap-2">
          <button class="flex-1 bg-white/10 hover:bg-white/20 text-white py-2 rounded-lg transition-colors text-sm font-medium">Ver Perfil</button>
          <button class="bg-white/10 hover:bg-white/20 text-white p-2 rounded-lg transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Usuario 5 -->
      <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-6 hover:bg-white/10 transition-all duration-300 hover:shadow-2xl hover:scale-105">
        <div class="flex items-start justify-between mb-4">
          <div class="relative">
            <div class="w-16 h-16 bg-gradient-to-br from-red-400 to-red-600 rounded-full flex items-center justify-center">
              <span class="text-white text-xl font-bold">LM</span>
            </div>
            <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-green-500 border-2 border-gray-900 rounded-full"></div>
          </div>
          <span class="bg-red-500/20 text-red-300 text-xs px-3 py-1 rounded-full font-semibold">Psicología</span>
        </div>

        <div class="mb-4">
          <h3 class="text-white font-bold text-lg mb-1">Laura Medina</h3>
          <p class="text-gray-400 text-sm">Psicóloga Escolar</p>
        </div>

        <div class="space-y-2 mb-4">
          <div class="flex items-center gap-2 text-gray-300 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            <span>l.medina@escuela.edu</span>
          </div>
          <div class="flex items-center gap-2 text-gray-300 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>Último acceso: Hoy, 07:50 AM</span>
          </div>
        </div>

        <div class="flex gap-2">
          <button class="flex-1 bg-white/10 hover:bg-white/20 text-white py-2 rounded-lg transition-colors text-sm font-medium">Ver Perfil</button>
          <button class="bg-white/10 hover:bg-white/20 text-white p-2 rounded-lg transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Usuario 6 -->
      <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-6 hover:bg-white/10 transition-all duration-300 hover:shadow-2xl hover:scale-105">
        <div class="flex items-start justify-between mb-4">
          <div class="relative">
            <div class="w-16 h-16 bg-gradient-to-br from-teal-400 to-teal-600 rounded-full flex items-center justify-center">
              <span class="text-white text-xl font-bold">MP</span>
            </div>
            <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-green-500 border-2 border-gray-900 rounded-full"></div>
          </div>
          <span class="bg-teal-500/20 text-teal-300 text-xs px-3 py-1 rounded-full font-semibold">Docente</span>
        </div>

        <div class="mb-4">
          <h3 class="text-white font-bold text-lg mb-1">Miguel Paredes</h3>
          <p class="text-gray-400 text-sm">Profesor de Arte</p>
        </div>

        <div class="space-y-2 mb-4">
          <div class="flex items-center gap-2 text-gray-300 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            <span>m.paredes@escuela.edu</span>
          </div>
          <div class="flex items-center gap-2 text-gray-300 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>Último acceso: Hoy, 06:40 AM</span>
          </div>
        </div>

        <div class="flex gap-2">
          <button class="flex-1 bg-white/10 hover:bg-white/20 text-white py-2 rounded-lg transition-colors text-sm font-medium">Ver Perfil</button>
          <button class="bg-white/10 hover:bg-white/20 text-white p-2 rounded-lg transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Usuario 7 -->
      <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-6 hover:bg-white/10 transition-all duration-300 hover:shadow-2xl hover:scale-105">
        <div class="flex items-start justify-between mb-4">
          <div class="relative">
            <div class="w-16 h-16 bg-gradient-to-br from-pink-400 to-pink-600 rounded-full flex items-center justify-center">
              <span class="text-white text-xl font-bold">VG</span>
            </div>
            <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-green-500 border-2 border-gray-900 rounded-full"></div>
          </div>
          <span class="bg-pink-500/20 text-pink-300 text-xs px-3 py-1 rounded-full font-semibold">Tutora</span>
        </div>

        <div class="mb-4">
          <h3 class="text-white font-bold text-lg mb-1">Valeria Gómez</h3>
          <p class="text-gray-400 text-sm">Tutora de 5° Secundaria</p>
        </div>

        <div class="space-y-2 mb-4">
          <div class="flex items-center gap-2 text-gray-300 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            <span>v.gomez@alumnos.edu</span>
          </div>
          <div class="flex items-center gap-2 text-gray-300 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>Último acceso: Hoy, 10:00 AM</span>
          </div>
        </div>

        <div class="flex gap-2">
          <button class="flex-1 bg-white/10 hover:bg-white/20 text-white py-2 rounded-lg transition-colors text-sm font-medium">Ver Perfil</button>
          <button class="bg-white/10 hover:bg-white/20 text-white p-2 rounded-lg transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Usuario 8 -->
      <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-6 hover:bg-white/10 transition-all duration-300 hover:shadow-2xl hover:scale-105">
        <div class="flex items-start justify-between mb-4">
          <div class="relative">
            <div class="w-16 h-16 bg-gradient-to-br from-cyan-400 to-cyan-600 rounded-full flex items-center justify-center">
              <span class="text-white text-xl font-bold">AC</span>
            </div>
            <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-green-500 border-2 border-gray-900 rounded-full"></div>
          </div>
          <span class="bg-cyan-500/20 text-cyan-300 text-xs px-3 py-1 rounded-full font-semibold">Tutor</span>
        </div>

        <div class="mb-4">
          <h3 class="text-white font-bold text-lg mb-1">Andrés Chávez</h3>
          <p class="text-gray-400 text-sm">Tutor de 4° Primaria</p>
        </div>

        <div class="space-y-2 mb-4">
          <div class="flex items-center gap-2 text-gray-300 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            <span>a.chavez@alumnos.edu</span>
          </div>
          <div class="flex items-center gap-2 text-gray-300 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>Último acceso: Hoy, 09:45 AM</span>
          </div>
        </div>

        <div class="flex gap-2">
          <button class="flex-1 bg-white/10 hover:bg-white/20 text-white py-2 rounded-lg transition-colors text-sm font-medium">Ver Perfil</button>
          <button class="bg-white/10 hover:bg-white/20 text-white p-2 rounded-lg transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
            </svg>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
// Aquí puedes agregar la lógica para cargar usuarios desde tu API
</script>
