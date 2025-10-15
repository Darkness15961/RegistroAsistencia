<template>
  <div class="w-full">
    <!-- Header personalizado (slot) -->
    <slot name="header-title">
      <!-- Header con búsqueda y botón (por defecto) -->
      <div 
        v-if="showHeader"
        class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-4"
      >
        <div>
          <h1 class="text-2xl sm:text-3xl font-bold mb-2" :class="theme('cardTitle').value">
            {{ title }}
          </h1>
          <p class="text-sm" :class="theme('cardSubtitle').value">
            {{ subtitle }}
          </p>
        </div>
      </div>
    </slot>

    <!-- Búsqueda y botón de acción -->
    <div 
      v-if="showHeader"
      class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-4"
    >
      <!-- Búsqueda -->
      <div class="relative w-full sm:flex-1 sm:max-w-md">
        <input
          v-model="searchQuery"
          type="text"
          :placeholder="searchPlaceholder"
          class="w-full px-4 py-2 pr-10 rounded-lg border transition-colors"
          :class="theme('input').value"
        />
        <svg 
          class="w-5 h-5 absolute right-3 top-1/2 -translate-y-1/2"
          :class="theme('cardSubtitle').value"
          fill="none" 
          stroke="currentColor" 
          viewBox="0 0 24 24"
        >
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
      </div>

      <!-- Botón de acción -->
      <button
        v-if="showActionButton"
        class="w-full sm:w-auto px-6 py-2 rounded-lg transition-all duration-200 flex items-center justify-center gap-2 shadow-lg whitespace-nowrap"
        :class="theme('buttonPrimary').value"
        @click="$emit('action-click')"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
        </svg>
        {{ actionButtonText }}
      </button>
    </div>

    <!-- Vista Desktop: Tabla -->
    <div class="hidden md:block rounded-xl border overflow-hidden shadow-2xl" :class="theme('card').value">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead :class="theme('tableHeader').value">
            <tr class="border-b" :class="isDark ? 'border-white/20' : 'border-gray-200'">
              <th
                v-for="column in columns"
                :key="column.key"
                class="px-4 py-4 text-left text-sm font-bold"
                :class="[column.headerClass, theme('cardSubtitle').value]"
              >
                {{ column.label }}
              </th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(row, index) in filteredData"
              :key="row.id || index"
              class="border-b transition-all"
              :class="[
                theme('tableRow').value,
                isDark ? 'border-white/5' : 'border-gray-100',
                index % 2 === 0 ? (isDark ? 'bg-white/[0.03]' : 'bg-gray-50/50') : ''
              ]"
            >
              <td
                v-for="column in columns"
                :key="column.key"
                class="px-4 py-4"
                :class="column.cellClass"
              >
                <!-- Slot personalizado por columna -->
                <slot 
                  :name="`cell-${column.key}`" 
                  :row="row" 
                  :value="row[column.key]"
                >
                  <span :class="theme('cardTitle').value">
                    {{ row[column.key] }}
                  </span>
                </slot>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Estado vacío -->
        <div 
          v-if="filteredData.length === 0"
          class="text-center py-12"
          :class="theme('cardSubtitle').value"
        >
          <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
          </svg>
          <p class="text-lg">{{ emptyMessage }}</p>
        </div>
      </div>

      <!-- Footer con estadísticas -->
      <div 
        v-if="filteredData.length > 0"
        class="border-t px-6 py-4 flex flex-wrap items-center justify-between gap-4"
        :class="[
          isDark ? 'bg-white/5 border-white/10' : 'bg-gray-50 border-gray-200'
        ]"
      >
        <div class="text-sm" :class="theme('cardSubtitle').value">
          Mostrando <span class="font-semibold" :class="theme('cardTitle').value">{{ filteredData.length }}</span> 
          de <span class="font-semibold" :class="theme('cardTitle').value">{{ data.length }}</span> registros
        </div>
        <div class="flex gap-2" v-if="showPagination">
          <button 
            class="px-3 py-1.5 rounded-lg text-sm transition-all border"
            :class="theme('buttonSecondary').value"
          >
            Anterior
          </button>
          <button 
            class="px-3 py-1.5 rounded-lg font-medium text-sm border"
            :class="isDark ? 'bg-white/10 border-white/20 text-white' : 'bg-gray-200 border-gray-300 text-gray-900'"
          >
            1
          </button>
          <button 
            class="px-3 py-1.5 rounded-lg text-sm transition-all border"
            :class="theme('buttonSecondary').value"
          >
            2
          </button>
          <button 
            class="px-3 py-1.5 rounded-lg text-sm transition-all border"
            :class="theme('buttonSecondary').value"
          >
            Siguiente
          </button>
        </div>
      </div>
    </div>

    <!-- Vista Mobile: Cards -->
    <div class="md:hidden space-y-4">
      <div
        v-for="(row, index) in filteredData"
        :key="row.id || index"
        class="rounded-xl p-4 border shadow-lg"
        :class="theme('card').value"
      >
        <!-- Slot personalizado para card mobile -->
        <slot name="mobile-card" :row="row">
          <!-- Fallback: mostrar todos los campos -->
          <div class="space-y-3">
            <div
              v-for="column in columns"
              :key="column.key"
              class="flex justify-between items-start gap-2"
            >
              <span 
                class="text-sm font-medium"
                :class="theme('cardSubtitle').value"
              >
                {{ column.label }}:
              </span>
              <div class="text-right">
                <slot 
                  :name="`cell-${column.key}`" 
                  :row="row" 
                  :value="row[column.key]"
                >
                  <span :class="theme('cardTitle').value">
                    {{ row[column.key] }}
                  </span>
                </slot>
              </div>
            </div>
          </div>
        </slot>
      </div>

      <!-- Estado vacío mobile -->
      <div 
        v-if="filteredData.length === 0"
        class="text-center py-12 rounded-xl border"
        :class="[theme('card').value, theme('cardSubtitle').value]"
      >
        <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
        </svg>
        <p class="text-lg">{{ emptyMessage }}</p>
      </div>

      <!-- Footer mobile -->
      <div 
        v-if="filteredData.length > 0"
        class="text-center text-sm py-3"
        :class="theme('cardSubtitle').value"
      >
        Mostrando <span class="font-semibold" :class="theme('cardTitle').value">{{ filteredData.length }}</span> 
        de <span class="font-semibold" :class="theme('cardTitle').value">{{ data.length }}</span> registros
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useTheme } from '../composables/useTheme'

