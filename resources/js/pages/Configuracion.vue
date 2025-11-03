<template>
  <div class="flex-1 p-4 sm:p-6 overflow-x-hidden">
    <PageHeader
      title="Configuración"
      subtitle="Configura las opciones generales del sistema"
    />

    <div 
      class="max-w-2xl mx-auto rounded-3xl shadow-lg p-6 sm:p-8"
      :class="theme('card').value"
    >
      <form @submit.prevent="guardarConfiguraciones" class="space-y-6">
        
        <div>
          <label for="nombre_institucion" class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">
            Nombre de la Institución
          </label>
          <input 
            v-model="config.nombre_institucion"
            type="text" 
            id="nombre_institucion"
            class="w-full rounded-xl"
            :class="theme('input').value"
            required
          >
          <p class="text-xs mt-1.5" :class="theme('cardSubtitle').value">
          </p>
        </div>

        <div>
          <label for="tolerancia_tardanza" class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">
            Tolerancia de Tardanza (en minutos)
          </label>
          <input 
            v-model="config.tolerancia_tardanza"
            type="number" 
            id="tolerancia_tardanza"
            class="w-full rounded-xl"
            :class="theme('input').value"
            required
            min="0"
          >
          <p class="text-xs mt-1.5" :class="theme('cardSubtitle').value">
            Minutos después de la hora de entrada antes de marcar "Tarde".
          </p>
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

        <div class="pt-2 text-right">
          <button 
            type="submit"
            :disabled="loading"
            class="w-full sm:w-auto flex items-center justify-center gap-2 px-6 py-3 rounded-xl font-semibold shadow-lg hover:scale-105 transition-all duration-200"
            :class="[theme('buttonPrimary').value, loading ? 'opacity-50 cursor-not-allowed' : '']"
          >
            <i v-if="loading" class="fas fa-spinner animate-spin"></i>
            {{ loading ? 'Guardando...' : 'Guardar Cambios' }}
          </button>
        </div>

      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useTheme } from '../composables/useTheme'
import PageHeader from '../components/PageHeader.vue'
import axios from 'axios'

const { theme } = useTheme()

// Ref para el formulario
const config = ref({
  nombre_institucion: 'Colegio 4scan',
  tolerancia_tardanza: 10
})

const loading = ref(false)
const message = ref(null)
const messageType = ref('error')

// Cargar la configuración actual al montar la página
onMounted(async () => {
  loading.value = true
  try {
    const response = await axios.get('/api/configuraciones')
    config.value = response.data
  } catch (err) {
    message.value = 'No se pudo cargar la configuración.'
    console.error(err)
  } finally {
    loading.value = false
  }
})

// Guardar la configuración
const guardarConfiguraciones = async () => {
  loading.value = true
  message.value = null
  messageType.value = 'error'

  try {
    const response = await axios.post('/api/configuraciones', config.value)
    message.value = response.data.message
    messageType.value = 'success'
  } catch (err) {
    message.value = 'Ocurrió un error al guardar.'
    console.error(err)
  } finally {
    loading.value = false
  }
}
</script>