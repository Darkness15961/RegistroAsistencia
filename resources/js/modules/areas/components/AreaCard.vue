<template>
  <div 
    class="backdrop-blur-xl rounded-2xl p-5 transition-all duration-300 border cursor-pointer group hover:scale-105"
    :class="theme('card').value"
    @click="$emit('click')"
  >
    <div class="flex items-start justify-between gap-4">
      <div>
        <div 
          class="w-12 h-12 rounded-lg flex items-center justify-center mb-3 shadow-lg"
          :class="iconGradient"
        >
          <i :class="[getAreaStyle(area.nombre_area).icon, 'text-white text-xl']"></i>
        </div>
        <h3 
          class="font-bold text-lg group-hover:text-blue-400 transition-colors"
          :class="theme('cardTitle').value"
        >
          {{ area.nombre_area }}
        </h3>
        <p 
          class="text-sm"
          :class="theme('cardSubtitle').value"
        >
          {{ area.descripcion || 'Sin descripción' }}
        </p>
      </div>
      
      <div class="flex flex-col items-end">
        <div class="px-3 py-1 rounded-full text-xs font-bold flex items-center mb-2"
           :class="isDark ? 'bg-white/10 text-gray-200' : 'bg-gray-100 text-gray-700'">
          <i class="fas fa-layer-group mr-2"></i>
          <span>{{ cantidadGrupos }}</span>
        </div>
        
        <div class="flex gap-2">
          <button 
            class="p-2 w-10 h-10 rounded-xl transition-all hover:scale-110 group"
            :class="isDark 
              ? 'bg-blue-500/20 hover:bg-blue-500/30' 
              : 'bg-blue-50 hover:bg-blue-100'"
            title="Editar"
            @click.stop="$emit('editar', area.id_area)"
          >
            <i class="fas fa-edit" :class="isDark ? 'text-blue-300' : 'text-blue-600'"></i>
          </button>
          <button 
            class="p-2 w-10 h-10 rounded-xl transition-all hover:scale-110 group"
            :class="isDark 
              ? 'bg-red-500/20 hover:bg-red-500/30' 
              : 'bg-red-50 hover:bg-red-100'"
            title="Eliminar"
            @click.stop="$emit('eliminar', area.id_area)"
          >
            <i class="fas fa-trash" :class="isDark ? 'text-red-300' : 'text-red-600'"></i>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useTheme } from '@/composables/useTheme'

const { theme, isDark } = useTheme() // Importamos isDark para los botones

const props = defineProps({ 
  area: {
    type: Object,
    required: true
  },
  cantidadGrupos: {
    type: Number,
    default: 0
  }
})

defineEmits(['editar', 'eliminar', 'click'])

// Lógica de iconos y gradientes (de la plantilla de Empleados/Áreas)
const styleCatalog = {
  'Dirección':            { gradient: 'bg-gradient-to-br from-green-400 to-emerald-600', icon: 'fas fa-building' },
  'Administración':       { gradient: 'bg-gradient-to-br from-pink-400 to-pink-600',    icon: 'fas fa-briefcase' },
  'Docentes de Primaria': { gradient: 'bg-gradient-to-br from-orange-400 to-orange-600', icon: 'fas fa-chalkboard-teacher' },
  'Docentes de Secundaria': { gradient: 'bg-gradient-to-br from-violet-400 to-indigo-500',  icon: 'fas fa-user-graduate' },
  'Alumnos de Inicial':   { gradient: 'bg-gradient-to-br from-emerald-400 to-green-500', icon: 'fas fa-child' },
  'Alumnos de Primaria':  { gradient: 'bg-gradient-to-br from-blue-400 to-sky-500',     icon: 'fas fa-book-reader' },
  'Alumnos de Secundaria':{ gradient: 'bg-gradient-to-br from-purple-400 to-fuchsia-500', icon: 'fas fa-users' },
  'Tutoría y Psicología': { gradient: 'bg-gradient-to-br from-red-400 to-pink-500',   icon: 'fas fa-hands-helping' },
  'Mantenimiento y Limpieza': { gradient: 'bg-gradient-to-br from-gray-400 to-gray-700',   icon: 'fas fa-broom' },
  'Seguridad':            { gradient: 'bg-gradient-to-br from-cyan-400 to-cyan-600',     icon: 'fas fa-shield-alt' },
  'Biblioteca':           { gradient: 'bg-gradient-to-br from-yellow-400 to-amber-500', icon: 'fas fa-book' },
  'Laboratorio':          { gradient: 'bg-gradient-to-br from-green-400 to-lime-500',   icon: 'fas fa-flask' },
  'Coordinación Académica': { gradient: 'bg-gradient-to-br from-teal-400 to-cyan-500', icon: 'fas fa-graduation-cap' },
  'Servicio Médico':      { gradient: 'bg-gradient-to-br from-red-400 to-rose-500',     icon: 'fas fa-medkit' },
  'Juegos':               { gradient: 'bg-gradient-to-br from-indigo-400 to-purple-500', icon: 'fas fa-gamepad' },
  'Prueba':               { gradient: 'bg-gradient-to-br from-gray-400 to-gray-500',     icon: 'fas fa-vial' },
};
const defaultStyle = { gradient: 'bg-gradient-to-br from-slate-400 to-slate-600', icon: 'fas fa-university' }

const getAreaStyle = (nombre) => {
  const style = styleCatalog[nombre] ?? defaultStyle;
  return {
    ...style,
    icon: `${style.icon} text-white`
  };
};

const iconGradient = computed(() => getAreaStyle(props.area.nombre_area).gradient)
</script>