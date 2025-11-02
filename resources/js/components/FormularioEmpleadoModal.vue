<template>
  <VentanaModal @close="$emit('close')">
    <template #header>
      <i class="fas fa-user-plus mr-2"></i>
      Crear Nuevo Empleado
    </template>
    
    <template #body>
      <form @submit.prevent="submitForm" class="space-y-4">
        <div>
          <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">
            Nombre Completo
          </label>
          <input 
            v-model="form.nombre"
            type="text" 
            placeholder="Ej: Carlos Ramírez López"
            class="w-full rounded-xl"
            :class="theme('input').value"
            required
          >
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">
              Cargo
            </label>
            <input 
              v-model="form.cargo"
              type="text" 
              placeholder="Ej: Estudiante 5to Secundaria"
              class="w-full rounded-xl"
              :class="theme('input').value"
              required
            >
          </div>
          <div>
            <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">
              Teléfono
            </label>
            <input 
              v-model="form.telefono"
              type="tel" 
              placeholder="Ej: 987654321"
              class="w-full rounded-xl"
              :class="theme('input').value"
            >
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">
            Correo Electrónico
          </label>
          <input 
            v-model="form.correo"
            type="email" 
            placeholder="Ej: carlos.ramirez@colegio.edu"
            class="w-full rounded-xl"
            :class="theme('input').value"
            required
          >
        </div>
        
        <div>
          <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">
            Estado
          </label>
          <select 
            v-model="form.estado" 
            class="w-full rounded-xl appearance-none pr-8 focus:ring-2 focus:ring-blue-500" 
            :class="[theme('input').value, isDark ? 'text-white bg-gray-700 border-gray-600' : 'text-gray-900 bg-white border-gray-300']"
            required
          >
            <option value="Activo" :class="isDark ? 'text-white bg-gray-700' : 'text-gray-900 bg-white'">Activo</option>
            <option value="Inactivo" :class="isDark ? 'text-white bg-gray-700' : 'text-gray-900 bg-white'">Inactivo</option>
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
        Guardar Empleado
      </button>
    </template>
  </VentanaModal>
</template>

<script setup>
import { ref } from 'vue'
import { useTheme } from '../composables/useTheme'
import VentanaModal from './VentanaModal.vue'

const { theme, isDark } = useTheme()
const emit = defineEmits(['close', 'save'])

const form = ref({
  nombre: '',
  cargo: '',
  correo: '',
  telefono: '',
  estado: 'Activo'
})

const submitForm = () => {
  if (form.value.nombre && form.value.cargo && form.value.correo) {
    emit('save', { ...form.value })
  } else {
    console.warn('Formulario de empleado incompleto')
  }
}
</script>