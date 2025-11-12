<template>
  <div class="p-4 sm:p-6 space-y-6">
    
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
      <h2 class="text-xl font-bold mb-4" :class="theme('cardTitle').value">
        Información Personal
      </h2>
      <form @submit.prevent="updateProfile" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">Nombre Completo</label>
            <input v-model="userForm.nombre_completo" type="text" :readonly="!isEditing" :class="[theme('input').value, !isEditing && 'opacity-70']" class="w-full rounded-xl"/>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">DNI</label>
            <input v-model="userForm.dni" type="text" :readonly="!isEditing" :class="[theme('input').value, !isEditing && 'opacity-70']" class="w-full rounded-xl"/>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">Correo</label>
            <input v-model="userForm.correo" type="email" :readonly="!isEditing" :class="[theme('input').value, !isEditing && 'opacity-70']" class="w-full rounded-xl"/>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">Teléfono</label>
            <input v-model="userForm.telefono" type="tel" :readonly="!isEditing" :class="[theme('input').value, !isEditing && 'opacity-70']" class="w-full rounded-xl"/>
          </div>
        </div>
        
        <div class="flex justify-end gap-3 pt-4">
          <button v-if="!isEditing" @click.prevent="isEditing = true" type="button" :class="theme('buttonSecondary').value" class="px-4 py-2 rounded-xl font-semibold">
            <i class="fas fa-pencil-alt mr-2"></i>Editar
          </button>
          <template v-else>
            <button @click.prevent="cancelEdit" type="button" :class="theme('buttonSecondary').value" class="px-4 py-2 rounded-xl font-semibold">
              Cancelar
            </button>
            <button type="submit" :disabled="loading.profile" :class="[theme('buttonPrimary').value, loading.profile && 'opacity-50']" class="px-4 py-2 rounded-xl font-semibold">
              <i v-if="loading.profile" class="fas fa-spinner animate-spin mr-2"></i>
              Guardar Cambios
            </button>
          </template>
        </div>
      </form>
    </div>

    <div class="p-6 rounded-2xl border" :class="theme('card').value">
      <h2 class="text-xl font-bold mb-4" :class="theme('cardTitle').value">
        Cambiar Contraseña
      </h2>
      <form @submit.prevent="changePassword" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          
          <div>
            <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">Contraseña Actual</label>
            <div class="relative">
              <input 
                v-model="passwordForm.current_password" 
                :type="showCurrentPassword ? 'text' : 'password'" 
                required 
                :class="theme('input').value" 
                class="w-full rounded-xl pr-10"
              />
              <button 
                type="button" 
                @click="showCurrentPassword = !showCurrentPassword"
                class="absolute inset-y-0 right-0 px-3 flex items-center text-sm"
                :class="theme('cardSubtitle').value"
                title="Mostrar/Ocular contraseña"
              >
                <i class="fas" :class="showCurrentPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
              </button>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">Nueva Contraseña</label>
            <div class="relative">
              <input 
                v-model="passwordForm.new_password" 
                :type="showNewPassword ? 'text' : 'password'" 
                required 
                :class="theme('input').value" 
                class="w-full rounded-xl pr-10"
              />
              <button 
                type="button" 
                @click="showNewPassword = !showNewPassword"
                class="absolute inset-y-0 right-0 px-3 flex items-center text-sm"
                :class="theme('cardSubtitle').value"
                title="Mostrar/Ocular contraseña"
              >
                <i class="fas" :class="showNewPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
              </button>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">Confirmar Nueva Contraseña</label>
            <div class="relative">
              <input 
                v-model="passwordForm.new_password_confirmation" 
                :type="showConfirmPassword ? 'text' : 'password'" 
                required 
                :class="theme('input').value" 
                class="w-full rounded-xl pr-10"
              />
              <button 
                type="button" 
                @click="showConfirmPassword = !showConfirmPassword"
                class="absolute inset-y-0 right-0 px-3 flex items-center text-sm"
                :class="theme('cardSubtitle').value"
                title="Mostrar/Ocular contraseña"
              >
                <i class="fas" :class="showConfirmPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
              </button>
            </div>
          </div>

        </div>
        <div class="flex justify-end pt-4">
          <button type="submit" :disabled="loading.password" :class="[theme('buttonPrimary').value, loading.password && 'opacity-50']" class="px-4 py-2 rounded-xl font-semibold">
             <i v-if="loading.password" class="fas fa-spinner animate-spin mr-2"></i>
            Actualizar Contraseña
          </button>
        </div>
      </form>
    </div>

    <div class="p-6 rounded-2xl border border-red-500/30" :class="theme('card').value">
      <h2 class="text-xl font-bold mb-2 text-red-400">
        Zona de Peligro
      </h2>
      <p class="mb-4 text-sm" :class="theme('cardSubtitle').value">
        Esta acción no se puede deshacer. Esto eliminará permanentemente tu cuenta y todos tus datos asociados.
      </p>
      <div class="flex justify-start">
        <button @click="deleteAccount" :disabled="loading.delete" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-xl font-semibold transition-colors disabled:opacity-50">
          <i v-if="loading.delete" class="fas fa-spinner animate-spin mr-2"></i>
          Eliminar mi cuenta
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue' 
import { useRouter } from 'vue-router'
import { useTheme } from '@/composables/useTheme'
import axios from '@/axiosConfig'

