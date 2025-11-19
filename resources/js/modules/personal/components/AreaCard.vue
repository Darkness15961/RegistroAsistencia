<template>
  <div
    @click="$emit('click')"
    :class="[theme('card').value, 'transition-all duration-300 hover:shadow-2xl hover:scale-105 rounded-2xl p-6 cursor-pointer w-full group relative border']"
  >
    <div class="flex items-start justify-between mb-4">
      <div
        :class="['flex items-center justify-center w-14 h-14 rounded-2xl shadow text-2xl font-bold', getAreaStyle(nombre).gradient]"
      >
        <i :class="[getAreaStyle(nombre).icon, 'text-white']"></i>
      </div>
      <div class="px-3 py-1 rounded-full text-xs font-bold flex items-center"
           :class="isDark ? 'bg-white/10 text-gray-200' : 'bg-gray-100 text-gray-700'">
        <i class="fas fa-users mr-2"></i>
        <span>{{ cantidadPersonas }}</span>
      </div>
    </div>
    <div class="flex flex-col flex-1">
      <h3 :class="[theme('cardTitle').value,'font-bold mb-2 text-lg']">
        {{ nombre }}
      </h3>
      <p :class="[theme('cardSubtitle').value, 'text-sm']">
        {{ descripcion }}
      </p>
    </div>
  </div>
</template>

<script setup>
import { useTheme } from '@/composables/useTheme'

const { theme, isDark } = useTheme()

defineProps({
  nombre: String,
  descripcion: String,
  cantidadPersonas: Number,
})
defineEmits(['click'])

// Catálogo de estilos con los iconos y gradientes de tus imágenes
const styleCatalog = {
  'Dirección':            { gradient: 'bg-gradient-to-br from-green-400 to-emerald-600', icon: 'fas fa-building' },
  'Administración':       { gradient: 'bg-gradient-to-br from-pink-400 to-pink-600',    icon: 'fas fa-briefcase' },
  'Docentes de Primaria': { gradient: 'bg-gradient-to-br from-orange-400 to-orange-600', icon: 'fas fa-chalkboard-teacher' },
  'Docentes de Secundaria': { gradient: 'bg-gradient-to-br from-violet-400 to-indigo-500',  icon: 'fas fa-user-graduate' },
  'Tutoría y Psicología': { gradient: 'bg-gradient-to-br from-red-400 to-pink-500',   icon: 'fas fa-hands-helping' },
  'Mantenimiento y Limpieza': { gradient: 'bg-gradient-to-br from-gray-400 to-gray-700',   icon: 'fas fa-broom' },
  'Seguridad':            { gradient: 'bg-gradient-to-br from-cyan-400 to-cyan-600',     icon: 'fas fa-shield-alt' },
  'Biblioteca':           { gradient: 'bg-gradient-to-br from-yellow-400 to-amber-500', icon: 'fas fa-book' },
  'Laboratorio':          { gradient: 'bg-gradient-to-br from-green-400 to-lime-500',   icon: 'fas fa-flask' },
  'Coordinación Académica': { gradient: 'bg-gradient-to-br from-teal-400 to-cyan-500', icon: 'fas fa-graduation-cap' },
  'Servicio Médico':      { gradient: 'bg-gradient-to-br from-red-400 to-rose-500',     icon: 'fas fa-medkit' },
  // Agregamos los de Alumnos por si acaso, aunque aquí no se usen
  'Alumnos de Inicial':   { gradient: 'bg-gradient-to-br from-emerald-400 to-green-500', icon: 'fas fa-child' },
  'Alumnos de Primaria':  { gradient: 'bg-gradient-to-br from-blue-400 to-sky-500',     icon: 'fas fa-book-reader' },
  'Alumnos de Secundaria':{ gradient: 'bg-gradient-to-br from-purple-400 to-fuchsia-500', icon: 'fas fa-users' },
};

const defaultStyle = { gradient: 'bg-gradient-to-br from-slate-400 to-slate-600', icon: 'fas fa-building' }; // Icono por defecto

const getAreaStyle = (nombre) => {
  const style = styleCatalog[nombre] ?? defaultStyle;
  return {
    ...style,
    icon: `${style.icon} text-white`
  };
};
</script>