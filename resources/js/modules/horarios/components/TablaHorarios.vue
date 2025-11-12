<template>
  <div>
    <!-- 1. Tabla (Desktop) -->
    <div 
      class="hidden md:block backdrop-blur-xl border rounded-3xl overflow-hidden shadow-lg"
      :class="theme('card').value"
    >
      <div class="overflow-x-auto">
        <table class="w-full text-left">
          <thead :class="theme('tableHeader').value">
            <tr class="border-b" :class="isDark ? 'border-white/20' : 'border-gray-200'">
              <th class="px-6 py-4 font-bold text-sm uppercase" :class="theme('cardSubtitle').value">Área / Nivel</th>
              <th class="px-6 py-4 font-bold text-sm uppercase" :class="theme('cardSubtitle').value">Entrada</th>
              <th class="px-6 py-4 font-bold text-sm uppercase" :class="theme('cardSubtitle').value">Salida</th>
              <th class="px-6 py-4 font-bold text-sm uppercase" :class="theme('cardSubtitle').value">Turno</th>
              <th class="text-center px-6 py-4 font-bold text-sm uppercase" :class="theme('cardSubtitle').value">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="horarios.length === 0">
              <td colspan="5" class="text-center p-8" :class="theme('cardSubtitle').value">
                <i class="fas fa-calendar text-5xl mb-3 opacity-50"></i>
                <p>No se encontraron horarios</p>
              </td>
            </tr>
            <tr 
              v-for="(horario, index) in horarios" 
              :key="horario.id_horario"
              class="border-b transition-all"
              :class="[
                theme('tableRow').value,
                isDark ? 'border-white/5' : 'border-gray-100',
                index % 2 === 0 ? (isDark ? 'bg-white/[0.03]' : 'bg-gray-50/50') : ''
              ]"
            >
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <div 
                    :class="[
                      'w-10 h-10 rounded-xl flex items-center justify-center shadow-lg',
                      'bg-gradient-to-br text-white font-bold',
                      getAreaStyle(horario.area?.nombre_area || '').gradient
                    ]"
                  >
                    <i :class="getAreaStyle(horario.area?.nombre_area || '').icon"></i>
                  </div>
                  <span class="font-semibold" :class="theme('cardTitle').value">{{ horario.area?.nombre_area || 'N/A' }}</span>
                </div>
              </td>
              <td class="px-6 py-4">
                <div class="border px-3 py-1.5 rounded-lg inline-flex items-center gap-2" :class="isDark ? 'bg-green-500/20 border-green-500/30' : 'bg-green-50 border-green-200'">
                  <i class="fas fa-clock text-sm" :class="isDark ? 'text-green-300' : 'text-green-600'"></i>
                  <span class="font-mono font-semibold text-sm" :class="isDark ? 'text-green-200' : 'text-green-700'">{{ horario.hora_entrada }}</span>
                </div>
              </td>
              <td class="px-6 py-4">
                <div class="border px-3 py-1.5 rounded-lg inline-flex items-center gap-2" :class="isDark ? 'bg-red-500/20 border-red-500/30' : 'bg-red-50 border-red-200'">
                  <i class="fas fa-clock text-sm" :class="isDark ? 'text-red-300' : 'text-red-600'"></i>
                  <span class="font-mono font-semibold text-sm" :class="isDark ? 'text-red-200' : 'text-red-700'">{{ horario.hora_salida }}</span>
                </div>
              </td>
              <td class="px-6 py-4">
                <div class="backdrop-blur-sm border px-3 py-1.5 rounded-xl inline-flex items-center gap-2" :class="isDark ? 'bg-white/10 border-white/20' : 'bg-gray-100 border-gray-200'">
                  <i :class="getTurnoIcon(horario.turno)"></i>
                  <span class="font-medium text-sm capitalize" :class="theme('cardTitle').value">{{ horario.turno }}</span>
                </div>
              </td>
              <td class="px-6 py-4">
                <div class="flex justify-center gap-2">
                  <button @click="$emit('editar', horario)" class="p-2 w-10 h-10 rounded-xl transition-all hover:scale-110 group" :class="isDark ? 'bg-blue-500/20 hover:bg-blue-500/30' : 'bg-blue-50 hover:bg-blue-100'" title="Editar">
                    <i class="fas fa-edit" :class="isDark ? 'text-blue-300' : 'text-blue-600'"></i>
                  </button>
                  <button @click="$emit('eliminar', horario.id_horario)" class="p-2 w-10 h-10 rounded-xl transition-all hover:scale-110 group" :class="isDark ? 'bg-red-500/20 hover:bg-red-500/30' : 'bg-red-50 hover:bg-red-100'" title="Eliminar">
                    <i class="fas fa-trash" :class="isDark ? 'text-red-300' : 'text-red-600'"></i>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- 2. Tarjetas (Móvil) -->
    <div class="grid gap-4 md:hidden">
      <div v-if="horarios.length === 0" class="text-center py-12 rounded-xl border" :class="[theme('card').value, theme('cardSubtitle').value]">
        <i class="fas fa-search text-5xl mb-4 opacity-50"></i>
        <p class="text-lg">No se encontraron horarios</p>
      </div>
      <div
        v-for="horario in horarios"
        :key="horario.id_horario"
        class="backdrop-blur-xl border rounded-3xl shadow-lg p-5"
        :class="theme('card').value"
      >
        <div class="flex items-start justify-between gap-3 mb-4">
          <div class="flex items-center gap-3">
            <div :class="['w-12 h-12 rounded-2xl flex items-center justify-center shadow-lg bg-gradient-to-br text-white font-bold', getAreaStyle(horario.area?.nombre_area || '').gradient]">
              <i :class="getAreaStyle(horario.area?.nombre_area || '').icon"></i>
            </div>
            <div>
              <h2 class="text-lg font-semibold" :class="theme('cardTitle').value">{{ horario.area?.nombre_area || 'N/A' }}</h2>
              <div class="backdrop-blur-sm border px-3 py-1.5 rounded-xl inline-flex items-center gap-2 mt-1" :class="isDark ? 'bg-white/10 border-white/20' : 'bg-gray-100 border-gray-200'">
                <i :class="getTurnoIcon(horario.turno)"></i>
                <span class="font-medium text-sm capitalize" :class="theme('cardTitle').value">{{ horario.turno }}</span>
              </div>
            </div>
          </div>
          <div class="flex gap-2">
            <button @click="$emit('editar', horario)" class="p-2 w-10 h-10 rounded-xl transition-all group" :class="isDark ? 'bg-blue-500/20' : 'bg-blue-50'" title="Editar">
              <i class="fas fa-edit" :class="isDark ? 'text-blue-300' : 'text-blue-600'"></i>
            </button>
            <button @click="$emit('eliminar', horario.id_horario)" class="p-2 w-10 h-10 rounded-xl transition-all group" :class="isDark ? 'bg-red-500/20' : 'bg-red-50'" title="Eliminar">
              <i class="fas fa-trash" :class="isDark ? 'text-red-300' : 'text-red-600'"></i>
            </button>
          </div>
        </div>
        <div class="flex flex-wrap items-center justify-between gap-3 pt-3 border-t" :class="isDark ? 'border-white/10' : 'border-gray-200'">
          <div class="border px-3 py-1.5 rounded-lg inline-flex items-center gap-2" :class="isDark ? 'bg-green-500/20 border-green-500/30' : 'bg-green-50 border-green-200'">
            <i class="fas fa-arrow-right-to-bracket text-sm" :class="isDark ? 'text-green-300' : 'text-green-600'"></i>
            <span class="font-mono font-semibold text-sm" :class="isDark ? 'text-green-200' : 'text-green-700'">{{ horario.hora_entrada }}</span>
          </div>
          <div class="border px-3 py-1.5 rounded-lg inline-flex items-center gap-2" :class="isDark ? 'bg-red-500/20 border-red-500/30' : 'bg-red-50 border-red-200'">
            <i class="fas fa-arrow-right-from-bracket text-sm" :class="isDark ? 'text-red-300' : 'text-red-600'"></i>
            <span class="font-mono font-semibold text-sm" :class="isDark ? 'text-red-200' : 'text-red-700'">{{ horario.hora_salida }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useTheme } from '@/composables/useTheme'