// --- INICIALIZACIÓN ---
// ✅ Importamos 'isDark'
const { theme, isDark } = useTheme()
const router = useRouter()

// --- ESTADO REACTIVO ---
const userForm = ref({})
const originalUser = ref({}) 
const isEditing = ref(false)
const passwordForm = ref({
  current_password: '',
  new_password: '',
  new_password_confirmation: '',
})

const showCurrentPassword = ref(false)
const showNewPassword = ref(false)
const showConfirmPassword = ref(false)

const loading = reactive({
  profile: false,
  password: false,
  delete: false,
})
const successMessage = ref('')
const errorMessage = ref('')

// --- LÓGICA ---
onMounted(() => {
  const userData = JSON.parse(localStorage.getItem('user_data'))
  if (userData && userData.persona) {
    userForm.value = { ...userData.persona }
    originalUser.value = { ...userData.persona }
  }
})

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
  }, 5000);
}

function cancelEdit() {
  userForm.value = { ...originalUser.value }
  isEditing.value = false
}

async function updateProfile() {
  loading.profile = true
  try {
    const response = await axios.put('/perfil', userForm.value) 
    
    const storedUser = JSON.parse(localStorage.getItem('user_data'))
    storedUser.persona = response.data.persona;
    localStorage.setItem('user_data', JSON.stringify(storedUser))
    
    originalUser.value = { ...response.data.persona }
    isEditing.value = false
    setNotification('success', 'Perfil actualizado correctamente.')
  } catch (error) {
    const msg = error.response?.data?.message || 'Error al actualizar el perfil.'
    setNotification('error', msg)
  } finally {
    loading.profile = false
  }
}

async function changePassword() {
  loading.password = true
  if (passwordForm.value.new_password !== passwordForm.value.new_password_confirmation) {
    setNotification('error', 'Las nuevas contraseñas no coinciden.')
    loading.password = false
    return
  }
  try {
    await axios.post('/perfil/cambiar-password', passwordForm.value) 
    passwordForm.value = { current_password: '', new_password: '', new_password_confirmation: '' }
    setNotification('success', 'Contraseña cambiada exitosamente.')
  } catch (error) {
    const msg = error.response?.data?.message || 'Error al cambiar la contraseña.'
    setNotification('error', msg)
  } finally {
    loading.password = false
  }
}

async function deleteAccount() {
  if (confirm('¿Estás seguro de que quieres eliminar tu cuenta? Esta acción es irreversible.')) {
    loading.delete = true
    try {
      await axios.delete('/perfil') 
      localStorage.removeItem('auth_token')
      localStorage.removeItem('user_data')
      router.push('/login')
    } catch (error) {
      setNotification('error', 'No se pudo eliminar la cuenta.')
      loading.delete = false
    }
  }
}
</script>