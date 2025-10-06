<template>
  <div class="w-full">
    <!-- Header -->
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
      <div>
        <h2 class="text-2xl font-bold text-white mb-1">
          <i :class="`fas fa-${iconoArea} mr-2`"></i>
          {{ nombreArea }}
        </h2>
        <p class="text-gray-300 text-sm">{{ registros.length }} personas registradas</p>
      </div>
      
      <button 
        @click="$emit('volver')"
        class="bg-white/10 hover:bg-white/20 border border-white/20 text-white px-4 py-2 rounded-xl font-medium transition-all"
      >
        <i class="fas fa-arrow-left mr-2"></i>
        Volver
      </button>
    </div>

    <!-- Barra de búsqueda -->
    <div class="bg-white/10 backdrop-blur-xl border border-white/15 rounded-2xl p-3 flex items-center gap-3 mb-6">
      <i class="fas fa-search text-gray-400"></i>
      <input
        v-model="busqueda"
        type="text"
        placeholder="Buscar por nombre o sección..."
        class="bg-transparent text-white placeholder-gray-400 outline-none flex-1 text-sm"
      />
    </div>

    <!-- Filtros por sección -->
    <FiltrosAsistencia
      v-if="filtros && filtros.length > 0"
      :filtros="filtros"
      :filtroSeleccionado="filtroSeleccionado"
      @seleccionar="$emit('seleccionarFiltro', $event)"
    />

    <!-- Tabla -->
    <div class="bg-white/10 backdrop-blur-xl border border-white/15 rounded-3xl overflow-hidden shadow-2xl">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="bg-white/10 border-b border-white/20 text-gray-200 text-sm">
              <th class="p-3 text-left">ID</th>
              <th class="p-3 text-left">Nombre Completo</th>
              <th class="p-3 text-left">Sección</th>
              <th class="p-3 text-center" v-for="dia in diasSemana" :key="dia">{{ dia }}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="registrosFiltrados.length === 0">
              <td colspan="10" class="text-center p-8 text-gray-400">
                <i class="fas fa-user-clock text-5xl mb-3 opacity-50"></i>
                <p>No hay registros de asistencia</p>
              </td>
            </tr>

            <tr
              v-for="(persona, index) in registrosFiltrados"
              :key="persona.id"
              :class="[
                'border-b border-white/5 hover:bg-white/10 transition-all',
                index % 2 === 0 ? 'bg-white/[0.03]' : ''
              ]"
            >
              <td class="p-3 text-gray-300 font-mono text-sm">{{ persona.id }}</td>
              <td class="p-3 text-white font-semibold">{{ persona.nombre }}</td>
              <td class="p-3 text-gray-300 text-sm">{{ persona.seccion }}</td>
              
              <td
                v-for="dia in diasSemana"
                :key="dia"
                class="p-3 text-center"
              >
                <span
                  :class="[
                    'px-3 py-1 rounded-full text-xs font-medium inline-flex items-center justify-center gap-2',
                    persona.asistencia[dia] === 'P'
                      ? 'bg-green-500/20 border border-green-500/30 text-green-200'
                      : persona.asistencia[dia] === 'T'
                      ? 'bg-yellow-500/20 border border-yellow-500/30 text-yellow-200'
                      : 'bg-red-500/20 border border-red-500/30 text-red-200'
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

const props = defineProps({
  registros: {
    type: Array,
    default: () => []
  },
  nombreArea: {
    type: String,
    required: true
  },
  iconoArea: {
    type: String,
    default: 'calendar-check'
  },
  filtros: {
    type: Array,
    default: () => []
  },
  filtroSeleccionado: {
    type: Object,
    default: null
  }
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

