<template>
  <div
    class="absolute top-full right-0 mt-3 w-64 rounded-3xl border p-4 z-50"
    :class="isDark 
      ? 'bg-gradient-to-br from-gray-800 to-gray-900 border-gray-700' 
      : 'bg-white border-gray-200'"
  >
    <nav>
      <ul>
        <li>
          <router-link 
            to="/perfil"
            @click="emit('close')" 
            class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all"
            :class="menuItemClass"
          >
            <i class="fas fa-user w-4 text-center" :class="theme('cardSubtitle').value"></i>
            <span class="text-sm font-medium">Mi Perfil</span>
          </router-link>
        </li>
        <li v-if="isAdmin">
          <router-link 
            to="/configuracion"
            @click="emit('close')" 
            class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all"
            :class="menuItemClass"
          >
            <i class="fas fa-cog w-4 text-center" :class="theme('cardSubtitle').value"></i>
            <span class="text-sm font-medium">Configuración</span>
          </router-link>
        </li>
        <li class="mt-2 pt-2 border-t" :class="isDark ? 'border-white/10' : 'border-gray-200'">
          <a 
            href="#"
            @click.prevent="handleLogout"
            class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all"
            :class="menuItemDangerClass"
          >
            <i class="fas fa-sign-out-alt w-4 text-center"></i>
            <span class="text-sm font-medium">Cerrar Sesión</span>
          </a>
        </li>
      </ul>
    </nav>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useTheme } from '@/composables/useTheme'
import { useRouter } from 'vue-router'
import { useAuth } from '@/composables/useAuth'
import axios from '@/axiosConfig'

// Define el emit 'close'
const emit = defineEmits(['close'])

const { theme, isDark } = useTheme()
const { isAdmin } = useAuth()
const router = useRouter()

const handleLogout = async () => {
  emit('close')
  try {
    await axios.post('/logout')
  } catch (error) {
    console.warn('No se pudo cerrar sesión en el backend:', error.response?.data || error.message)
  } finally {
    localStorage.removeItem('auth_token')
    localStorage.removeItem('user_data')
    router.push('/login') 
  }
}

const menuItemClass = computed(() => {
  const textColor = theme('cardTitle').value
  const hoverBg = isDark.value ? 'hover:bg-white/10' : 'hover:bg-gray-100'
  return [textColor, hoverBg]
})

const menuItemDangerClass = computed(() => {
  return isDark.value
    ? 'text-red-400 hover:bg-red-500/20'
    : 'text-red-600 hover:bg-red-50'
})
</script>