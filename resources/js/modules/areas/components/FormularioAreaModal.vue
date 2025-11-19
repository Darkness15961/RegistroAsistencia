<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4"
       @click.self="$emit('cerrar')">
    <div class="rounded-3xl w-full max-w-lg shadow-2xl border p-6" :class="theme('card').value">
      
      <h2 class="text-xl font-bold mb-6" :class="theme('cardTitle').value">
        <i class="fas fa-building mr-2"></i>
        {{ area ? 'Editar Área' : 'Nueva Área' }}
      </h2>

      <form @submit.prevent="guardarArea">
        <div class="space-y-4">
          
          <div>
            <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">
              Nombre del Área
            </label>
            <input 
              v-model="form.nombre_area" 
              placeholder="Ej: Docentes de Primaria"
              class="w-full rounded-xl border px-3 py-2 outline-none transition-colors"
              :class="isDark ? 'bg-gray-800 border-gray-600 text-white placeholder-gray-500 focus:border-blue-500' : 'bg-white border-gray-200 text-gray-900 focus:border-blue-500'"
              required
            />
          </div>

          <div>
            <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">
              Descripción (Opcional)
            </label>
            <textarea 
              v-model="form.descripcion" 
              placeholder="Breve descripción de las funciones..."
              rows="3"
              class="w-full rounded-xl border px-3 py-2 outline-none transition-colors"
              :class="isDark ? 'bg-gray-800 border-gray-600 text-white placeholder-gray-500 focus:border-blue-500' : 'bg-white border-gray-200 text-gray-900 focus:border-blue-500'"
            ></textarea>
          </div>

        </div>

        <div class="flex justify-end gap-3 pt-6">
          <button type="button" @click="$emit('cerrar')" class="px-5 py-2.5 rounded-xl font-semibold" :class="theme('buttonSecondary').value">
            Cancelar
          </button>
          <button type="submit" :disabled="loading" class="px-5 py-2.5 rounded-xl font-semibold shadow-lg" :class="[theme('buttonPrimary').value, loading && 'opacity-50']">
            <i v-if="loading" class="fas fa-spinner animate-spin mr-2"></i>
            {{ area ? 'Actualizar' : 'Guardar' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, watch } from 'vue'
import { useTheme } from '@/composables/useTheme'
import api from '@/axiosConfig'

const props = defineProps({
  area: { type: [Object, null], default: null }
})
const emit = defineEmits(['cerrar', 'actualizado'])
const { theme, isDark } = useTheme()
const loading = ref(false)

const form = reactive({
  nombre_area: '',
  descripcion: ''
})

watch(() => props.area, (nuevo) => {
  if (nuevo) {
    form.nombre_area = nuevo.nombre_area || ''
    form.descripcion = nuevo.descripcion || ''
  } else {
    form.nombre_area = ''
    form.descripcion = ''
  }
}, { immediate: true })

const guardarArea = async () => {
  loading.value = true
  try {
    if (props.area) {
      // Editar
      await api.put(`/areas/${props.area.id_area}`, form)
    } else {
      // Nuevo
      await api.post('/areas', form)
    }
    emit('actualizado')
    emit('cerrar')
  } catch (err) {
    console.error('Error guardando área:', err)
    const msg = err.response?.data?.message || 'Error al guardar el área.'
    alert(msg)
  } finally {
    loading.value = false
  }
}
</script>