<template>
  <div class="w-full">
    <!-- Header -->
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
      <div>
        <h2 class="text-2xl font-bold mb-1 flex items-center text-gray-800 dark:text-white">
          <i :class="`fas fa-${iconoArea} mr-2 text-blue-500 dark:text-blue-400`"></i>
          {{ nombreArea }}
        </h2>
        <p class="text-sm text-gray-400 dark:text-gray-300">
          {{ registrosFiltrados.length }} personas registradas
        </p>
      </div>
      <button 
        @click="$emit('volver')"
        class="px-4 py-2 rounded-xl font-medium transition-all
               bg-gradient-to-r from-blue-500 to-purple-500 text-white shadow"
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
        class="w-full bg-white border border-gray-200 rounded-xl pl-10 pr-4 py-3 text-gray-800 placeholder-gray-400 shadow focus:outline-none focus:border-blue-400 transition
               dark:bg-white/10 dark:border-white/20 dark:text-white dark:placeholder-gray-300"
      />
      <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-400"></i>
    </div>

    <!-- Filtros por sección -->
    <FiltrosAsistencia
      v-if="filtros && filtros.length > 0"
      :filtros="filtros"
      :filtroSeleccionado="filtroSeleccionado"
      @seleccionar="$emit('seleccionarFiltro', $event)"
    />

    <!-- Tabla -->
    <div class="rounded-3xl overflow-hidden shadow-xl
        bg-white border border-gray-200
        dark:bg-white/10 dark:border-white/20
        backdrop-blur-xl">
      <div class="overflow-x-auto">
        <table class="w-full text-left">
          <thead :class="`text-sm ${isDark ? 'bg-white/5 text-gray-200 border-white/20' : 'bg-gray-50 text-gray-700 border-gray-200'}`">
            <tr class="border-b"
                :class="isDark ? 'border-white/20' : 'border-gray-200'">
              <th class="px-6 py-4 font-bold uppercase tracking-wide text-gray-600 dark:text-gray-300">ID</th>
              <th class="px-6 py-4 font-bold uppercase tracking-wide text-gray-900 dark:text-white">Nombre</th>
              <th class="px-6 py-4 font-bold uppercase tracking-wide text-blue-800 dark:text-blue-300">Sección</th>
              <th v-for="dia in diasSemana" :key="dia"
                  class="px-6 py-4 font-bold uppercase tracking-wide text-blue-900 dark:text-blue-200 text-center">{{ dia }}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="registrosFiltrados.length === 0">
              <td colspan="10" class="text-center p-8 text-gray-400 dark:text-gray-500">
                <i class="fas fa-user-clock text-5xl mb-3 opacity-50"></i>
                <p>No hay registros de asistencia</p>
              </td>
            </tr>
            <tr
              v-for="(persona, index) in registrosFiltrados"
              :key="persona.id"
              class="border-b transition-all"
              :class="[
                isDark ? 'border-white/5' : 'border-gray-100',
                index % 2 === 0
                  ? (isDark ? 'bg-white/[0.03]' : 'bg-gray-50/50')
                  : ''
              ]"
            >
              <td class="px-6 py-4 font-mono text-sm text-gray-800 dark:text-gray-300">{{ persona.id }}</td>
              <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white">{{ persona.nombre }}</td>
              <td class="px-6 py-4 text-sm text-blue-800 dark:text-blue-300">{{ persona.seccion }}</td>
              <td
                v-for="dia in diasSemana"
                :key="dia"
                class="px-6 py-4 text-center"
              >
                <span
                  :class="[
                    'px-3 py-1 rounded-full text-xs font-medium inline-flex items-center justify-center gap-2 border',
                    persona.asistencia[dia] === 'P'
                      ? 'bg-green-100 text-green-700 border-green-200 dark:bg-green-800/30 dark:text-green-200 dark:border-green-900'
                      : persona.asistencia[dia] === 'T'
                      ? 'bg-yellow-100 text-yellow-700 border-yellow-200 dark:bg-yellow-800/40 dark:text-yellow-200 dark:border-yellow-900'
                      : 'bg-red-100 text-red-700 border-red-200 dark:bg-red-800/30 dark:text-red-300 dark:border-red-900'
                  ]"
                >
                  {{ persona.asistencia[dia] }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import FiltrosAsistencia from '@/components/FiltrosAsistencia.vue'
import { useTheme } from '../composables/useTheme'
const { isDark } = useTheme()

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
</script>