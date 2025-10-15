<template>
  <div class="w-full">
    <!-- Header -->
    <div class="mb-6">
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
          <h1 class="text-2xl sm:text-3xl font-bold mb-2" :class="theme('cardTitle').value">
            <i class="fas fa-clock mr-2"></i>
            Horarios
          </h1>
          <p class="text-sm" :class="theme('cardSubtitle').value">
            Gestiona horarios de entrada y salida por área
          </p>
        </div>
        
        <button 
          @click="$emit('nuevoHorario')"
          class="flex items-center gap-2 px-6 py-3 rounded-xl font-semibold shadow-lg hover:scale-105 transition-all duration-200"
          :class="theme('buttonPrimary').value"
        >
          <i class="fas fa-plus"></i>
          Nuevo Horario
        </button>
      </div>

      <!-- Barra de búsqueda -->
      <div class="relative w-full sm:max-w-md mb-6">
        <input
          v-model="busqueda"
          type="text"
          placeholder="Buscar por área o turno..."
          class="w-full rounded-xl pl-10 pr-4 py-3 outline-none border transition-colors"
          :class="theme('input').value"
        />
        <i 
          class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2"
          :class="theme('cardSubtitle').value"
        ></i>
      </div>
    </div>

    <!-- Tabla -->
    <div 
      class="backdrop-blur-xl border rounded-3xl overflow-hidden shadow-2xl"
      :class="theme('card').value"
    >
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead :class="theme('tableHeader').value">
            <tr class="border-b" :class="isDark ? 'border-white/20' : 'border-gray-200'">
              <th class="text-left p-4 font-bold text-sm" :class="theme('cardSubtitle').value">ID</th>
              <th class="text-left p-4 font-bold text-sm" :class="theme('cardSubtitle').value">Área / Nivel</th>
              <th class="text-left p-4 font-bold text-sm" :class="theme('cardSubtitle').value">Entrada</th>
              <th class="text-left p-4 font-bold text-sm" :class="theme('cardSubtitle').value">Salida</th>
              <th class="text-left p-4 font-bold text-sm" :class="theme('cardSubtitle').value">Turno</th>
              <th class="text-center p-4 font-bold text-sm" :class="theme('cardSubtitle').value">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="horariosFiltrados.length === 0">
              <td colspan="6" class="text-center p-8" :class="theme('cardSubtitle').value">
                <i class="fas fa-calendar text-5xl mb-3 opacity-50"></i>
                <p>No se encontraron horarios</p>
              </td>
            </tr>
            
            <tr 
              v-for="(horario, index) in horariosFiltrados" 
              :key="horario.id"
              class="border-b transition-all"
              :class="[
                theme('tableRow').value,
                isDark ? 'border-white/5' : 'border-gray-100',
                index % 2 === 0 ? (isDark ? 'bg-white/[0.03]' : 'bg-gray-50/50') : ''
              ]"
            >
              <td class="p-4">
                <span class="font-mono text-sm" :class="theme('cardSubtitle').value">{{ horario.id }}</span>
              </td>
              
              <td class="p-4">
                <div class="flex items-center gap-3">
                  <div 
                    :class="[
                      'w-10 h-10 rounded-xl flex items-center justify-center shadow-lg',
                      'bg-gradient-to-br',
                      horario.color
                    ]"
                  >
                    <span class="text-white font-bold text-xs">
                      {{ horario.area.substring(0, 2).toUpperCase() }}
                    </span>
                  </div>
                  <span class="font-semibold" :class="theme('cardTitle').value">{{ horario.area }}</span>
                </div>
              </td>
              
              <td class="p-4">
                <div 
                  class="border px-3 py-1.5 rounded-lg inline-flex items-center gap-2"
                  :class="isDark 
                    ? 'bg-green-500/20 border-green-500/30' 
                    : 'bg-green-50 border-green-200'"
                >
                  <i 
                    class="fas fa-clock text-sm"
                    :class="isDark ? 'text-green-300' : 'text-green-600'"
                  ></i>
                  <span 
                    class="font-mono font-semibold text-sm"
                    :class="isDark ? 'text-green-200' : 'text-green-700'"
                  >
                    {{ horario.entrada }}
                  </span>
                </div>
              </td>
              
              <td class="p-4">
                <div 
                  class="border px-3 py-1.5 rounded-lg inline-flex items-center gap-2"
                  :class="isDark 
                    ? 'bg-red-500/20 border-red-500/30' 
                    : 'bg-red-50 border-red-200'"
                >
                  <i 
                    class="fas fa-clock text-sm"
                    :class="isDark ? 'text-red-300' : 'text-red-600'"
                  ></i>
                  <span 
                    class="font-mono font-semibold text-sm"
                    :class="isDark ? 'text-red-200' : 'text-red-700'"
                  >
                    {{ horario.salida }}
                  </span>
                </div>
              </td>
              
              <td class="p-4">
                <div 
                  class="backdrop-blur-sm border px-3 py-1.5 rounded-xl inline-flex items-center gap-2"
                  :class="isDark ? 'bg-white/10 border-white/20' : 'bg-gray-100 border-gray-200'"
                >
                  <i :class="getTurnoIcon(horario.turno)"></i>
                  <span class="font-medium text-sm" :class="theme('cardTitle').value">{{ horario.turno }}</span>
                </div>
              </td>
              
              <td class="p-4">
                <div class="flex justify-center gap-2">
                  <button 
                    @click="$emit('editar', horario)"
                    class="p-2 rounded-xl transition-all hover:scale-110 group"
                    :class="isDark 
                      ? 'bg-blue-500/20 hover:bg-blue-500/30 border border-blue-500/30' 
                      : 'bg-blue-50 hover:bg-blue-100 border border-blue-200'"
                  >
                    <i 
                      class="fas fa-edit"
                      :class="isDark ? 'text-blue-300 group-hover:text-blue-200' : 'text-blue-600 group-hover:text-blue-700'"
                    ></i>
                  </button>
                  <button 
                    @click="$emit('eliminar', horario.id)"
                    class="p-2 rounded-xl transition-all hover:scale-110 group"
                    :class="isDark 
                      ? 'bg-red-500/20 hover:bg-red-500/30 border border-red-500/30' 
                      : 'bg-red-50 hover:bg-red-100 border border-red-200'"
                  >
                    <i 
                      class="fas fa-trash"
                      :class="isDark ? 'text-red-300 group-hover:text-red-200' : 'text-red-600 group-hover:text-red-700'"
                    ></i>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Footer con estadísticas -->
      <div 
        class="border-t px-6 py-4 flex flex-wrap items-center justify-between gap-4"
        :class="isDark ? 'bg-white/5 border-white/10' : 'bg-gray-50 border-gray-200'"
      >
        <div class="text-sm" :class="theme('cardSubtitle').value">
          Mostrando <span class="font-semibold" :class="theme('cardTitle').value">{{ horariosFiltrados.length }}</span> 
          de <span class="font-semibold" :class="theme('cardTitle').value">{{ horarios.length }}</span> horarios
        </div>
        <div class="flex gap-2">
          <button 
            class="px-3 py-1.5 rounded-lg text-sm transition-all border"
            :class="isDark 
              ? 'bg-white/5 hover:bg-white/10 border-white/10 text-gray-300' 
              : 'bg-gray-100 hover:bg-gray-200 border-gray-200 text-gray-600'"
          >
            Anterior
          </button>
          <button 
            class="px-3 py-1.5 rounded-lg font-medium text-sm border"
            :class="isDark 
              ? 'bg-white/10 border-white/20 text-white' 
              : 'bg-blue-500 border-blue-500 text-white'"
          >
            1
          </button>
          <button 
            class="px-3 py-1.5 rounded-lg text-sm transition-all border"
            :class="isDark 
              ? 'bg-white/5 hover:bg-white/10 border-white/10 text-gray-300' 
              : 'bg-gray-100 hover:bg-gray-200 border-gray-200 text-gray-600'"
          >
            2
          </button>
          <button 
            class="px-3 py-1.5 rounded-lg text-sm transition-all border"
            :class="isDark 
              ? 'bg-white/5 hover:bg-white/10 border-white/10 text-gray-300' 
              : 'bg-gray-100 hover:bg-gray-200 border-gray-200 text-gray-600'"
          >
            Siguiente
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useTheme } from '../composables/useTheme'

