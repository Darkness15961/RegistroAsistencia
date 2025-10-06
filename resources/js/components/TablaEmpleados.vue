<template>
  <div class="w-full">
    <!-- Header con título del área y botones -->
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
      <div>
        <h2 class="text-2xl font-bold text-white mb-1">
          <i :class="`fas fa-${iconoArea} mr-2`"></i>
          {{ nombreArea }}
        </h2>
        <p class="text-gray-300 text-sm">{{ empleados.length }} personas registradas</p>
      </div>
      
      <div class="flex gap-3">
        <button 
          @click="$emit('volver')"
          class="bg-white/10 hover:bg-white/20 border border-white/20 text-white px-4 py-2 rounded-xl font-medium transition-all"
        >
          <i class="fas fa-arrow-left mr-2"></i>
          Volver
        </button>
        <button 
          @click="$emit('nuevoEmpleado')"
          class="bg-gradient-to-r from-cyan-400 to-blue-500 hover:from-cyan-500 hover:to-blue-600 text-white px-5 py-2.5 rounded-xl font-medium transition-all transform hover:scale-105 shadow-lg"
        >
          <i class="fas fa-plus mr-2"></i>
          Nuevo Empleado
        </button>
      </div>
    </div>

    <!-- Barra de búsqueda -->
    <div class="bg-white/10 backdrop-blur-xl border border-white/15 rounded-2xl p-3 flex items-center gap-3 mb-6">
      <i class="fas fa-search text-gray-400"></i>
      <input
        v-model="busqueda"
        type="text"
        placeholder="Buscar por nombre, cargo o correo..."
        class="bg-transparent text-white placeholder-gray-400 outline-none flex-1 text-sm"
      />
    </div>

    <!-- Tabla -->
    <div class="bg-white/10 backdrop-blur-xl border border-white/15 rounded-3xl overflow-hidden shadow-2xl">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="bg-white/10 border-b border-white/20">
              <th class="text-left p-4 text-gray-200 font-bold text-sm">ID</th>
              <th class="text-left p-4 text-gray-200 font-bold text-sm">Nombre Completo</th>
              <th class="text-left p-4 text-gray-200 font-bold text-sm">Cargo</th>
              <th class="text-left p-4 text-gray-200 font-bold text-sm">Correo</th>
              <th class="text-left p-4 text-gray-200 font-bold text-sm">Teléfono</th>
              <th class="text-left p-4 text-gray-200 font-bold text-sm">Estado</th>
              <th class="text-center p-4 text-gray-200 font-bold text-sm">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="empleadosFiltrados.length === 0">
              <td colspan="7" class="text-center p-8 text-gray-400">
                <i class="fas fa-users text-5xl mb-3 opacity-50"></i>
                <p>No se encontraron empleados</p>
              </td>
            </tr>
            
            <tr 
              v-for="(empleado, index) in empleadosFiltrados" 
              :key="empleado.id"
              :class="[
                'border-b border-white/5 hover:bg-white/10 transition-all',
                index % 2 === 0 ? 'bg-white/[0.03]' : ''
              ]"
            >
              <td class="p-4">
                <span class="text-gray-300 font-mono text-sm">{{ empleado.id }}</span>
              </td>
              
              <td class="p-4">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-400 to-pink-500 flex items-center justify-center shadow-lg">
                    <span class="text-white font-bold text-sm">
                      {{ empleado.nombre.split(' ').map(n => n[0]).join('').substring(0, 2) }}
                    </span>
                  </div>
                  <span class="text-white font-semibold">{{ empleado.nombre }}</span>
                </div>
              </td>
              
              <td class="p-4">
                <div class="bg-blue-500/20 border border-blue-500/30 px-3 py-1 rounded-lg inline-block">
                  <span class="text-blue-200 text-sm font-medium">{{ empleado.cargo }}</span>
                </div>
              </td>
              
              <td class="p-4">
                <div class="flex items-center gap-2">
                  <i class="fas fa-envelope text-gray-400 text-sm"></i>
                  <span class="text-gray-300 text-sm">{{ empleado.correo }}</span>
                </div>
              </td>
              
              <td class="p-4">
                <div class="flex items-center gap-2">
                  <i class="fas fa-phone text-gray-400 text-sm"></i>
                  <span class="text-gray-300 text-sm">{{ empleado.telefono }}</span>
                </div>
              </td>
              
              <td class="p-4">
                <div 
                  :class="[
                    'px-3 py-1 rounded-full inline-flex items-center gap-2 text-xs font-medium',
                    empleado.estado === 'Activo' 
                      ? 'bg-green-500/20 border border-green-500/30 text-green-200' 
                      : 'bg-red-500/20 border border-red-500/30 text-red-200'
                  ]"
                >
                  <div 
                    :class="[
                      'w-2 h-2 rounded-full',
                      empleado.estado === 'Activo' ? 'bg-green-400' : 'bg-red-400'
                    ]"
                  ></div>
                  {{ empleado.estado }}
                </div>
              </td>
              
              <td class="p-4">
                <div class="flex justify-center gap-2">
                  <button 
                    @click="$emit('editar', empleado)"
                    class="bg-blue-500/20 hover:bg-blue-500/30 border border-blue-500/30 p-2 rounded-xl transition-all hover:scale-110 group"
                  >
                    <i class="fas fa-edit text-blue-300 group-hover:text-blue-200"></i>
                  </button>
                  <button 
                    @click="$emit('eliminar', empleado.id)"
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

      <!-- Footer con paginación -->
      <div class="bg-white/5 border-t border-white/10 px-6 py-4 flex flex-wrap items-center justify-between gap-4">
        <div class="text-gray-300 text-sm">
          Mostrando <span class="text-white font-semibold">{{ empleadosFiltrados.length }}</span> de <span class="text-white font-semibold">{{ empleados.length }}</span> empleados
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
  empleados: {
    type: Array,
    default: () => []
  },
  nombreArea: {
    type: String,
    required: true
  },
  iconoArea: {
    type: String,
    default: 'users'
  }
})

defineEmits(['nuevoEmpleado', 'editar', 'eliminar', 'volver'])

const busqueda = ref('')

const empleadosFiltrados = computed(() => {
  if (!busqueda.value) return props.empleados
  
  return props.empleados.filter(e => 
    e.nombre.toLowerCase().includes(busqueda.value.toLowerCase()) ||
    e.cargo.toLowerCase().includes(busqueda.value.toLowerCase()) ||
    e.correo.toLowerCase().includes(busqueda.value.toLowerCase())
  )
})
</script>