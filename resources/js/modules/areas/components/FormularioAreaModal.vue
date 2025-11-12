<template>
  <div 
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4"
    @click.self="$emit('cerrar')"
  >
    <div 
      class="w-full max-w-md rounded-3xl border shadow-2xl p-6" 
      :class="theme('card').value"
    >
      <h3 class="text-xl font-bold mb-6 flex items-center" :class="theme('cardTitle').value">
        <i class="fas fa-building mr-3"></i>
        {{ area ? 'Editar Área' : 'Nueva Área' }}
      </h3>

      <form @submit.prevent="guardarArea" class="space-y-4">
        <div>
          <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">
            Nombre del Área
          </label>
          <input 
            v-model="form.nombre_area"
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
          <input
            v-model="form.descripcion"
            type="text"
            placeholder="Ej: Área encargada de los docentes de primaria"
            class="w-full rounded-xl"
            :class="theme('input').value"
          />
        </div>
        
        <div class="flex justify-end gap-3 pt-4">
          <button 
            @click="$emit('cerrar')"
            type="button"
            class="px-5 py-2.5 rounded-xl font-semibold"
            :class="theme('buttonSecondary').value"
          >
            Cancelar
          </button>
          <button 
            type="submit"
            :disabled="loading"
            class="flex items-center gap-2 px-5 py-2.5 rounded-xl font-semibold shadow-lg"
            :class="theme('buttonPrimary').value"
          >
            <i v-if="loading" class="fas fa-spinner animate-spin"></i>
            {{ area ? 'Actualizar' : 'Guardar' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import { useTheme } from '@/composables/useTheme'
import api from '@/axiosConfig'

const props = defineProps({ area: Object })
const emit = defineEmits(['cerrar'])

const { theme } = useTheme()
const loading = ref(false)

const form = ref({
  nombre_area: '',
  descripcion: ''
})

// Lógica original para cargar datos al editar
watch(
  () => props.area,
  (val) => {
    if (val) {
      form.value = { nombre_area: val.nombre_area, descripcion: val.descripcion || '' }
    } else {
      form.value = { nombre_area: '', descripcion: '' }
    }
  },
  { immediate: true }
)

// Lógica original para guardar
const guardarArea = async () => {
  loading.value = true
  try {
    if (props.area) {
      await api.put(`/areas/${props.area.id_area}`, form.value)
    } else {
      await api.post('/areas', form.value)
    }
    emit('cerrar')
  } catch (error) {
    console.error('Error guardando área:', error)
    alert(error.response?.data?.message || 'Error al guardar.')
  } finally {
    loading.value = false
  }
}
</script>