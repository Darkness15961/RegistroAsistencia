<template>
  <VentanaModal @close="$emit('close')">
    <template #header>
      <i class="fas fa-clock mr-2"></i>
      Crear Nuevo Horario
    </template>
    
    <template #body>
      <form @submit.prevent="submitForm" class="space-y-4">
        <div>
          <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">
            Nombre del Área / Nivel
          </label>
          <input 
            v-model="form.area"
            type="text" 
            placeholder="Ej: Primaria"
            class="w-full rounded-xl"
            :class="theme('input').value"
            required
          >
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">
              Hora de Entrada
            </label>
            <input 
              v-model="form.entrada"
              type="time" 
              class="w-full rounded-xl"
              :class="theme('input').value"
              required
            >
          </div>
          <div>
            <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">
              Hora de Salida
            </label>
            <input 
              v-model="form.salida"
              type="time" 
              class="w-full rounded-xl"
              :class="theme('input').value"
              required
            >
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">
            Turno
          </label>
          <select 
            v-model="form.turno" 
            class="w-full rounded-xl appearance-none pr-8 focus:ring-2 focus:ring-blue-500" 
            :class="[theme('input').value, isDark ? 'text-white bg-gray-700 border-gray-600' : 'text-gray-900 bg-white border-gray-300']"
            required
          >
            <option value="Mañana" :class="isDark ? 'text-white bg-gray-700' : 'text-gray-900 bg-white'">Mañana</option>
            <option value="Tarde" :class="isDark ? 'text-white bg-gray-700' : 'text-gray-900 bg-white'">Tarde</option>
            <option value="Noche" :class="isDark ? 'text-white bg-gray-700' : 'text-gray-900 bg-white'">Noche</option>
            <option value="Completo" :class="isDark ? 'text-white bg-gray-700' : 'text-gray-900 bg-white'">Completo</option>
          </select>
        </div>
      </form>
    </template>
    
    <template #footer>
      <button 
        @click="$emit('close')"
        type="button"
        class="px-5 py-2.5 rounded-xl font-semibold"
        :class="theme('buttonSecondary').value"
      >
        Cancelar
      </button>
      <button 
        @click="submitForm"
        type="button"
        class="flex items-center gap-2 px-5 py-2.5 rounded-xl font-semibold shadow-lg"
        :class="theme('buttonPrimary').value"
      >
        <i class="fas fa-save"></i>
        Guardar Horario
      </button>
    </template>
  </VentanaModal>
</template>

<script setup>
import { ref } from 'vue'
import { useTheme } from '../composables/useTheme'
import VentanaModal from './VentanaModal.vue'

// Importamos 'isDark' para el fix del select
const { theme, isDark } = useTheme() 
const emit = defineEmits(['close', 'save'])

const form = ref({
  area: '',
  entrada: '08:00',
  salida: '16:00',
  turno: 'Mañana'
})

const submitForm = () => {
  if (form.value.area && form.value.entrada && form.value.salida && form.value.turno) {
    emit('save', { ...form.value })
  } else {
    console.warn('Formulario incompleto')
  }
}
</script>