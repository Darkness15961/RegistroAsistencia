<template>
  <div class="w-full">
    <div class="mb-6">
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
          <h1 class="text-2xl sm:text-3xl font-bold mb-2" :class="theme('cardTitle').value">
            <i class="fas fa-users mr-2"></i>
            {{ titulo }}
          </h1>
          <p :class="theme('cardSubtitle').value" class="text-sm">
            Gestiona el personal del área
          </p>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-4">
          <button 
            @click="$emit('nuevoEmpleado')"
            class="flex items-center gap-2 px-6 py-3 rounded-xl font-semibold shadow-lg hover:scale-105 transition-all duration-200 whitespace-nowrap"
            :class="theme('buttonPrimary').value"
          >
            <i class="fas fa-plus"></i>
            Nuevo Empleado
          </button>
          
          <button 
            @click="$emit('volver')"
            class="flex items-center gap-2 px-6 py-3 rounded-xl font-semibold shadow-lg hover:scale-105 transition-all duration-200 whitespace-nowrap"
            :class="theme('buttonPrimary').value" 
          >
            <i class="fas fa-arrow-left"></i>
            Volver
          </button>
        </div>
      </div>

      <div class="relative w-full sm:max-w-md mb-6">
        <input
          v-model="busqueda"
          type="text"
          placeholder="Buscar por nombre, DNI o cargo..."
          class="w-full rounded-xl pl-10 pr-4 py-3 outline-none border transition-colors"
          :class="theme('input').value"
        />
        <i 
          class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2"
          :class="theme('cardSubtitle').value"
        ></i>
      </div>
    </div>

    <div 
      class="hidden md:block backdrop-blur-xl border rounded-3xl overflow-hidden shadow-2xl"
      :class="theme('card').value"
    >
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead :class="theme('tableHeader').value">
            <tr class="border-b" :class="isDark ? 'border-white/20' : 'border-gray-200'">
              <th class="text-left p-4 font-bold text-sm" :class="theme('cardSubtitle').value">ID</th>
              <th class="text-left p-4 font-bold text-sm" :class="theme('cardSubtitle').value">Nombre</th>
              <th class="text-left p-4 font-bold text-sm" :class="theme('cardSubtitle').value">Cargo</th>
              <th class="text-left p-4 font-bold text-sm" :class="theme('cardSubtitle').value">Email</th>
              <th class="text-left p-4 font-bold text-sm" :class="theme('cardSubtitle').value">Estado</th>
              <th class="text-center p-4 font-bold text-sm" :class="theme('cardSubtitle').value">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="empleadosFiltrados.length === 0">
              <td colspan="6" class="text-center p-8" :class="theme('cardSubtitle').value">
                <i class="fas fa-user-slash text-5xl mb-3 opacity-50"></i>
                <p>No se encontraron empleados</p>
              </td>
            </tr>
            
            <tr 
              v-for="(empleado, index) in empleadosFiltrados" 
              :key="empleado.id"
              class="border-b transition-all"
              :class="[
                theme('tableRow').value,
                isDark ? 'border-white/5' : 'border-gray-100',
                index % 2 === 0 ? (isDark ? 'bg-white/[0.03]' : 'bg-gray-50/50') : ''
              ]"
            >
              <td class="p-4">
                <span class="font-mono text-sm" :class="theme('cardSubtitle').value">{{ empleado.id }}</span>
              </td>
              
              <td class="p-4">
                <div class="flex items-center gap-3">
                  <img :src="`https://i.pravatar.cc/150?img=${empleado.id}`" alt="Avatar" class="w-10 h-10 rounded-xl object-cover shadow-lg">
                  <div>
                    <span class="font-semibold" :class="theme('cardTitle').value">{{ empleado.nombre }}</span>
                    <p class="text-xs font-mono" :class="theme('cardSubtitle').value">DNI: {{ empleado.dni }}</p>
                  </div>
                </div>
              </td>
              
              <td class="p-4" :class="theme('cardSubtitle').value">
                {{ empleado.cargo }}
              </td>
              
              <td class="p-4" :class="theme('cardSubtitle').value">
                {{ empleado.email }}
              </td>

              <td class="p-4">
                <span 
                  class="px-3 py-1.5 rounded-full text-xs font-bold inline-flex items-center gap-2 border"
                  :class="empleado.estado === 'Activo' 
                    ? (isDark ? 'bg-green-500/20 border-green-500/30 text-green-200' : 'bg-green-50 border-green-200 text-green-700') 
                    : (isDark ? 'bg-red-500/20 border-red-500/30 text-red-200' : 'bg-red-50 border-red-200 text-red-700')"
                >
                  <i class="fas fa-circle text-xs" :class="empleado.estado === 'Activo' ? (isDark ? 'text-green-300' : 'text-green-500') : (isDark ? 'text-red-300' : 'text-red-500')"></i>
                  {{ empleado.estado }}
                </span>
              </td>
              
              <td class="p-4">
                <div class="flex justify-center gap-2">
                  <button 
                    @click="$emit('editar', empleado)"
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
                    @click="$emit('eliminar', empleado.id)"
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

      <div 
        class="border-t px-6 py-4 flex flex-wrap items-center justify-between gap-4"
        :class="isDark ? 'bg-white/5 border-white/10' : 'bg-gray-50 border-gray-200'"
      >
        <div class="text-sm" :class="theme('cardSubtitle').value">
          Mostrando <span class="font-semibold" :class="theme('cardTitle').value">{{ empleadosFiltrados.length }}</span> 
          de <span class="font-semibold" :class="theme('cardTitle').value">{{ empleados.length }}</span> empleados
        </div>
      </div>
    </div>

    <div class="grid gap-4 md:hidden">
      <div
        v-if="empleadosFiltrados.length === 0"
        class="text-center py-12 rounded-xl border"
        :class="[theme('card').value, theme('cardSubtitle').value]"
      >
        <i class="fas fa-user-slash text-5xl mb-4 opacity-50"></i>
        <p class="text-lg">No se encontraron empleados</p>
      </div>

      <div
        v-for="empleado in empleadosFiltrados"
        :key="empleado.id"
        class="backdrop-blur-xl border rounded-3xl shadow-lg p-5"
        :class="theme('card').value"
      >
        <div class="flex items-start justify-between gap-3 mb-4">
          <div class="flex items-center gap-3">
            <img :src="`https://i.pravatar.cc/150?img=${empleado.id}`" alt="Avatar" class="w-12 h-12 rounded-2xl object-cover shadow-lg">
            <div>
              <h2 class="text-lg font-semibold" :class="theme('cardTitle').value">
                {{ empleado.nombre }}
              </h2>
              <span class="text-xs px-2 py-1 rounded-full" :class="isDark ? 'bg-white/10 text-gray-300' : 'bg-gray-100 text-gray-600'">
                DNI: {{ empleado.dni }}
              </span>
            </div>
          </div>

          <div class="flex gap-2">
            <button
              @click="$emit('editar', empleado)"
              class="p-2 rounded-xl transition-all hover:scale-110 group"
              :class="isDark
                ? 'bg-blue-500/20 hover:bg-blue-500/30 border border-blue-500/30'
                : 'bg-blue-50 hover:bg-blue-100 border border-blue-200'"
              title="Editar"
            >
              <i
                class="fas fa-edit"
                :class="isDark ? 'text-blue-300 group-hover:text-blue-200' : 'text-blue-600 group-hover:text-blue-700'"
              ></i>
            </button>
            <button
              @click="$emit('eliminar', empleado.id)"
              class="p-2 rounded-xl transition-all hover:scale-110 group"
              :class="isDark
                ? 'bg-red-500/20 hover:bg-red-500/30 border border-red-500/30'
                : 'bg-red-50 hover:bg-red-100 border border-red-200'"
              title="Eliminar"
            >
              <i
                class="fas fa-trash"
                :class="isDark ? 'text-red-300 group-hover:text-red-200' : 'text-red-600 group-hover:text-red-700'"
              ></i>
            </button>
          </div>
        </div>
        
        <div class="flex flex-wrap items-center justify-between gap-3 pt-3 border-t" :class="isDark ? 'border-white/10' : 'border-gray-200'">
            <div class="flex items-center gap-2">
                <i class="fas fa-briefcase text-sm" :class="theme('cardSubtitle').value"></i>
                <span class="font-medium text-sm" :class="theme('cardTitle').value">{{ empleado.cargo }}</span>
            </div>
            <div class="flex items-center gap-2">
                <i class="fas fa-envelope text-sm" :class="theme('cardSubtitle').value"></i>
                <span class="font-medium text-sm" :class="theme('cardTitle').value">{{ empleado.email }}</span>
            </div>
             <div 
              class="px-3 py-1.5 rounded-full text-xs font-bold inline-flex items-center gap-2 border"
              :class="empleado.estado === 'Activo' 
                ? (isDark ? 'bg-green-500/20 border-green-500/30 text-green-200' : 'bg-green-50 border-green-200 text-green-700') 
                : (isDark ? 'bg-red-500/20 border-red-500/30 text-red-200' : 'bg-red-50 border-red-200 text-red-700')"
            >
              <i class="fas fa-circle text-xs" :class="empleado.estado === 'Activo' ? (isDark ? 'text-green-300' : 'text-green-500') : (isDark ? 'text-red-300' : 'text-red-500')"></i>
              {{ empleado.estado }}
            </div>
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
  titulo: {
    type: String,
    default: 'Empleados'
  },
  empleados: {
    type: Array,
    default: () => []
  }
})

// CORRECCIÓN: Se añade 'volver' a los emits
defineEmits(['nuevoEmpleado', 'editar', 'eliminar', 'volver'])

const busqueda = ref('')

const empleadosFiltrados = computed(() => {
  if (!busqueda.value) return props.empleados
  
  const lowerBusqueda = busqueda.value.toLowerCase()
  return props.empleados.filter(e => 
    (e.nombre && e.nombre.toLowerCase().includes(lowerBusqueda)) ||
    (e.dni && e.dni.toLowerCase().includes(lowerBusqueda)) ||
    (e.cargo && e.cargo.toLowerCase().includes(lowerBusqueda))
  )
})
</script>