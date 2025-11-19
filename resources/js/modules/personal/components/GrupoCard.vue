<template>
  <div 
    class="backdrop-blur-xl border rounded-3xl shadow-lg p-5 transition-all duration-300 hover:shadow-xl cursor-pointer group relative overflow-hidden"
    :class="theme('card').value"
    @click="$emit('click')"
  >
    <div class="absolute top-0 left-0 w-1 h-full bg-gradient-to-b from-blue-400 to-purple-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

    <div class="flex justify-between items-start mb-4 pl-3">
      <div class="overflow-hidden">
        <h3 class="text-lg font-bold leading-tight mb-1 truncate" :class="theme('cardTitle').value" :title="nombreGrupo">
            {{ nombreGrupo }}
        </h3>
        <p class="text-xs uppercase tracking-wider font-semibold opacity-60 truncate" :class="theme('cardSubtitle').value">
          {{ grupo.nivel || grupo.area?.nombre_area || 'General' }}
        </p>
      </div>
      
      <div 
        class="flex-shrink-0 flex items-center justify-center w-10 h-10 rounded-2xl font-bold text-sm shadow-sm border"
        :class="badgeClasses"
      >
        {{ cantidadPersonas }}
      </div>
      </div>

    <div class="pl-3 mt-4 pt-3 border-t border-dashed" :class="isDark ? 'border-white/10' : 'border-gray-200'">
        <p class="text-[10px] uppercase tracking-widest mb-2 opacity-50 font-bold" :class="theme('cardSubtitle').value">
            Encargado / Tutor
        </p>
        
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

// Clases de colores para el badge: [clase_claro_bg_border, clase_oscuro_bg_border, clase_texto]
const badgeColorClasses = [
    ['bg-blue-50 border-blue-100', 'dark:bg-blue-900/20 dark:border-blue-800', 'text-blue-600 dark:text-blue-400'],
    ['bg-purple-50 border-purple-100', 'dark:bg-purple-900/20 dark:border-purple-800', 'text-purple-600 dark:text-purple-400'],
    ['bg-green-50 border-green-100', 'dark:bg-green-900/20 dark:border-green-800', 'text-green-600 dark:text-green-400'],
    ['bg-orange-50 border-orange-100', 'dark:bg-orange-900/20 dark:border-orange-800', 'text-orange-600 dark:text-orange-400'],
    ['bg-red-50 border-red-100', 'dark:bg-red-900/20 dark:border-red-800', 'text-red-600 dark:text-red-400'],
    ['bg-cyan-50 border-cyan-100', 'dark:bg-cyan-900/20 dark:border-cyan-800', 'text-cyan-600 dark:text-cyan-400'],
    ['bg-indigo-50 border-indigo-100', 'dark:bg-indigo-900/20 dark:border-indigo-800', 'text-indigo-600 dark:text-indigo-400'],
    ['bg-pink-50 border-pink-100', 'dark:bg-pink-900/20 dark:border-pink-800', 'text-pink-600 dark:text-pink-400'],
]

const getAvatarGradient = (id) => {
  if (!id) return 'bg-gray-300 dark:bg-gray-700 text-gray-500 dark:text-gray-400'
  return gradients[id % gradients.length]
}

// ** FUNCIÓN CORREGIDA para MODO CLARO/OSCURO **
const getBadgeColorClasses = () => {
    let index = 0;
    if (props.grupo.id_grupo && typeof props.grupo.id_grupo === 'number' && props.grupo.id_grupo > 0) {
        index = props.grupo.id_grupo % badgeColorClasses.length;
    }

    // Desestructura las clases: [Claro Fondo/Borde, Oscuro Fondo/Borde, Texto]
    const [bgLight, bgDark, text] = badgeColorClasses[index] || badgeColorClasses[0];

    // Selecciona el fondo/borde correcto basado en el modo
    const backgroundClass = isDark.value ? bgDark : bgLight;

    // Devuelve el array de clases para ser unido por badgeClasses
    return [backgroundClass, text];
}

const badgeClasses = computed(() => {
    return getBadgeColorClasses().join(' ')
})

const getInitials = (name) => {
    if (!name) return '?'
    return name.substring(0, 1).toUpperCase()
}
</script>