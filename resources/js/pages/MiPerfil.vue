<template>
  <div class="flex-1 p-4 sm:p-6 overflow-x-hidden">
    <PageHeader
      title="Mi Perfil"
      subtitle="Gestiona la información de tu cuenta y tu seguridad"
    />

    <div class="max-w-2xl mx-auto">
      <div 
        class="rounded-3xl shadow-lg p-6 sm:p-8"
        :class="theme('card').value"
      >
        <h2 class="text-xl font-semibold mb-6" :class="theme('cardTitle').value">
          <i class="fas fa-lock mr-2"></i>
          Cambiar Contraseña
        </h2>

        <form @submit.prevent="cambiarPassword" class="space-y-4">
          <div>
            <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">
              Contraseña Actual
            </label>
            <input 
              v-model="form.current_password"
              type="password" 
              placeholder="••••••••"
              class="w-full rounded-xl"
              :class="theme('input').value"
              required
            >
          </div>
          
          <div>
            <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">
              Nueva Contraseña
            </label>
            <input 
              v-model="form.new_password"
              type="password" 
              placeholder="••••••••"
              class="w-full rounded-xl"
              :class="theme('input').value"
              required
            >
          </div>
          
          <div>
            <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">
              Confirmar Nueva Contraseña
            </label>
            <input 
              v-model="form.new_password_confirmation"
              type="password" 
              placeholder="••••••••"
              class="w-full rounded-xl"
              :class="theme('input').value"
              required
            >
          </div>

          <div 
            v-if="message"
            :class="messageType === 'success' 
              ? 'text-green-500 bg-green-500/10 border border-green-500/20' 
              : 'text-red-500 bg-red-500/10 border border-red-500/20'"
            class="text-sm font-medium p-3 rounded-xl"
          >
            {{ message }}
          </div>

          <div class="pt-2">
            <button 
              type="submit"
              :disabled="loading"
              class="w-full flex items-center justify-center gap-2 px-6 py-3 rounded-xl font-semibold shadow-lg hover:scale-105 transition-all duration-200"
              :class="[theme('buttonPrimary').value, loading ? 'opacity-50 cursor-not-allowed' : '']"
            >
              <i v-if="loading" class="fas fa-spinner animate-spin"></i>
              {{ loading ? 'Guardando...' : 'Actualizar Contraseña' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useTheme } from '../composables/useTheme'
import PageHeader from '../components/PageHeader.vue' // Reutilizamos tu PageHeader
import axios from 'axios'

const { theme } = useTheme()

const form = ref({
  current_password: '',
  new_password: '',
  new_password_confirmation: ''
})

const loading = ref(false)
const message = ref(null)
const messageType = ref('error') // 'success' o 'error'

const cambiarPassword = async () => {
  loading.value = true
  message.value = null
  messageType.value = 'error'

  // Validar que las contraseñas coincidan
  if (form.value.new_password !== form.value.new_password_confirmation) {
    message.value = 'La nueva contraseña y la confirmación no coinciden.'
    loading.value = false
    return
  }

  try {
    const response = await axios.post('/api/perfil/cambiar-password', form.value)
    
    // Éxito
    message.value = response.data.message
    messageType.value = 'success'
    // Limpiar formulario
    form.value.current_password = ''
    form.value.new_password = ''
    form.value.new_password_confirmation = ''

  } catch (err) {
    // Manejar errores
    if (err.response && err.response.data.errors) {
      // Muestra el primer error de validación
      const errors = err.response.data.errors
      const firstErrorKey = Object.keys(errors)[0]
      message.value = errors[firstErrorKey][0]
    } else {
      message.value = 'Ocurrió un error inesperado.'
    }
    console.error('Error al cambiar contraseña:', err)
  } finally {
    loading.value = false
  }
}
</script>