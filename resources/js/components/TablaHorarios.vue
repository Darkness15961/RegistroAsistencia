<template>
  <div>
    <div 
      class="hidden md:block backdrop-blur-xl border rounded-3xl overflow-hidden shadow-2xl"
      :class="theme('card').value"
    >
      <div class="overflow-x-auto">
        <table class="w-full text-left">
          <thead :class="theme('tableHeader').value">
            <tr class="border-b" :class="isDark ? 'border-white/20' : 'border-gray-200'">
              <th class="px-6 py-4 font-bold text-sm" :class="theme('cardSubtitle').value">ID</th>
              <th class="px-6 py-4 font-bold text-sm" :class="theme('cardSubtitle').value">Área / Nivel</th>
              <th class="px-6 py-4 font-bold text-sm" :class="theme('cardSubtitle').value">Entrada</th>
              <th class="px-6 py-4 font-bold text-sm" :class="theme('cardSubtitle').value">Salida</th>
              <th class="px-6 py-4 font-bold text-sm" :class="theme('cardSubtitle').value">Turno</th>
              <th class="text-center px-6 py-4 font-bold text-sm" :class="theme('cardSubtitle').value">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="horarios.length === 0">
              <td colspan="6" class="text-center p-8" :class="theme('cardSubtitle').value">
                <i class="fas fa-calendar text-5xl mb-3 opacity-50"></i>
                <p>No se encontraron horarios</p>
              </td>
            </tr>
            <tr 
              v-for="(horario, index) in horarios" 
              :key="horario.id"
              class="border-b transition-all"
              :class="[
                theme('tableRow').value,
                isDark ? 'border-white/5' : 'border-gray-100',
                index % 2 === 0 ? (isDark ? 'bg-white/[0.03]' : 'bg-gray-50/50') : ''
              ]"
            >
              <td class="px-6 py-4">
                <span class="font-mono text-sm" :class="theme('cardSubtitle').value">{{ horario.id }}</span>
              </td>
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <div 
                    :class="[
                      'w-10 h-10 rounded-xl flex items-center justify-center shadow-lg',
                      'bg-gradient-to-br text-white font-bold',
                      horario.color
                    ]"
                  >
                    {{ horario.area.substring(0, 2).toUpperCase() }}
                  </div>
                  <span class="font-semibold" :class="theme('cardTitle').value">{{ horario.area }}</span>
                </div>
              </td>
              <td class="px-6 py-4">
                <div class="border px-3 py-1.5 rounded-lg inline-flex items-center gap-2" :class="isDark ? 'bg-green-500/20 border-green-500/30' : 'bg-green-50 border-green-200'">
                  <i class="fas fa-clock text-sm" :class="isDark ? 'text-green-300' : 'text-green-600'"></i>
                  <span class="font-mono font-semibold text-sm" :class="isDark ? 'text-green-200' : 'text-green-700'">{{ horario.entrada }}</span>
                </div>
              </td>
              <td class="px-6 py-4">
                <div class="border px-3 py-1.5 rounded-lg inline-flex items-center gap-2" :class="isDark ? 'bg-red-500/20 border-red-500/30' : 'bg-red-50 border-red-200'">
                  <i class="fas fa-clock text-sm" :class="isDark ? 'text-red-300' : 'text-red-600'"></i>
                  <span class="font-mono font-semibold text-sm" :class="isDark ? 'text-red-200' : 'text-red-700'">{{ horario.salida }}</span>
                </div>
              </td>
              <td class="px-6 py-4">
                <div class="backdrop-blur-sm border px-3 py-1.5 rounded-xl inline-flex items-center gap-2" :class="isDark ? 'bg-white/10 border-white/20' : 'bg-gray-100 border-gray-200'">
                  <i :class="getTurnoIcon(horario.turno)"></i>
                  <span class="font-medium text-sm" :class="theme('cardTitle').value">{{ horario.turno }}</span>
                </div>
              </td>
              <td class="px-6 py-4">
                <div class="flex justify-center gap-2">
                  <button @click="$emit('editar', horario)" class="p-2 rounded-xl transition-all hover:scale-110 group" :class="isDark ? 'bg-blue-500/20 hover:bg-blue-500/30 border border-blue-500/30' : 'bg-blue-50 hover:bg-blue-100 border border-blue-200'">
                    <i class="fas fa-edit" :class="isDark ? 'text-blue-300 group-hover:text-blue-200' : 'text-blue-600 group-hover:text-blue-700'"></i>
                  </button>
                  <button @click="$emit('eliminar', horario.id)" class="p-2 rounded-xl transition-all hover:scale-110 group" :class="isDark ? 'bg-red-500/20 hover:bg-red-500/30 border border-red-500/30' : 'bg-red-50 hover:bg-red-100 border border-red-200'">
                    <i class="fas fa-trash" :class="isDark ? 'text-red-300 group-hover:text-red-200' : 'text-red-600 group-hover:text-red-700'"></i>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="grid gap-4 md:hidden">
      <div
        v-if="horarios.length === 0"
        class="text-center py-12 rounded-xl border"
        :class="[theme('card').value, theme('cardSubtitle').value]"
      >
        <i class="fas fa-search text-5xl mb-4 opacity-50"></i>
        <p class="text-lg">No se encontraron horarios</p>
      </div>
      <div
        v-for="horario in horarios"
        :key="horario.id"
        class="backdrop-blur-xl border rounded-3xl shadow-lg p-5"
        :class="theme('card').value"
      >
        <div class="flex items-start justify-between gap-3 mb-4">
          <div class="flex items-center gap-3">
            <div :class="['w-12 h-12 rounded-2xl flex items-center justify-center shadow-lg bg-gradient-to-br text-white font-bold', horario.color]">
              {{ horario.area.substring(0, 2).toUpperCase() }}
            </div>
            <div>
              <h2 class="text-lg font-semibold" :class="theme('cardTitle').value">{{ horario.area }}</h2>
              <span class="text-xs px-2 py-1 rounded-full" :class="isDark ? 'bg-white/10 text-gray-300' : 'bg-gray-100 text-gray-600'">ID: {{ horario.id }}</span>
            </div>
          </div>
          <div class="flex gap-2">
            <button @click="$emit('editar', horario)" class="p-2 rounded-xl transition-all hover:scale-110 group" :class="isDark ? 'bg-blue-500/20 hover:bg-blue-500/30 border border-blue-500/30' : 'bg-blue-50 hover:bg-blue-100 border border-blue-200'" title="Editar">
              <i class="fas fa-edit" :class="isDark ? 'text-blue-300 group-hover:text-blue-200' : 'text-blue-600 group-hover:text-blue-700'"></i>
            </button>
            <button @click="$emit('eliminar', horario.id)" class="p-2 rounded-xl transition-all hover:scale-110 group" :class="isDark ? 'bg-red-500/20 hover:bg-red-500/30 border border-red-500/30' : 'bg-red-50 hover:bg-red-100 border border-red-200'" title="Eliminar">
              <i class="fas fa-trash" :class="isDark ? 'text-red-300 group-hover:text-red-200' : 'text-red-600 group-hover:text-red-700'"></i>
            </button>
          </div>
        </div>
        <div class="flex flex-wrap items-center justify-between gap-3 pt-3 border-t" :class="isDark ? 'border-white/10' : 'border-gray-200'">
            <div class="border px-3 py-1.5 rounded-lg inline-flex items-center gap-2" :class="isDark ? 'bg-green-500/20 border-green-500/30' : 'bg-green-50 border-green-200'">
              <i class="fas fa-arrow-right-to-bracket text-sm" :class="isDark ? 'text-green-300' : 'text-green-600'"></i>
              <span class="font-mono font-semibold text-sm" :class="isDark ? 'text-green-200' : 'text-green-700'">{{ horario.entrada }}</span>
            </div>
            <div class="border px-3 py-1.5 rounded-lg inline-flex items-center gap-2" :class="isDark ? 'bg-red-500/20 border-red-500/30' : 'bg-red-50 border-red-200'">
              <i class="fas fa-arrow-right-from-bracket text-sm" :class="isDark ? 'text-red-300' : 'text-red-600'"></i>
              <span class="font-mono font-semibold text-sm" :class="isDark ? 'text-red-200' : 'text-red-700'">{{ horario.salida }}</span>
            </div>
             <div class="backdrop-blur-sm border px-3 py-1.5 rounded-xl inline-flex items-center gap-2" :class="isDark ? 'bg-white/10 border-white/20' : 'bg-gray-100 border-gray-200'">
              <i :class="getTurnoIcon(horario.turno)"></i>
              <span class="font-medium text-sm" :class="theme('cardTitle').value">{{ horario.turno }}</span>
            </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useTheme } from '../composables/useTheme'

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
  if (turno === 'Mañana') return `${base} fas fa-cloud-sun ${isDark.value ? 'text-sky-300' : 'text-sky-600'}`;
  if (turno === 'Tarde') return `${base} fas fa-sun ${isDark.value ? 'text-amber-300' : 'text-amber-500'}`;
  if (turno === 'Noche') return `${base} fas fa-moon ${isDark.value ? 'text-indigo-300' : 'text-indigo-500'}`;
  return `${base} fas fa-circle-notch ${isDark.value ? 'text-slate-300' : 'text-slate-500'}`;
};
</script>