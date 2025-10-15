<template>
  <div class="flex-1 p-4 sm:p-6 overflow-x-hidden">
    <!-- Header -->
    <PageHeader
      title="Usuarios del Sistema"
      subtitle="Gestiona los usuarios con acceso al sistema de asistencias"
      :show-button="true"
      button-text="Nuevo Usuario"
      @button-click="handleNewUser"
    />

    <!-- Grid de Cards de Usuarios -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
      <UserCard
        v-for="user in users"
        :key="user.id"
        :user="user"
        @view-profile="handleViewProfile"
        @open-menu="handleOpenMenu"
      />
    </div>

    <!-- Estado vacío (opcional) -->
    <div 
      v-if="users.length === 0" 
      class="text-center py-12"
      :class="theme('cardSubtitle').value"
    >
      <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
      </svg>
      <p class="text-lg">No hay usuarios registrados</p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import UserCard from '../components/UserCard.vue'
import PageHeader from '../components/PageHeader.vue'
import { useTheme } from '../composables/useTheme'

const { theme } = useTheme()

// Datos de usuarios (puedes reemplazar esto con una llamada a API)
const users = ref([
  {
    id: 1,
    name: 'María Benítez',
    initials: 'MB',
    position: 'Directora',
    email: 'm.benitez@escuela.edu',
    lastAccess: 'Hoy, 09:15 AM',
    role: 'Administrador',
    status: 'online',
    avatarGradient: 'bg-gradient-to-br from-blue-400 to-blue-600'
  },
  {
    id: 2,
    name: 'Juan Rodríguez',
    initials: 'JR',
    position: 'Profesor de Matemáticas',
    email: 'j.rodriguez@escuela.edu',
    lastAccess: 'Hoy, 08:30 AM',
    role: 'Docente',
    status: 'online',
    avatarGradient: 'bg-gradient-to-br from-purple-400 to-purple-600'
  },
  {
    id: 3,
    name: 'Ana Silva',
    initials: 'AS',
    position: 'Profesora de Historia',
    email: 'a.silva@escuela.edu',
    lastAccess: 'Ayer, 05:20 PM',
    role: 'Docente',
    status: 'away',
    avatarGradient: 'bg-gradient-to-br from-green-400 to-green-600'
  },
  {
    id: 4,
    name: 'Carlos Pérez',
    initials: 'CP',
    position: 'Secretario Académico',
    email: 'c.perez@escuela.edu',
    lastAccess: 'Hace 2 días',
    role: 'Secretaría',
    status: 'offline',
    avatarGradient: 'bg-gradient-to-br from-orange-400 to-orange-600'
  },
  {
    id: 5,
    name: 'Laura Medina',
    initials: 'LM',
    position: 'Psicóloga Escolar',
    email: 'l.medina@escuela.edu',
    lastAccess: 'Hoy, 07:50 AM',
    role: 'Psicología',
    status: 'online',
    avatarGradient: 'bg-gradient-to-br from-red-400 to-red-600'
  },
  {
    id: 6,
    name: 'Miguel Paredes',
    initials: 'MP',
    position: 'Profesor de Arte',
    email: 'm.paredes@escuela.edu',
    lastAccess: 'Hoy, 06:40 AM',
    role: 'Docente',
    status: 'online',
    avatarGradient: 'bg-gradient-to-br from-teal-400 to-teal-600'
  },
  {
    id: 7,
    name: 'Valeria Gómez',
    initials: 'VG',
    position: 'Tutora de 5° Secundaria',
    email: 'v.gomez@alumnos.edu',
    lastAccess: 'Hoy, 10:00 AM',
    role: 'Tutora',
    status: 'online',
    avatarGradient: 'bg-gradient-to-br from-pink-400 to-pink-600'
  },
  {
    id: 8,
    name: 'Andrés Chávez',
    initials: 'AC',
    position: 'Tutor de 4° Primaria',
    email: 'a.chavez@alumnos.edu',
    lastAccess: 'Hoy, 09:45 AM',
    role: 'Tutor',
    status: 'online',
    avatarGradient: 'bg-gradient-to-br from-cyan-400 to-cyan-600'
  }
])

// Handlers
const handleNewUser = () => {
  console.log('Crear nuevo usuario')
  // Aquí puedes abrir un modal o navegar a un formulario
}

const handleViewProfile = (user) => {
  console.log('Ver perfil de:', user.name)
  // Aquí puedes navegar al perfil del usuario
}

const handleOpenMenu = (user) => {
  console.log('Abrir menú de:', user.name)
  // Aquí puedes abrir un menú contextual
}
</script>