const { theme, isDark } = useTheme()

const props = defineProps({
  horarios: {
    type: Array,
    default: () => [
      { id: 1, area: 'Primaria', entrada: '07:30', salida: '13:30', turno: 'Mañana', color: 'from-blue-400 to-blue-600' },
      { id: 2, area: 'Secundaria', entrada: '07:30', salida: '14:00', turno: 'Mañana', color: 'from-green-400 to-emerald-600' },
      { id: 3, area: 'Administración', entrada: '08:00', salida: '16:00', turno: 'Completo', color: 'from-purple-400 to-purple-600' },
      { id: 4, area: 'Docentes', entrada: '07:00', salida: '13:00', turno: 'Mañana', color: 'from-pink-400 to-pink-600' },
      { id: 5, area: 'Inicial', entrada: '08:00', salida: '12:30', turno: 'Mañana', color: 'from-orange-400 to-orange-600' },
      { id: 6, area: 'Secundaria Tarde', entrada: '13:00', salida: '18:00', turno: 'Tarde', color: 'from-indigo-400 to-indigo-600' },
      { id: 7, area: 'Limpieza', entrada: '06:00', salida: '14:00', turno: 'Completo', color: 'from-cyan-400 to-cyan-600' },
      { id: 8, area: 'Seguridad', entrada: '18:00', salida: '06:00', turno: 'Noche', color: 'from-slate-400 to-slate-600' },
    ]
  }
})

defineEmits(['nuevoHorario', 'editar', 'eliminar'])

const busqueda = ref('')

const horariosFiltrados = computed(() => {
  if (!busqueda.value) return props.horarios
  
  return props.horarios.filter(h => 
    h.area.toLowerCase().includes(busqueda.value.toLowerCase()) ||
    h.turno.toLowerCase().includes(busqueda.value.toLowerCase())
  )
})

const getTurnoIcon = (turno) => {
  const base = 'text-sm';

  if (turno === 'Mañana') {
    // fa-cloud-sun con nube blanca y “borde celeste” vía drop-shadow
    return [
      base,
      'fas fa-cloud-sun',
      isDark ? 'text-white drop-shadow-[0_0_0.5px_rgba(125,211,252,0.9)]'
             : 'text-white drop-shadow-[0_0_0.5px_rgba(59,130,246,0.9)]'
    ].join(' ');
  }
  if (turno === 'Tarde') {
    return `${base} fas fa-sun ${isDark ? 'text-amber-300' : 'text-amber-600'}`;
  }
  if (turno === 'Noche') {
    return `${base} fas fa-moon ${isDark ? 'text-indigo-300' : 'text-indigo-600'}`;
  }
  return `${base} fas fa-clock ${isDark ? 'text-purple-300' : 'text-purple-600'}`;
};
</script>