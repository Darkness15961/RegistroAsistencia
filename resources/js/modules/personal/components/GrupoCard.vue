<template>
  <div 
    class="backdrop-blur-xl border rounded-3xl shadow-lg p-5 transition-all duration-300 hover:shadow-2xl cursor-pointer group relative overflow-hidden transform hover:scale-[1.02]"
    :class="theme('card').value"
    @click="$emit('click')"
  >
    <div class="absolute top-0 left-0 w-1 h-full bg-gradient-to-b from-blue-400 to-purple-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

    <div class="flex justify-between items-start mb-4 pl-3">
      
      <div class="overflow-hidden">
        <h3 class="text-lg font-bold leading-tight mb-1 truncate" :class="theme('cardTitle').value" :title="grupo.nivel || grupo.grado || 'Sin nombre'">
            {{ grupo.nivel || grupo.grado || 'Grupo sin nombre' }}
        </h3>
        <p class="text-xs uppercase tracking-wider font-semibold opacity-60 truncate" :class="theme('cardSubtitle').value">
          {{ grupo.grado && grupo.nivel ? grupo.grado : (grupo.area?.nombre_area || 'Sin área') }}
        </p>
      </div>
      
      <div 
        class="flex-shrink-0 flex items-center justify-center w-10 h-10 rounded-2xl font-bold text-sm shadow-md text-white"
        :class="badgeClasses"
      >
        {{ cantidadPersonas }}
      </div>
    </div>

    <div class="pl-3 mt-4 pt-3 border-t border-dashed" :class="isDark ? 'border-white/10' : 'border-gray-200'">
        <div class="flex items-center justify-between">
            <p class="text-[10px] uppercase tracking-widest mb-2 opacity-50 font-bold" :class="theme('cardSubtitle').value">
                Encargado / Tutor
            </p>
            <span 
                v-if="grupo.tutor"
                class="text-[10px] px-2 py-0.5 rounded-full font-bold opacity-80"
                :class="getTutorRoleClasses()"
            >
                {{ grupo.tutor.tipo_persona === 'empleado' ? 'Staff' : 'Monitor' }}
            </span>
        </div>
        
        <div class="flex items-center gap-3">
            <div 
              class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold text-white shadow-sm flex-shrink-0"
              :class="getAvatarGradient(grupo.tutor?.id_persona)"
            >
                {{ getInitials(grupo.tutor?.nombre_completo) }}
            </div>
            
            <div class="overflow-hidden">
              <span 
                class="text-sm font-medium truncate block" 
                :class="[
                  theme('cardTitle').value, 
                  !grupo.tutor && 'opacity-50 italic'
                ]"
              >
                  {{ grupo.tutor?.nombre_completo || 'Sin asignar' }}
              </span>
              <span v-if="grupo.tutor?.cargo_grado" class="text-[10px] block truncate opacity-60" :class="theme('cardSubtitle').value">
                  {{ grupo.tutor.cargo_grado }}
              </span>
            </div>
        </div>
    </div>

  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useTheme } from '@/composables/useTheme' 

const { theme, isDark } = useTheme()

const props = defineProps({
  grupo: { type: Object, required: true },
  cantidadPersonas: { type: Number, default: 0 }
})

const nombreGrupo = computed(() => {
    return props.grupo.grado || props.grupo.area?.nombre_area || 'Grupo Sin Nombre'
})

// --- LÓGICA DE COLORES ---

// Lista de gradientes para avatares y el badge (igual que en Home)
const gradients = [
  'bg-gradient-to-tr from-blue-400 to-blue-600',
  'bg-gradient-to-tr from-purple-400 to-purple-600',
  'bg-gradient-to-tr from-green-400 to-green-600',
  'bg-gradient-to-tr from-orange-400 to-orange-600',
  'bg-gradient-to-tr from-red-400 to-red-600',
  'bg-gradient-to-tr from-pink-400 to-pink-600',
  'bg-gradient-to-tr from-cyan-400 to-cyan-600',
  'bg-gradient-to-tr from-teal-400 to-teal-600',
  'bg-gradient-to-tr from-indigo-400 to-indigo-600'
]

// *** LÓGICA DE BADGE SIMPLIFICADA ***

const getAvatarGradient = (id) => {
  if (!id) return 'bg-gray-300 dark:bg-gray-700 text-gray-500 dark:text-gray-400'
  return gradients[id % gradients.length]
}

/**
 * Devuelve la clase de gradiente a aplicar en el badge.
 */
const getBadgeBackground = (id) => {
    let index = 0;
    if (id && typeof id === 'number' && id > 0) {
        index = id % gradients.length;
    }
    
    // Usamos el gradiente de la lista de colores para el fondo del badge
    return gradients[index] || gradients[0];
}

const badgeClasses = computed(() => {
    // Retorna la clase de gradiente para el fondo del badge
    return getBadgeBackground(props.grupo.id_grupo);
})

const getInitials = (name) => {
    if (!name) return '?'
    return name.substring(0, 1).toUpperCase()
}

// --- FUNCIÓN DE ROL (Se mantiene) ---

/**
 * Devuelve las clases de color para el badge del rol del tutor.
 */
const getTutorRoleClasses = () => {
    if (!props.grupo.tutor) return '';
    
    const isEmployee = props.grupo.tutor.tipo_persona === 'empleado';
    
    // Clases basadas en el rol
    if (isEmployee) {
        // Staff/Empleado (Verde)
        return 'bg-green-200 text-green-800 dark:bg-green-800 dark:text-green-200';
    } else {
        // Monitor/Estudiante (Naranja)
        return 'bg-orange-200 text-orange-800 dark:bg-orange-800 dark:text-orange-200';
    }
}
</script>