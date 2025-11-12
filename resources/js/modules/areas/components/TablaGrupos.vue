<template>
  <div class="w-full">
    <div class="mb-6">
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
          <h1 class="text-2xl sm:text-3xl font-bold mb-2" :class="theme('cardTitle').value">
            <i class="fas fa-layer-group mr-2"></i>
            {{ nombreArea }}
          </h1>
          <p :class="theme('cardSubtitle').value" class="text-sm">
            Grupos pertenecientes a esta área
          </p>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-4 w-full sm:w-auto">
          <button 
            @click="$emit('nuevoGrupo')"
            class="flex items-center justify-center gap-2 px-6 py-3 rounded-xl font-semibold shadow-lg"
            :class="theme('buttonPrimary').value"
          >
            <i class="fas fa-plus"></i>
            Nuevo Grupo
          </button>
          
          <button 
            @click="$emit('volver')"
            class="flex items-center justify-center gap-2 px-6 py-3 rounded-xl font-semibold shadow-lg"
            :class="theme('buttonSecondary').value" 
          >
            <i class="fas fa-arrow-left"></i>
            Volver a Áreas
          </button>
        </div>
      </div>
    </div>

    <div 
      class="hidden md:block backdrop-blur-xl border rounded-3xl overflow-hidden shadow-lg"
      :class="theme('card').value"
    >
      <table class="w-full text-left">
        <thead :class="theme('tableHeader').value">
          <tr class="border-b" :class="isDark ? 'border-white/20' : 'border-gray-200'">
            <th class="px-6 py-4 font-bold text-sm uppercase" :class="theme('cardSubtitle').value">Nivel</th>
            <th class="px-6 py-4 font-bold text-sm uppercase" :class="theme('cardSubtitle').value">Grado</th>
            <th class="px-6 py-4 font-bold text-sm uppercase" :class="theme('cardSubtitle').value">Sección</th>
            <th class="px-6 py-4 font-bold text-sm uppercase" :class="theme('cardSubtitle').value">Tutor</th>
            <th class="text-center px-6 py-4 font-bold text-sm uppercase" :class="theme('cardSubtitle').value">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="grupos.length === 0">
            <td colspan="5" class="text-center p-8" :class="theme('cardSubtitle').value">
              <i class="fas fa-box-open text-5xl mb-3 opacity-50"></i>
              <p>No se encontraron grupos en esta área</p>
            </td>
          </tr>
          <tr 
            v-for="(grupo, index) in grupos" 
            :key="grupo.id_grupo"
            class="border-b transition-all"
            :class="[theme('tableRow').value, isDark ? 'border-white/5' : 'border-gray-100']"
          >
            <td class="px-6 py-4 font-semibold" :class="theme('cardTitle').value">{{ grupo.nivel }}</td>
            <td class="px-6 py-4" :class="theme('cardSubtitle').value">{{ grupo.grado }}</td>
            <td class="px-6 py-4" :class="theme('cardSubtitle').value">{{ grupo.seccion }}</td>
            <td class="px-6 py-4" :class="theme('cardSubtitle').value">{{ grupo.tutor?.nombre_completo || 'No asignado' }}</td>
            <td class="px-6 py-4">
              <div class="flex justify-center gap-2">
                <button @click="$emit('editar', grupo)" class="p-2 w-10 h-10 rounded-xl" :class="theme('buttonSecondary').value" title="Editar">
                  <i class="fas fa-edit" :class="isDark 
                  ? 'text-blue-300' : 'text-blue-600'"></i>
                </button>
                <button @click="$emit('eliminar', grupo.id_grupo)" class="p-2 w-10 h-10 rounded-xl" :class="theme('buttonSecondary').value" title="Eliminar">
                  <i class="fas fa-trash" :class="isDark 
                  ? 'text-red-300' : 'text-red-600'"></i>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    
    <div class="grid gap-4 md:hidden">
      <div
        v-if="grupos.length === 0"
        class="text-center py-12 rounded-xl border"
        :class="[theme('card').value, theme('cardSubtitle').value]"
      >
        <i class="fas fa-box-open text-5xl mb-4 opacity-50"></i>
        <p class="text-lg">No se encontraron grupos</p>
      </div>
      <div
        v-for="grupo in grupos"
        :key="grupo.id_grupo"
        class="backdrop-blur-xl border rounded-3xl shadow-lg p-5"
        :class="theme('card').value"
      >
        <div class="flex items-start justify-between gap-3 mb-4">
          <div>
            <h2 class="text-lg font-semibold" :class="theme('cardTitle').value">
              {{ grupo.grado }} "{{ grupo.seccion }}"
            </h2>
            <p class="text-sm" :class="theme('cardSubtitle').value">{{ grupo.nivel }}</p>
          </div>
          <div class="flex gap-2">
            <button @click="$emit('editar', grupo)" class="p-2 w-10 h-10 rounded-xl" :class="theme('buttonSecondary').value" title="Editar">
              <i class="fas fa-edit" :class="isDark ? 'bg-blue-500/20' : 'text-blue-600'"></i>
            </button>
            <button @click="$emit('eliminar', grupo.id_grupo)" class="p-2 w-10 h-10 rounded-xl" :class="theme('buttonSecondary').value" title="Eliminar">
              <i class="fas fa-trash" :class="isDark ? 'text-red-300' : 'text-red-600'"></i>
            </button>
          </div>
        </div>
        <div class="pt-3 border-t" :class="isDark ? 'border-white/10' : 'border-gray-200'">
          <p class="text-sm" :class="theme('cardSubtitle').value">
            Tutor: <span :class="theme('cardTitle').value">{{ grupo.tutor?.nombre_completo || 'No asignado' }}</span>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useTheme } from '@/composables/useTheme'
const { theme, isDark } = useTheme()
defineProps({
  grupos: { type: Array, default: () => [] },
  nombreArea: { type: String, default: 'Área' }
})
defineEmits(['nuevoGrupo', 'editar', 'eliminar', 'volver'])
</script>