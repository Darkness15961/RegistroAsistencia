<template>
  <div class="w-full">
    <!-- Header personalizado -->
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
      <div>
        <h2 class="text-2xl font-bold mb-1" :class="theme('cardTitle').value">
          <i :class="`fas fa-${iconoArea} mr-2`"></i>
          {{ nombreArea }}
        </h2>
        <p class="text-sm" :class="theme('cardSubtitle').value">
          {{ empleados.length }} personas registradas
        </p>
      </div>
      
      <div class="flex gap-3">
        <button 
          @click="$emit('volver')"
          class="px-4 py-2 rounded-xl font-medium transition-all border"
          :class="theme('buttonSecondary').value"
        >
          <i class="fas fa-arrow-left mr-2"></i>
          Volver
        </button>
        <button 
          @click="$emit('nuevoEmpleado')"
          class="px-5 py-2.5 rounded-xl font-medium transition-all transform hover:scale-105 shadow-lg"
          :class="theme('buttonPrimary').value"
        >
          <i class="fas fa-plus mr-2"></i>
          Nuevo Empleado
        </button>
      </div>
    </div>

    <!-- Tabla Responsive -->
    <ResponsiveTable
      :columns="columns"
      :data="empleados"
      :search-keys="['nombre', 'cargo', 'correo']"
      search-placeholder="Buscar por nombre, cargo o correo..."
      :show-header="true"
      :show-action-button="false"
      empty-message="No se encontraron empleados"
    >
      <!-- Columna: Nombre -->
      <template #cell-nombre="{ row }">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-400 to-pink-500 flex items-center justify-center shadow-lg">
            <span class="text-white font-bold text-sm">
              {{ row.nombre.split(' ').map(n => n[0]).join('').substring(0, 2) }}
            </span>
          </div>
          <span class="font-semibold" :class="theme('cardTitle').value">
            {{ row.nombre }}
          </span>
        </div>
      </template>

      <!-- Columna: Cargo -->
      <template #cell-cargo="{ value }">
        <div class="bg-blue-500/20 border border-blue-500/30 px-3 py-1 rounded-lg inline-block">
          <span class="text-blue-200 text-sm font-medium">{{ value }}</span>
        </div>
      </template>

      <!-- Columna: Correo -->
      <template #cell-correo="{ value }">
        <div class="flex items-center gap-2">
          <i class="fas fa-envelope text-sm" :class="theme('cardSubtitle').value"></i>
          <span class="text-sm" :class="theme('cardSubtitle').value">{{ value }}</span>
        </div>
      </template>

      <!-- Columna: Teléfono -->
      <template #cell-telefono="{ value }">
        <div class="flex items-center gap-2">
          <i class="fas fa-phone text-sm" :class="theme('cardSubtitle').value"></i>
          <span class="text-sm" :class="theme('cardSubtitle').value">{{ value }}</span>
        </div>
      </template>

      <!-- Columna: Estado -->
      <template #cell-estado="{ value }">
        <div 
          :class="[
            'px-3 py-1 rounded-full inline-flex items-center gap-2 text-xs font-medium border',
            value === 'Activo' 
              ? 'bg-green-500/20 border-green-500/30 text-green-200' 
              : 'bg-red-500/20 border-red-500/30 text-red-200'
          ]"
        >
          <div 
            :class="[
              'w-2 h-2 rounded-full',
              value === 'Activo' ? 'bg-green-400' : 'bg-red-400'
            ]"
          ></div>
          {{ value }}
        </div>
      </template>

      <!-- Columna: Acciones -->
      <template #cell-acciones="{ row }">
        <div class="flex justify-center gap-2">
          <button 
            @click="$emit('editar', row)"
            class="bg-blue-500/20 hover:bg-blue-500/30 border border-blue-500/30 p-2 rounded-xl transition-all hover:scale-110 group"
          >
            <i class="fas fa-edit text-blue-300 group-hover:text-blue-200"></i>
          </button>
          <button 
            @click="$emit('eliminar', row.id)"
            class="bg-red-500/20 hover:bg-red-500/30 border border-red-500/30 p-2 rounded-xl transition-all hover:scale-110 group"
          >
            <i class="fas fa-trash text-red-300 group-hover:text-red-200"></i>
          </button>
        </div>
      </template>

      <!-- Card Mobile personalizado -->
      <template #mobile-card="{ row }">
        <div class="space-y-4">
          <!-- Header del card -->
          <div class="flex items-start justify-between pb-3 border-b" :class="isDark ? 'border-white/10' : 'border-gray-200'">
            <div class="flex items-center gap-3">
              <div class="w-12 h-12 rounded-full bg-gradient-to-br from-purple-400 to-pink-500 flex items-center justify-center shadow-lg">
                <span class="text-white font-bold">
                  {{ row.nombre.split(' ').map(n => n[0]).join('').substring(0, 2) }}
                </span>
              </div>
              <div>
                <h3 class="font-bold" :class="theme('cardTitle').value">
                  {{ row.nombre }}
                </h3>
                <p class="text-xs" :class="theme('cardSubtitle').value">
                  ID: {{ row.id }}
                </p>
              </div>
            </div>
            <div 
              :class="[
                'px-2 py-1 rounded-full inline-flex items-center gap-1 text-xs font-medium border',
                row.estado === 'Activo' 
                  ? 'bg-green-500/20 border-green-500/30 text-green-200' 
                  : 'bg-red-500/20 border-red-500/30 text-red-200'
              ]"
            >
              <div 
                :class="[
                  'w-2 h-2 rounded-full',
                  row.estado === 'Activo' ? 'bg-green-400' : 'bg-red-400'
                ]"
              ></div>
              {{ row.estado }}
            </div>
          </div>

          <!-- Info -->
          <div class="space-y-3">
            <div class="bg-blue-500/20 border border-blue-500/30 px-3 py-2 rounded-lg">
              <p class="text-xs text-blue-300 mb-1">Cargo</p>
              <p class="text-blue-200 font-medium">{{ row.cargo }}</p>
            </div>

            <div class="grid grid-cols-1 gap-2">
              <div class="flex items-center gap-2">
                <i class="fas fa-envelope text-sm" :class="theme('cardSubtitle').value"></i>
                <span class="text-sm" :class="theme('cardSubtitle').value">{{ row.correo }}</span>
              </div>
              <div class="flex items-center gap-2">
                <i class="fas fa-phone text-sm" :class="theme('cardSubtitle').value"></i>
                <span class="text-sm" :class="theme('cardSubtitle').value">{{ row.telefono }}</span>
              </div>
            </div>
          </div>

          <!-- Acciones -->
          <div class="flex gap-2 pt-2">
            <button 
              @click="$emit('editar', row)"
              class="flex-1 bg-blue-500/20 hover:bg-blue-500/30 border border-blue-500/30 py-2 rounded-xl transition-all flex items-center justify-center gap-2"
            >
              <i class="fas fa-edit text-blue-300"></i>
              <span class="text-blue-300 font-medium text-sm">Editar</span>
            </button>
            <button 
              @click="$emit('eliminar', row.id)"
              class="flex-1 bg-red-500/20 hover:bg-red-500/30 border border-red-500/30 py-2 rounded-xl transition-all flex items-center justify-center gap-2"
            >
              <i class="fas fa-trash text-red-300"></i>
              <span class="text-red-300 font-medium text-sm">Eliminar</span>
            </button>
          </div>
        </div>
      </template>
    </ResponsiveTable>
  </div>
</template>

<script setup>
import ResponsiveTable from './ResponsiveTable.vue'
import { useTheme } from '../composables/useTheme'

const { theme, isDark } = useTheme()

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

const columns = [
  { key: 'id', label: 'ID' },
  { key: 'nombre', label: 'Nombre Completo' },
  { key: 'cargo', label: 'Cargo' },
  { key: 'correo', label: 'Correo' },
  { key: 'telefono', label: 'Teléfono' },
  { key: 'estado', label: 'Estado' },
  { key: 'acciones', label: 'Acciones' }
]
</script>