const { theme, isDark } = useTheme()

defineProps({
  horarios: {
    type: Array,
    required: true
  }
})

defineEmits(['editar', 'eliminar'])

const getTurnoIcon = (turno) => {
  const base = 'text-sm';
  if (turno === 'mañana') return `${base} fas fa-cloud-sun ${isDark.value ? 'text-sky-300' : 'text-sky-600'}`;
  if (turno === 'tarde') return `${base} fas fa-sun ${isDark.value ? 'text-amber-300' : 'text-amber-500'}`;
  if (turno === 'noche') return `${base} fas fa-moon ${isDark.value ? 'text-indigo-300' : 'text-indigo-500'}`;
  return `${base} fas fa-circle-notch ${isDark.value ? 'text-slate-300' : 'text-slate-500'}`;
};

// Catálogo de estilos para iconos de área (similar al de la plantilla)
const styleCatalog = {
  'Dirección':            { gradient: 'from-indigo-400 to-indigo-600', icon: 'fas fa-building' },
  'Administración':       { gradient: 'from-cyan-400 to-blue-500',    icon: 'fas fa-briefcase' },
  'Docentes de Primaria': { gradient: 'from-amber-400 to-orange-500', icon: 'fas fa-chalkboard-teacher' },
  'Docentes de Secundaria': { gradient: 'from-pink-400 to-rose-500',  icon: 'fas fa-user-graduate' },
  'Alumnos de Inicial':   { gradient: 'from-emerald-400 to-green-500', icon: 'fas fa-child' },
  'Alumnos de Primaria':  { gradient: 'from-blue-400 to-sky-500',     icon: 'fas fa-book-reader' },
  'Alumnos de Secundaria':{ gradient: 'from-purple-400 to-fuchsia-500', icon: 'fas fa-users' },
  'Tutoría y Psicología': { gradient: 'from-teal-400 to-green-500',   icon: 'fas fa-hands-helping' },
  'Seguridad':            { gradient: 'from-red-400 to-rose-500',     icon: 'fas fa-shield-alt' },
  'Biblioteca':           { gradient: 'from-yellow-400 to-amber-500', icon: 'fas fa-book' },
}
const defaultStyle = { gradient: 'from-slate-400 to-slate-600', icon: 'fas fa-university' }
const getAreaStyle = (nombre) => styleCatalog[nombre] ?? defaultStyle;
</script>