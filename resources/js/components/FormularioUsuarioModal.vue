<template>
  <VentanaModal @close="$emit('close')">
    <template #header>
      <i class="fas fa-user-plus mr-2"></i>
      Crear Nuevo Usuario
    </template>
    
    <template #body>
      <form @submit.prevent="submitForm" class="space-y-4">
        <div>
          <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">Nombre Completo</label>
          <input 
            v-model="form.name"
            type="text" 
            placeholder="Ej: María Benítez"
            class="w-full rounded-xl"
            :class="theme('input').value"
            required
          >
        </div>
        
        <div>
          <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">Email</label>
          <input 
            v-model="form.email"
            type="email" 
            placeholder="Ej: m.benitez@escuela.edu"
            class="w-full rounded-xl"
            :class="theme('input').value"
            required
          >
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">Rol</label>
            <select 
              v-model="form.role" 
              class="w-full rounded-xl appearance-none pr-8 focus:ring-2 focus:ring-blue-500" 
              :class="[theme('input').value, isDark ? 'text-white bg-gray-700 border-gray-600' : 'text-gray-900 bg-white border-gray-300']"
              required
            >
              <option 
                value="Administrador"
                :class="isDark ? 'text-white bg-gray-700 hover:bg-gray-600' : 'text-gray-900 bg-white hover:bg-gray-100'"
              >
                Administrador
              </option>
              <option 
                value="Docente"
                :class="isDark ? 'text-white bg-gray-700 hover:bg-gray-600' : 'text-gray-900 bg-white hover:bg-gray-100'"
              >
                Docente
              </option>
              <option 
                value="Secretaría"
                :class="isDark ? 'text-white bg-gray-700 hover:bg-gray-600' : 'text-gray-900 bg-white hover:bg-gray-100'"
              >
                Secretaría
              </option>
              <option 
                value="Psicología"
                :class="isDark ? 'text-white bg-gray-700 hover:bg-gray-600' : 'text-gray-900 bg-white hover:bg-gray-100'"
              >
                Psicología
              </option>
              <option 
                value="Tutora"
                :class="isDark ? 'text-white bg-gray-700 hover:bg-gray-600' : 'text-gray-900 bg-white hover:bg-gray-100'"
              >
                Tutor/a
              </option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">Contraseña</label>
            <input 
              v-model="form.password"
              type="password" 
              placeholder="••••••••"
              class="w-full rounded-xl"
              :class="theme('input').value"
              required
            >
          </div>
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
        Guardar Usuario
      </button>
    </template>
  </VentanaModal>
</template>

<script setup>
import { ref } from 'vue'
import { useTheme } from '../composables/useTheme'
import VentanaModal from './VentanaModal.vue'

// Importamos 'isDark' para aplicar estilos condicionales al select
const { theme, isDark } = useTheme()
const emit = defineEmits(['close', 'save'])

const form = ref({
  name: '',
  email: '',
  role: 'Docente',
  password: ''
})

const submitForm = () => {
  if (form.value.name && form.value.email && form.value.password) {
    emit('save', { ...form.value })
  } else {
    console.warn('Formulario incompleto')
  }
}
</script>