<template>
  <div class="p-4 sm:p-6">
    
    <div class="flex flex-col md:flex-row justify-between items-start mb-6 gap-4">
      <h1 class="text-3xl font-bold" :class="theme('cardTitle').value">
        Gestión de Usuarios
      </h1>
      
      <div class="flex flex-col md:flex-row gap-3 w-full md:w-auto">
        <button 
          @click="toggleVista" 
          class="px-4 py-2.5 rounded-xl font-semibold w-full md:w-auto transition-colors"
          :class="theme('buttonSecondary').value"
          title="Alternar vista (Tabla/Tarjeta)"
        >
          <i class="fas" :class="vistaActual === 'card' ? 'fa-list-ul' : 'fa-th'"></i>
          <span class="ml-2 hidden sm:inline">{{ vistaActual === 'card' ? 'Ver Tabla' : 'Ver Tarjetas' }}</span>
        </button>

        <button 
          @click="abrirModal()" 
          class="px-5 py-2.5 rounded-xl font-semibold w-full md:w-auto"
          :class="theme('buttonPrimary').value"
        >
          <i class="fas fa-plus mr-2"></i>
          Nuevo Usuario
        </button>
      </div>
    </div>

    <div class="mb-6">
      <div class="relative">
        <input 
          v-model="searchTerm"
          type="text" 
          placeholder="Buscar por nombre, email o rol..."
          class="w-full md:w-1/2 rounded-xl pl-10 pr-4 py-3 border transition-colors"
          :class="theme('input').value"
        />
        <span class="absolute left-3 top-1/2 -translate-y-1/2" :class="theme('cardSubtitle').value">
          <i class="fas fa-search"></i>
        </span>
      </div>
    </div>

    <div v-if="loading" class="text-center py-12" :class="theme('cardSubtitle').value">
      <i class="fas fa-spinner fa-spin text-3xl"></i>
      <p class="mt-2">Cargando usuarios...</p>
    </div>
    <div v-else-if="error" class="text-red-400 text-center py-12">
      {{ error }}
    </div>

    <div v-else>
      <p v-if="!usuarios.length" class="text-center py-12 text-lg" :class="theme('cardSubtitle').value">
        No hay usuarios registrados.
      </p>
      
      <TablaUsuario
        v-else-if="vistaActual === 'table'"
        :usuarios="filteredUsuarios"
        @edit="abrirModal"
        @delete="eliminarUsuario"
      />

      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <UserCard
          v-for="usuario in filteredUsuarios"
          :key="usuario.id_usuario"
          :usuario="usuario"
          @edit="abrirModal"
          @delete="eliminarUsuario"
        />
      </div>
      
      <p v-if="!filteredUsuarios.length && usuarios.length" class="text-center py-12 text-lg" :class="theme('cardSubtitle').value">
        No se encontraron usuarios que coincidan con la búsqueda.
      </p>
    </div>

    <FormularioUsuarioModal
      v-if="showModal"
      :key="modalKey"
      :usuario="usuarioSeleccionado"
      @close="onModalClose"
      @saved="onModalClose"
    />
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useTheme } from '@/composables/useTheme'
import api from '@/axiosConfig'
import FormularioUsuarioModal from '../components/FormularioUsuarioModal.vue'
import UserCard from '../components/UserCard.vue'
import TablaUsuario from '../components/TablaUsuario.vue' // ✅ Importar el nuevo componente de tabla

const { theme } = useTheme()

const usuarios = ref([])
const loading = ref(true)
const showModal = ref(false)
const usuarioSeleccionado = ref(null)
const error = ref(null)
const modalKey = ref(0)
const searchTerm = ref('')
const vistaActual = ref('card') // ✅ Estado inicial: 'card'

const fetchUsuarios = async (page = 1) => {
  loading.value = true
  try {
    const res = await api.get('/usuarios', { params: { page } })
    usuarios.value = Array.isArray(res.data.data) ? res.data.data : (res.data || [])
  } catch (err) {
    console.error('Error al cargar usuarios:', err)
    error.value = 'No se pudo obtener la lista de usuarios.'
  } finally {
    loading.value = false
  }
}

const filteredUsuarios = computed(() => {
  if (!searchTerm.value) {
    return usuarios.value
  }
  const search = searchTerm.value.toLowerCase()
  return usuarios.value.filter(u => 
    (u.persona?.nombre_completo && u.persona.nombre_completo.toLowerCase().includes(search)) ||
    (u.email.toLowerCase().includes(search)) ||
    (u.rol.toLowerCase().includes(search))
  )
})

const abrirModal = (usuario = null) => {
  usuarioSeleccionado.value = usuario ? JSON.parse(JSON.stringify(usuario)) : null
  modalKey.value++
  showModal.value = true
}

const onModalClose = async (payload = null) => {
  showModal.value = false
  usuarioSeleccionado.value = null
  await fetchUsuarios() 
}

const eliminarUsuario = async (id_usuario) => {
  if (!confirm('¿Deseas eliminar este usuario?')) return
  try {
    await api.delete(`/usuarios/${id_usuario}`)
    await fetchUsuarios()
  } catch (err) {
    console.error(err)
    alert('Error eliminando el usuario: ' + (err.response?.data?.message || err.message))
  }
}

// ✅ Función para alternar la vista
const toggleVista = () => {
  vistaActual.value = vistaActual.value === 'card' ? 'table' : 'card'
}

onMounted(() => fetchUsuarios())
</script>