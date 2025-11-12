<template>
  <div class="p-4 sm:p-6 max-w-3xl mx-auto">
    
    <div v-if="successMessage"
         class="text-sm rounded-lg p-4 mb-4 border"
         :class="isDark 
           ? 'bg-green-500/20 border-green-500/30 text-green-300' 
           : 'bg-green-100 border-green-300 text-green-800'">
      {{ successMessage }}
    </div>
    <div v-if="errorMessage"
         class="text-sm rounded-lg p-4 mb-4 border"
         :class="isDark 
           ? 'bg-red-500/20 border-red-500/30 text-red-400'
           : 'bg-red-100 border-red-300 text-red-800'">
      {{ errorMessage }}
    </div>

    <div class="p-6 rounded-2xl border" :class="theme('card').value">
      <h1 class="text-2xl font-bold mb-6" :class="theme('cardTitle').value">
        Configuración del Sistema
      </h1>

      <div v-if="loading.load" class="text-center py-10" :class="theme('cardSubtitle').value">
        <i class="fas fa-spinner fa-spin text-3xl"></i>
        <p class="mt-2">Cargando configuración...</p>
      </div>

      <form v-else @submit.prevent="saveSettings" class="space-y-5">
        
        <div>
          <label for="nombre_institucion" class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">
            Nombre de la Institución
          </label>
          <input 
            id="nombre_institucion"
            v-model="settings.nombre_institucion" 
            type="text" 
            placeholder="Ej: Colegio 4Scan"
            :class="theme('input').value" 
            class="w-full rounded-xl"
          />
        </div>

        <div>
          <label for="tolerancia_tardanza" class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">
            Tolerancia de Tardanza (en minutos)
          </label>
          <input 
            id="tolerancia_tardanza"
            v-model.number="settings.tolerancia_tardanza" 
            type="number" 
            min="0"
            placeholder="Ej: 10"
            :class="theme('input').value" 
            class="w-full rounded-xl"
          />
          <p class="text-xs mt-1.5" :class="theme('cardSubtitle').value">
            Define cuántos minutos después de la hora de entrada se considera tardanza.
          </p>
        </div>

        <div class="flex justify-end pt-4">
          <button 
            type="submit" 
            :disabled="loading.save" 
            :class="[theme('buttonPrimary').value, loading.save && 'opacity-50']" 
            class="px-6 py-2 rounded-xl font-semibold"
          >
            <i v-if="loading.save" class="fas fa-spinner animate-spin mr-2"></i>
            Guardar Cambios
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue'
import { useTheme } from '@/composables/useTheme'
import axios from '@/axiosConfig'

// --- INICIALIZACIÓN ---
// ✅ Importamos 'isDark' además de 'theme'
const { theme, isDark } = useTheme()
const settings = ref({
  nombre_institucion: '',
  tolerancia_tardanza: 0
})
const loading = reactive({
  load: true,
  save: false
})
const successMessage = ref('')
const errorMessage = ref('')

// --- NOTIFICACIONES ---
function setNotification(type, message) {
  if (type === 'success') {
    successMessage.value = message;
    errorMessage.value = '';
  } else {
    errorMessage.value = message;
    successMessage.value = '';
  }
  setTimeout(() => {
    successMessage.value = '';
    errorMessage.value = '';
  }, 4000);
}

// --- CARGAR DATOS ---
onMounted(() => {
  loadSettings()
})

async function loadSettings() {
  loading.load = true
  try {
    const response = await axios.get('/configuraciones')
    settings.value.nombre_institucion = response.data.nombre_institucion || ''
    settings.value.tolerancia_tardanza = response.data.tolerancia_tardanza || 0
  } catch (error) {
    setNotification('error', 'No se pudo cargar la configuración existente.')
  } finally {
    loading.load = false
  }
}

// --- GUARDAR DATOS ---
async function saveSettings() {
  loading.save = true
  try {
    await axios.post('/configuraciones', settings.value)
    setNotification('success', 'Configuración guardada exitosamente.')
  } catch (error) {
    setNotification('error', 'Error al guardar la configuración.')
  } finally {
    loading.save = false
  }
}
</script>