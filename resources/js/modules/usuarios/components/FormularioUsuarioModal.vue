<template>
  <div 
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4"
    @click.self="$emit('close')"
  >
    <div 
      class="w-full max-w-xl rounded-3xl border shadow-2xl p-6" 
      :class="theme('card').value"
    >
      <h3 class="text-xl font-bold mb-6" :class="theme('cardTitle').value">
        {{ isEditing ? 'Editar Usuario' : 'Nuevo Usuario' }}
      </h3>

      <form @submit.prevent="submit" class="space-y-4">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">
              Email de Acceso
            </label>
            <input 
              v-model="form.email" 
              type="email"
              placeholder="usuario@dominio.com" 
              required
              class="w-full rounded-xl"
              :class="theme('input').value"
            />
          </div>
          
          <div>
            <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">
              Persona (ID Opcional)
            </label>
            <input 
              v-model="form.id_persona" 
              type="number"
              placeholder="Vincular a ID de persona" 
              class="w-full rounded-xl"
              :class="theme('input').value"
            />
          </div>

          <div>
            <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">
              Rol del Usuario
            </label>
            <select 
              v-model="form.rol" 
              class="w-full rounded-xl appearance-none pr-8" 
              :class="[theme('input').value, isDark ? 'bg-gray-900/50' : 'bg-white']"
            >
              <option value="administrador" :class="isDark ? 'bg-gray-800' : 'bg-white'">Administrador</option>
              <option value="empleado" :class="isDark ? 'bg-gray-800' : 'bg-white'">Empleado</option>
              <option value="docente" :class="isDark ? 'bg-gray-800' : 'bg-white'">Docente</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">
              Estado
            </label>
            <select 
              v-model="form.estado" 
              class="w-full rounded-xl appearance-none pr-8"
              :class="[theme('input').value, isDark ? 'bg-gray-900/50' : 'bg-white']"
            >
              <option value="activo" :class="isDark ? 'bg-gray-800' : 'bg-white'">Activo</option>
              <option value="inactivo" :class="isDark ? 'bg-gray-800' : 'bg-white'">Inactivo</option>
            </select>
          </div>
        </div>

        <div v-if="!isEditing" class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">
              Contraseña
            </label>
            <div class="relative">
              <input 
                v-model="form.password" 
                :type="showPassword ? 'text' : 'password'" 
                placeholder="••••••••" 
                required
                class="w-full rounded-xl pr-10"
                :class="theme('input').value"
              />
              <button 
                type="button" 
                @click="showPassword = !showPassword"
                class="absolute inset-y-0 right-0 px-3 flex items-center text-sm"
                :class="theme('cardSubtitle').value"
              >
                <i class="fas" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
              </button>
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">
              Confirmar Contraseña
            </label>
            <div class="relative">
              <input 
                v-model="form.password_confirmation" 
                :type="showConfirmPassword ? 'text' : 'password'" 
                placeholder="••••••••" 
                required
                class="w-full rounded-xl pr-10"
                :class="theme('input').value"
              />
              <button 
                type="button" 
                @click="showConfirmPassword = !showConfirmPassword"
                class="absolute inset-y-0 right-0 px-3 flex items-center text-sm"
                :class="theme('cardSubtitle').value"
              >
                <i class="fas" :class="showConfirmPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
              </button>
            </div>
          </div>
        </div>
        <p v-if="!isEditing" class="text-xs" :class="theme('cardSubtitle').value">
          Si no especificas un ID de Persona, se creará una nueva persona con datos básicos.
        </p>
        <p v-if="isEditing" class="text-xs" :class="theme('cardSubtitle').value">
          La contraseña no se puede modificar desde este modal.
        </p>

        <div class="flex gap-3 justify-end pt-4">
          <button 
            type="button" 
            @click="$emit('close')" 
            class="px-5 py-2 rounded-xl font-semibold"
            :class="theme('buttonSecondary').value"
          >
            Cancelar
          </button>
          <button 
            type="submit" 
            :disabled="loading"
            class="px-5 py-2 rounded-xl font-semibold flex items-center gap-2"
            :class="[theme('buttonPrimary').value, loading && 'opacity-50']"
          >
            <i v-if="loading" class="fas fa-spinner animate-spin"></i>
            {{ isEditing ? 'Guardar Cambios' : 'Crear Usuario' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { reactive, computed, watch, ref } from 'vue'
import { useTheme } from '@/composables/useTheme'
import api from '@/axiosConfig'

const props = defineProps({
  usuario: { type: [Object, null], default: null }
})
const emit = defineEmits(['close', 'saved'])

const { theme, isDark } = useTheme()
const loading = ref(false)
const showPassword = ref(false) // Para el ojo
const showConfirmPassword = ref(false) // Para el ojo

// ✅ ID de persona ya no es requerido, es opcional
const getInitialForm = () => ({
  id_persona: props.usuario?.id_persona || null,
  email: props.usuario?.email || '',
  rol: props.usuario?.rol || 'empleado',
  estado: props.usuario?.estado || 'activo',
  password: '',
  password_confirmation: ''
})

const form = reactive(getInitialForm())
const isEditing = computed(() => !!props.usuario)

watch(() => props.usuario, () => {
  Object.assign(form, getInitialForm())
}, { immediate: true })

const submit = async () => {
  loading.value = true
  try {
    // Limpiamos el payload
    const payload = {
      email: form.email,
      rol: form.rol,
      estado: form.estado
    }
    // Añadimos id_persona solo si tiene valor
    if (form.id_persona) {
      payload.id_persona = form.id_persona
    }
    
    if (isEditing.value) {
      await api.put(`/usuarios/${props.usuario.id_usuario}`, payload)
    } else {
      payload.password = form.password
      payload.password_confirmation = form.password_confirmation
      // Si no hay id_persona, tu API (según tu plantilla) puede necesitar un nombre
      // Asumimos que la API lo maneja o crea uno por defecto.
      // Si no, tendrías que añadir un campo "nombre" al crear.
      await api.post('/usuarios', payload)
    }
    emit('saved')
    emit('close')
  } catch (err) {
    console.error('Error en modal usuario:', err)
    alert(err.response?.data?.message || 'Error al guardar.')
  } finally {
    loading.value = false
  }
}
</script>