<template>
  <div class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4"
       @click.self="$emit('cerrar')">
    <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl border p-6" :class="theme('card').value">
      <h2 class="text-xl font-bold mb-6" :class="theme('cardTitle').value">
        <i class="fas fa-user-plus mr-2"></i>
        {{ empleado ? 'Editar Empleado' : 'Registrar Empleado' }}
      </h2>

      <form @submit.prevent="guardar">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">
              Nombre Completo
            </label>
            <input v-model="form.nombre_completo" placeholder="Nombre completo" class="w-full rounded-xl" :class="theme('input').value" required />
          </div>
          <div>
            <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">
              DNI
            </label>
            <input v-model="form.dni" placeholder="DNI" class="w-full rounded-xl" :class="theme('input').value" required />
          </div>
          <div>
            <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">
              Correo Electrónico
            </label>
            <input v-model="form.correo" placeholder="Correo electrónico" type="email" class="w-full rounded-xl" :class="theme('input').value" required />
          </div>
          <div>
            <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">
              Teléfono
            </label>
            <input v-model="form.telefono" placeholder="Teléfono" class="w-full rounded-xl" :class="theme('input').value" />
          </div>
          <div>
            <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">
              Cargo
            </label>
            <input v-model="form.cargo_grado" placeholder="Cargo o puesto" class="w-full rounded-xl" :class="theme('input').value" />
          </div>
          <div>
            <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">
              Área
            </label>
            <select v-model="form.id_area" class="w-full rounded-xl appearance-none pr-8" :class="[theme('input').value, isDark ? 'bg-gray-900/50' : 'bg-white']" required>
              <option value="" disabled>Seleccionar área</option>
              <option v-for="area in areas" :key="area.id_area" :value="area.id_area" :class="isDark ? 'bg-gray-800' : 'bg-white'">
                {{ area.nombre_area }}
              </option>
            </select>
          </div>
        </div>

        <div class="flex justify-end gap-3 pt-6">
          <button type="button" @click="$emit('cerrar')" class="px-5 py-2.5 rounded-xl font-semibold" :class="theme('buttonSecondary').value">
            Cancelar
          </button>
          <button type="submit" :disabled="loading" class="px-5 py-2.5 rounded-xl font-semibold shadow-lg" :class="[theme('buttonPrimary').value, loading && 'opacity-50']">
            <i v-if="loading" class="fas fa-spinner animate-spin mr-2"></i>
            {{ empleado ? 'Actualizar' : 'Guardar' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'
import { useTheme } from '@/composables/useTheme'
import { useEmpleados } from '../composables/useEmpleados'
import api from '@/axiosConfig'

const props = defineProps({ 
  empleado: Object,
  // Recibimos las áreas filtradas (solo empleados) como prop
  areas: {
    type: Array,
    required: true
  }
})
const emit = defineEmits(['cerrar', 'actualizado'])

const { theme, isDark } = useTheme()
const { crearEmpleado, actualizarEmpleado } = useEmpleados()
const loading = ref(false)

const form = ref({
  nombre_completo: '',
  dni: '',
  correo: '',
  telefono: '',
  cargo_grado: '',
  id_area: '',
})

// Carga los datos del empleado cuando se abre para editar
watch(() => props.empleado, (nuevo) => {
  if (nuevo) {
    form.value = { 
      nombre_completo: nuevo.nombre_completo || '',
      dni: nuevo.dni || '',
      correo: nuevo.correo || '',
      telefono: nuevo.telefono || '',
      cargo_grado: nuevo.cargo_grado || '',
      id_area: nuevo.id_area || '',
    }
  } else {
    // Resetea el formulario si es para crear uno nuevo
    form.value = { nombre_completo: '', dni: '', correo: '', telefono: '', cargo_grado: '', id_area: '' }
  }
}, { immediate: true }) // immediate:true asegura que se llene al abrir

const guardar = async () => {
  loading.value = true
  try {
    if (props.empleado) {
      await actualizarEmpleado(props.empleado.id_persona, form.value)
    } else {
      await crearEmpleado(form.value)
    }
    emit('actualizado')
    emit('cerrar')
  } catch (err) {
    alert(err.error || 'Error al guardar empleado')
  } finally {
    loading.value = false
  }
}
</script>