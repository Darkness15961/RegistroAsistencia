<template>
  <VentanaModal @close="$emit('close')">
    <template #header>
      <i class="fas fa-building mr-2"></i>
      Crear Nueva Área
    </template>
    
    <template #body>
      <form @submit.prevent="submitForm" class="space-y-4">
        <div>
          <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">
            Nombre del Área
          </label>
          <input 
            v-model="form.nombre"
            type="text" 
            placeholder="Ej: Docentes de Primaria"
            class="w-full rounded-xl"
            :class="theme('input').value"
            required
          >
        </div>
        
        <div>
          <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">
            Descripción
          </label>
          <textarea
            v-model="form.descripcion"
            rows="3"
            placeholder="Ej: Profesores encargados del nivel primaria"
            class="w-full rounded-xl"
            :class="theme('input').value"
          ></textarea>
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
        Guardar Área
      </button>
    </template>
  </VentanaModal>
</template>

<script setup>
import { ref } from 'vue'
import { useTheme } from '../composables/useTheme'
import VentanaModal from './VentanaModal.vue' // Importamos el modal base

const { theme } = useTheme()
const emit = defineEmits(['close', 'save'])

// Estado local para el formulario
const form = ref({
  nombre: '',
  descripcion: ''
})

// Función para enviar el formulario
const submitForm = () => {
  if (form.value.nombre) {
    emit('save', { ...form.value }) // Enviamos una copia de los datos
  } else {
    // Manejo de error simple
    console.warn('El nombre del área es requerido')
  }
}
</script>