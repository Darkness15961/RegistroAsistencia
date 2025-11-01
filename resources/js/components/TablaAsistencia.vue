<template>
  <div class="w-full">
    <!-- Header -->
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
      <div>
        <h2 class="text-2xl font-bold mb-1 flex items-center" :class="theme('cardTitle').value">
          <i :class="`fas fa-${iconoArea} mr-2 ${isDark ? 'text-blue-400' : 'text-blue-500'}`"></i>
          {{ nombreArea }}
        </h2>
        <p class="text-sm" :class="theme('cardSubtitle').value">
          {{ registrosFiltrados.length }} personas registradas
        </p>
      </div>
      <button 
        @click="$emit('volver')"
        class="px-5 py-2.5 rounded-xl font-semibold transition-all shadow-lg hover:scale-105"
        :class="theme('buttonPrimary').value"
      >
        <i class="fas fa-arrow-left mr-2"></i>
        Volver
      </button>
    </div>
    
    <!-- BUSCADOR -->
    <div class="relative w-full sm:max-w-md mb-6">
      <input
        v-model="busqueda"
        type="text"
        placeholder="Buscar por nombre o sección..."
        class="w-full rounded-xl pl-10 pr-4 py-3 outline-none border transition-colors"
        :class="theme('input').value"
      />
      <i 
        class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2"
        :class="theme('cardSubtitle').value"
      ></i>
    </div>

    <!-- Filtros por sección -->
    <FiltrosAsistencia
      v-if="filtros && filtros.length > 0"
      :filtros="filtros"
      :filtroSeleccionado="filtroSeleccionado"
      @seleccionar="$emit('seleccionarFiltro', $event)"
      class="mb-6"
    />

    <!-- Tabla (Desktop) -->
    <div 
      class="hidden md:block rounded-3xl overflow-hidden shadow-2xl backdrop-blur-xl border"
      :class="theme('card').value"
    >
      <div class="overflow-x-auto">
        <table class="w-full text-left">
          <thead :class="theme('tableHeader').value">
            <tr class="border-b" :class="isDark ? 'border-white/20' : 'border-gray-200'">
              <th class="px-6 py-4 font-bold uppercase tracking-wide text-sm" :class="theme('cardSubtitle').value">ID</th>
              <th class="px-6 py-4 font-bold uppercase tracking-wide text-sm" :class="theme('cardTitle').value">Nombre</th>
              <th class="px-6 py-4 font-bold uppercase tracking-wide text-sm" :class="isDark ? 'text-blue-300' : 'text-blue-800'">Sección</th>
              <th v-for="dia in diasSemana" :key="dia"
                  class="px-6 py-4 font-bold uppercase tracking-wide text-sm text-center"
                  :class="isDark ? 'text-cyan-300' : 'text-cyan-800'">{{ dia }}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="registrosFiltrados.length === 0">
              <td colspan="10" class="text-center p-8" :class="theme('cardSubtitle').value">
                <i class="fas fa-user-clock text-5xl mb-3 opacity-50"></i>
                <p>No hay registros de asistencia</p>
              </td>
            </tr>
            <tr
              v-for="(persona, index) in registrosFiltrados"
              :key="persona.id"
              class="border-b transition-all"
              :class="[
                theme('tableRow').value,
                isDark ? 'border-white/5' : 'border-gray-100',
                index % 2 === 0 ? (isDark ? 'bg-white/[0.03]' : 'bg-gray-50/50') : ''
              ]"
            >
              <td class="px-6 py-4 font-mono text-sm" :class="theme('cardSubtitle').value">{{ persona.id }}</td>
              <td class="px-6 py-4 font-semibold" :class="theme('cardTitle').value">{{ persona.nombre }}</td>
              <td class="px-6 py-4 text-sm font-medium" :class="isDark ? 'text-blue-300' : 'text-blue-800'">{{ persona.seccion }}</td>
              <td
                v-for="dia in diasSemana"
                :key="dia"
                class="px-6 py-4 text-center"
              >
                <span :class="['px-3 py-1 rounded-full text-xs font-medium inline-flex items-center justify-center gap-2 border', getStatusClass(persona.asistencia[dia])]">
                  {{ persona.asistencia[dia] }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Vista Móvil (Tarjetas) -->
    <div class="grid gap-4 md:hidden">
      <div
        v-if="registrosFiltrados.length === 0"
        class="text-center py-12 rounded-xl border"
        :class="[theme('card').value, theme('cardSubtitle').value]"
      >
        <i class="fas fa-user-clock text-5xl mb-4 opacity-50"></i>
        <p class="text-lg">No hay registros</p>
      </div>

      <div
        v-for="persona in registrosFiltrados"
        :key="persona.id"
        class="backdrop-blur-xl border rounded-3xl shadow-lg p-5"
        :class="theme('card').value"
      >
        <div class="flex items-start justify-between gap-3 mb-4">
          <div>
            <h2 class="text-lg font-semibold" :class="theme('cardTitle').value">{{ persona.nombre }}</h2>
            <span class_="text-sm font-medium" :class="isDark ? 'text-blue-300' : 'text-blue-800'">{{ persona.seccion }}</span>
          </div>
          <span class="text-xs px-2 py-1 rounded-full" :class="isDark ? 'bg-white/10 text-gray-300' : 'bg-gray-100 text-gray-600'">
            ID: {{ persona.id }}
          </span>
        </div>
        
        <div class="flex items-center justify-between gap-2 pt-3 border-t" :class="isDark ? 'border-white/10' : 'border-gray-200'">
          <div v-for="dia in diasSemana" :key="dia" class="flex flex-col items-center">
            <span class="text-xs font-bold mb-1" :class="theme('cardSubtitle').value">{{ dia }}</span>
            <span :class="['px-3 py-1 rounded-full text-xs font-medium inline-flex items-center justify-center gap-2 border', getStatusClass(persona.asistencia[dia])]">
              {{ persona.asistencia[dia] }}
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import FiltrosAsistencia from '@/components/FiltrosAsistencia.vue'
import { useTheme } from '../composables/useTheme'

const { theme, isDark } = useTheme()

const props = defineProps({
  registros: { type: Array, default: () => [] },
  nombreArea: { type: String, required: true },
  iconoArea: { type: String, default: 'calendar-check' },
  filtros: { type: Array, default: () => [] },
  filtroSeleccionado: { type: Object, default: null }
})

defineEmits(['volver', 'seleccionarFiltro'])

const busqueda = ref('')
const diasSemana = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie']

const registrosFiltrados = computed(() => {
  let lista = props.registros
  if (props.filtroSeleccionado) {
    lista = lista.filter(r => r.seccion === props.filtroSeleccionado.nombre)
  }
  if (busqueda.value) {
    lista = lista.filter(r =>
      r.nombre.toLowerCase().includes(busqueda.value.toLowerCase()) ||
      r.seccion.toLowerCase().includes(busqueda.value.toLowerCase())
    )
  }
  return lista
})

// Función de estado actualizada para usar `isDark`
const getStatusClass = (estado) => {
  if (estado === 'P') {
    return isDark.value
      ? 'bg-green-500/20 border-green-500/30 text-green-200'
      : 'bg-green-50 border-green-200 text-green-700'
  }
  if (estado === 'T') {
    return isDark.value
      ? 'bg-yellow-500/20 border-yellow-500/30 text-yellow-200'
      : 'bg-yellow-50 border-yellow-200 text-yellow-700'
  }
  // Para 'F' (Falta) o cualquier otro estado
  return isDark.value
    ? 'bg-red-500/20 border-red-500/30 text-red-200'
    : 'bg-red-50 border-red-200 text-red-700'
}
</script>