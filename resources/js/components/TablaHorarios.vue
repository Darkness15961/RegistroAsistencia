<template>
  <div class="w-full">
    <!-- Header -->
    <div class="mb-6">
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
          <h1 class="text-2xl sm:text-3xl font-bold text-white mb-2">
            <i class="fas fa-clock mr-2"></i>
            Horarios
          </h1>
          <p class="text-gray-300 text-sm">Gestiona horarios de entrada y salida por área</p>
        </div>
        
        <button 
          @click="$emit('nuevoHorario')"
          class="bg-gradient-to-r from-cyan-400 to-blue-500 hover:from-cyan-500 hover:to-blue-600 text-white px-5 py-2.5 rounded-xl font-medium transition-all transform hover:scale-105 shadow-lg"
        >
          <i class="fas fa-plus mr-2"></i>
          Nuevo Horario
        </button>
      </div>

      <!-- Barra de búsqueda -->
      <div class="bg-white/10 backdrop-blur-xl border border-white/15 rounded-2xl p-3 flex items-center gap-3">
        <i class="fas fa-search text-gray-400"></i>
        <input
          v-model="busqueda"
          type="text"
          placeholder="Buscar por área, día o turno..."
          class="bg-transparent text-white placeholder-gray-400 outline-none flex-1 text-sm"
        />
      </div>
    </div>

    <!-- Tabla -->
    <div class="bg-white/10 backdrop-blur-xl border border-white/15 rounded-3xl overflow-hidden shadow-2xl">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="bg-white/10 border-b border-white/20">
              <th class="text-left p-4 text-gray-200 font-bold text-sm">ID</th>
              <th class="text-left p-4 text-gray-200 font-bold text-sm">Área / Nivel</th>
              <th class="text-left p-4 text-gray-200 font-bold text-sm">Día</th>
              <th class="text-left p-4 text-gray-200 font-bold text-sm">Entrada</th>
              <th class="text-left p-4 text-gray-200 font-bold text-sm">Salida</th>
              <th class="text-left p-4 text-gray-200 font-bold text-sm">Turno</th>
              <th class="text-center p-4 text-gray-200 font-bold text-sm">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="horariosFiltrados.length === 0">
              <td colspan="7" class="text-center p-8 text-gray-400">
                <i class="fas fa-calendar text-5xl mb-3 opacity-50"></i>
                <p>No se encontraron horarios</p>
              </td>
            </tr>
            
            <tr 
              v-for="(horario, index) in horariosFiltrados" 
              :key="horario.id"
              :class="[
                'border-b border-white/5 hover:bg-white/10 transition-all',
                index % 2 === 0 ? 'bg-white/[0.03]' : ''
              ]"
            >
              <td class="p-4">
                <span class="text-gray-300 font-mono text-sm">{{ horario.id }}</span>
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
                  <span class="text-white font-semibold">{{ horario.area }}</span>
                </div>
              </td>
              
              <td class="p-4">
                <div class="flex items-center gap-2">
                  <i class="fas fa-calendar text-gray-400 text-sm"></i>
                  <span class="text-gray-300">{{ horario.dia }}</span>
                </div>
              </td>
              
              <td class="p-4">
                <div class="bg-green-500/20 border border-green-500/30 px-3 py-1.5 rounded-lg inline-flex items-center gap-2">
                  <i class="fas fa-clock text-green-300 text-sm"></i>
                  <span class="text-green-200 font-mono font-semibold">{{ horario.entrada }}</span>
                </div>
              </td>
              
              <td class="p-4">
                <div class="bg-red-500/20 border border-red-500/30 px-3 py-1.5 rounded-lg inline-flex items-center gap-2">
                  <i class="fas fa-clock text-red-300 text-sm"></i>
                  <span class="text-red-200 font-mono font-semibold">{{ horario.salida }}</span>
                </div>
              </td>
              
              <td class="p-4">
                <div class="bg-white/10 backdrop-blur-sm border border-white/20 px-3 py-1.5 rounded-xl inline-flex items-center gap-2">
                  <i :class="getTurnoIcon(horario.turno)"></i>
                  <span class="text-white font-medium text-sm">{{ horario.turno }}</span>
                </div>
              </td>
              
              <td class="p-4">
                <div class="flex justify-center gap-2">
                  <button 
                    @click="$emit('editar', horario)"
                    class="bg-blue-500/20 hover:bg-blue-500/30 border border-blue-500/30 p-2 rounded-xl transition-all hover:scale-110 group"
                  >
                    <i class="fas fa-edit text-blue-300 group-hover:text-blue-200"></i>
                  </button>
                  <button 
                    @click="$emit('eliminar', horario.id)"
                    class="bg-red-500/20 hover:bg-red-500/30 border border-red-500/30 p-2 rounded-xl transition-all hover:scale-110 group"
                  >
                    <i class="fas fa-trash text-red-300 group-hover:text-red-200"></i>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Footer con estadísticas -->
      <div class="bg-white/5 border-t border-white/10 px-6 py-4 flex flex-wrap items-center justify-between gap-4">
        <div class="text-gray-300 text-sm">
          Mostrando <span class="text-white font-semibold">{{ horariosFiltrados.length }}</span> de <span class="text-white font-semibold">{{ horarios.length }}</span> horarios
        </div>
        <div class="flex gap-2">
          <button class="bg-white/5 hover:bg-white/10 border border-white/10 px-3 py-1.5 rounded-lg text-gray-300 text-sm transition-all">
            Anterior
          </button>
          <button class="bg-white/10 border border-white/20 px-3 py-1.5 rounded-lg text-white font-medium text-sm">
            1
          </button>
          <button class="bg-white/5 hover:bg-white/10 border border-white/10 px-3 py-1.5 rounded-lg text-gray-300 text-sm transition-all">
            2
          </button>
          <button class="bg-white/5 hover:bg-white/10 border border-white/10 px-3 py-1.5 rounded-lg text-gray-300 text-sm transition-all">
            Siguiente
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  horarios: {
    type: Array,
    default: () => [
      { id: 1, area: 'Primaria', dia: 'Lunes', entrada: '07:30', salida: '13:30', turno: 'Mañana', color: 'from-blue-400 to-blue-600' },
      { id: 2, area: 'Secundaria', dia: 'Lunes', entrada: '07:30', salida: '14:00', turno: 'Mañana', color: 'from-green-400 to-emerald-600' },
      { id: 3, area: 'Administración', dia: 'Lunes', entrada: '08:00', salida: '16:00', turno: 'Completo', color: 'from-purple-400 to-purple-600' },
      { id: 4, area: 'Primaria', dia: 'Martes', entrada: '07:30', salida: '13:30', turno: 'Mañana', color: 'from-blue-400 to-blue-600' },
      { id: 5, area: 'Docentes', dia: 'Lunes', entrada: '07:00', salida: '13:00', turno: 'Mañana', color: 'from-pink-400 to-pink-600' },
      { id: 6, area: 'Inicial', dia: 'Lunes', entrada: '08:00', salida: '12:30', turno: 'Mañana', color: 'from-orange-400 to-orange-600' },
      { id: 7, area: 'Secundaria', dia: 'Martes', entrada: '13:00', salida: '18:00', turno: 'Tarde', color: 'from-green-400 to-emerald-600' },
      { id: 8, area: 'Limpieza', dia: 'Lunes', entrada: '06:00', salida: '14:00', turno: 'Completo', color: 'from-cyan-400 to-cyan-600' },
    ]
  }
})

defineEmits(['nuevoHorario', 'editar', 'eliminar'])

const busqueda = ref('')

const horariosFiltrados = computed(() => {
  if (!busqueda.value) return props.horarios
  
  return props.horarios.filter(h => 
    h.area.toLowerCase().includes(busqueda.value.toLowerCase()) ||
    h.dia.toLowerCase().includes(busqueda.value.toLowerCase()) ||
    h.turno.toLowerCase().includes(busqueda.value.toLowerCase())
  )
})

const getTurnoIcon = (turno) => {
  if (turno === 'Mañana') return 'fas fa-sun text-yellow-300'
  if (turno === 'Tarde') return 'fas fa-moon text-blue-300'
  return 'fas fa-clock text-purple-300'
}
</script>