const { theme, isDark } = useTheme()

const props = defineProps({
  columns: {
    type: Array,
    required: true
    // Formato: [{ key: 'id', label: 'ID', headerClass: '', cellClass: '' }]
  },
  data: {
    type: Array,
    required: true
  },
  title: {
    type: String,
    default: ''
  },
  subtitle: {
    type: String,
    default: ''
  },
  showHeader: {
    type: Boolean,
    default: true
  },
  searchPlaceholder: {
    type: String,
    default: 'Buscar...'
  },
  searchKeys: {
    type: Array,
    default: () => []
    // Claves para buscar, ej: ['name', 'email']
  },
  showActionButton: {
    type: Boolean,
    default: false
  },
  actionButtonText: {
    type: String,
    default: 'Agregar'
  },
  emptyMessage: {
    type: String,
    default: 'No hay datos disponibles'
  },
  showPagination: {
    type: Boolean,
    default: true
  }
})

defineEmits(['action-click'])

const searchQuery = ref('')

// Filtrado de datos
const filteredData = computed(() => {
  if (!searchQuery.value || props.searchKeys.length === 0) {
    return props.data
  }

  const query = searchQuery.value.toLowerCase()
  return props.data.filter(row => {
    return props.searchKeys.some(key => {
      const value = row[key]
      return value && value.toString().toLowerCase().includes(query)
    })
  })
})
</script>