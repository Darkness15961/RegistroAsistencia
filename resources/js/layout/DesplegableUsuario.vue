<template>
  <div class="relative">
    <button
      @click="toggleDropdown"
      class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition"
    >
      <img
        src="https://ui-avatars.com/api/?name=Admin&background=0D8ABC&color=fff"
        alt="Avatar"
        class="w-8 h-8 rounded-full"
      />
      <span class="text-sm text-gray-700 dark:text-gray-300 hidden sm:inline">
        {{ userName }}
      </span>
      <i class="fas fa-chevron-down text-xs text-gray-500"></i>
    </button>

    <transition name="fade">
      <div
        v-if="dropdownOpen"
        class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 border border-gray-200 
               dark:border-gray-700 rounded-lg shadow-lg z-50"
      >
        <ul class="py-1 text-sm text-gray-700 dark:text-gray-300">
          <li>
            <router-link
              to="/perfil"
              class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700"
              @click="closeDropdown"
            >
              Mi Perfil
            </router-link>
          </li>
          <li>
            <button
              @click="logout"
              class="w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700"
            >
              Cerrar sesión
            </button>
          </li>
        </ul>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const dropdownOpen = ref(false)
const userName = 'Administrador'

const toggleDropdown = () => (dropdownOpen.value = !dropdownOpen.value)
const closeDropdown = () => (dropdownOpen.value = false)

const logout = () => {
  localStorage.removeItem('auth_token')
  router.push('/login')
}

// Cerrar el menú si se hace clic fuera
const handleClickOutside = (event) => {
  if (!event.target.closest('.relative')) closeDropdown()
}

onMounted(() => document.addEventListener('click', handleClickOutside))
onUnmounted(() => document.removeEventListener('click', handleClickOutside))
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
