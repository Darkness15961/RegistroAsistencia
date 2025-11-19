<template>
  <div class="w-full">
    <div class="mb-6">
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
          <h1 class="text-2xl sm:text-3xl font-bold mb-2" :class="theme('cardTitle').value">
            <i class="fas fa-users mr-2"></i>
            {{ nombreGrupo }}
          </h1>
          <p :class="theme('cardSubtitle').value" class="text-sm">
            Gestiona el personal de este grupo
          </p>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-4 w-full sm:w-auto">
          <button 
            @click="$emit('nuevoPersonal')"
            class="flex items-center justify-center gap-2 px-6 py-3 rounded-xl font-semibold shadow-lg"
            :class="theme('buttonPrimary').value"
          >
            <i class="fas fa-plus"></i>
            Nuevo Personal
          </button>
          
          <button 
            @click="$emit('volver')"
            class="flex items-center justify-center gap-2 px-6 py-3 rounded-xl font-semibold shadow-lg"
            :class="theme('buttonSecondary').value" 
          >
            <i class="fas fa-arrow-left"></i>
            Volver a Grupos
          </button>
        </div>
      </div>

      <div class="relative w-full sm:max-w-md">
        <input
          v-model="busqueda"
          type="text"
          placeholder="Buscar por nombre, DNI o cargo..."
          class="w-full rounded-xl pl-10 pr-4 py-3 outline-none border transition-colors"
          :class="theme('input').value"
        />
        <i 
          class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2"
          :class="theme('cardSubtitle').value"
        ></i>
      </div>
    </div>

    <div 
      class="hidden md:block backdrop-blur-xl border rounded-3xl overflow-hidden shadow-lg"
      :class="theme('card').value"
    >
      <div class="overflow-x-auto">
        <table class="w-full text-left">
          <thead :class="theme('tableHeader').value">
            <tr class="border-b" :class="isDark ? 'border-white/20' : 'border-gray-200'">
              <th class="p-4 font-bold text-sm uppercase min-w-[250px]" :class="theme('cardSubtitle').value">Nombre</th>
              <th class="p-4 font-bold text-sm uppercase" :class="theme('cardSubtitle').value">Cargo</th>
              <th class="p-4 font-bold text-sm uppercase" :class="theme('cardSubtitle').value">Contacto</th>
              <th class="p-4 font-bold text-sm uppercase text-center" :class="theme('cardSubtitle').value">Estado</th>
              <th class="text-center p-4 font-bold text-sm uppercase" :class="theme('cardSubtitle').value">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="personalFiltrado.length === 0">
              <td colspan="5" class="text-center p-8" :class="theme('cardSubtitle').value">
                <i class="fas fa-user-slash text-5xl mb-3 opacity-50"></i>
                <p>No se encontró personal en este grupo</p>
              </td>
            </tr>
            
            <tr 
              v-for="(persona, index) in personalFiltrado" 
              :key="persona.id_persona"
              class="border-b transition-all"
              :class="[
                theme('tableRow').value,
                isDark ? 'border-white/5' : 'border-gray-100',
                index % 2 === 0 ? (isDark ? 'bg-white/[0.03]' : 'bg-gray-50/50') : ''
              ]"
            >
              <td class="p-4">
                <div class="flex items-center gap-3">
                  <div 
                    class="w-10 h-10 rounded-xl object-cover shadow-md flex items-center justify-center text-white font-bold text-sm flex-shrink-0"
                    :class="getAvatarGradient(persona.id_persona)"
                  >
                    {{ getInitials(persona.nombre_completo) }}
                  </div>
                  
                  <div>
                    <span class="font-semibold block" :class="theme('cardTitle').value">
                      {{ persona.nombre_completo }}
                    </span>
                    <span class="text-xs px-2 py-0.5 rounded-full border inline-block mt-0.5" 
                          :class="isDark ? 'bg-white/10 border-white/10 text-gray-300' : 'bg-gray-100 border-gray-200 text-gray-600'">
                      DNI: {{ persona.dni }}
                    </span>
                  </div>
                </div>
              </td>
              
              <td class="p-4" :class="theme('cardSubtitle').value">
                {{ persona.cargo_grado || '-' }}
              </td>
              
              <td class="p-4 text-sm" :class="theme('cardSubtitle').value">
                <div class="flex items-center gap-2 mb-1">
                  <i class="fas fa-envelope text-xs opacity-70"></i> {{ persona.correo }}
                </div>
                <div class="flex items-center gap-2 font-mono text-xs opacity-80">
                  <i class="fas fa-phone text-xs opacity-70"></i> {{ persona.telefono || '-' }}
                </div>
              </td>

              <td class="p-4 text-center">
                <span 
                  class="px-3 py-1 rounded-full text-xs font-bold inline-flex items-center gap-1.5 border"
                  :class="persona.estado === 'activo' 
                    ? (isDark ? 'bg-green-500/20 border-green-500/30 text-green-300' : 'bg-green-50 border-green-200 text-green-700') 
                    : (isDark ? 'bg-red-500/20 border-red-500/30 text-red-200' : 'bg-red-50 border-red-200 text-red-700')"
                >
                  <i class="fas fa-circle text-[0.5rem]" :class="persona.estado === 'activo' ? 'text-green-500' : 'text-red-500'"></i>
                  {{ persona.estado }}
                </span>
              </td>
              
              <td class="p-4">
                <div class="flex justify-center gap-2">
                  <button 
                    @click="$emit('editar', persona)"
                    class="p-2 w-9 h-9 rounded-xl transition-all hover:scale-110 group flex items-center justify-center"
                    :class="isDark ? 'bg-blue-500/20 hover:bg-blue-500/30' : 'bg-blue-50 hover:bg-blue-100'"
                    title="Editar"
                  >
                    <i class="fas fa-edit text-sm" :class="isDark ? 'text-blue-300' : 'text-blue-600'"></i>
                  </button>
                  <button 
                    @click="$emit('eliminar', persona.id_persona)"
                    class="p-2 w-9 h-9 rounded-xl transition-all hover:scale-110 group flex items-center justify-center"
                    :class="isDark ? 'bg-red-500/20 hover:bg-red-500/30' : 'bg-red-50 hover:bg-red-100'"
                    title="Eliminar"
                  >
                    <i class="fas fa-trash text-sm" :class="isDark ? 'text-red-300' : 'text-red-600'"></i>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="grid gap-4 md:hidden">
      <div
        v-if="personalFiltrado.length === 0"
        class="text-center py-12 rounded-xl border"
        :class="[theme('card').value, theme('cardSubtitle').value]"
      >
        <i class="fas fa-user-slash text-5xl mb-4 opacity-50"></i>
        <p class="text-lg">No se encontró personal</p>
      </div>

      <div
        v-for="persona in personalFiltrado"
        :key="persona.id_persona"
        class="backdrop-blur-xl border rounded-3xl shadow-lg p-5"
        :class="theme('card').value"
      >
        <div class="flex items-start justify-between gap-3 mb-4">
          <div class="flex items-center gap-3">
            <div 
              class="w-12 h-12 rounded-2xl object-cover shadow-lg text-white flex items-center justify-center font-bold"
              :class="getAvatarGradient(persona.id_persona)"
            >
              {{ getInitials(persona.nombre_completo) }}
            </div>
            
            <div>
              <h2 class="text-lg font-semibold" :class="theme('cardTitle').value">
                {{ persona.nombre_completo }}
              </h2>
              <span class="text-xs px-2 py-0.5 rounded-full border" :class="isDark ? 'bg-white/10 border-white/10 text-gray-300' : 'bg-gray-100 border-gray-200 text-gray-600'">
                DNI: {{ persona.dni }}
              </span>
            </div>
          </div>

          <div class="flex gap-2">
            <button
              @click="$emit('editar', persona)"
              class="p-2 w-10 h-10 rounded-xl transition-all group flex items-center justify-center"
              :class="isDark ? 'bg-blue-500/20' : 'bg-blue-50'"
            >
              <i class="fas fa-edit" :class="isDark ? 'text-blue-300' : 'text-blue-600'"></i>
            </button>
            <button
              @click="$emit('eliminar', persona.id_persona)"
              class="p-2 w-10 h-10 rounded-xl transition-all group flex items-center justify-center"
              :class="isDark ? 'bg-red-500/20' : 'bg-red-50'"
            >
              <i class="fas fa-trash" :class="isDark ? 'text-red-300' : 'text-red-600'"></i>
            </button>
          </div>
        </div>
        
        <div class="flex flex-col gap-3 pt-3 border-t" :class="isDark ? 'border-white/10' : 'border-gray-200'">
            <div class="flex items-center gap-2">
              <i class="fas fa-briefcase text-sm w-5 text-center opacity-70" :class="theme('cardSubtitle').value"></i>
              <span class="font-medium text-sm" :class="theme('cardTitle').value">{{ persona.cargo_grado || 'Sin cargo' }}</span>
            </div>
            <div class="flex items-center gap-2">
              <i class="fas fa-envelope text-sm w-5 text-center opacity-70" :class="theme('cardSubtitle').value"></i>
              <span class="font-medium text-sm" :class="theme('cardTitle').value">{{ persona.correo }}</span>
            </div>
             <div 
              class="px-3 py-1 rounded-full text-xs font-bold inline-flex items-center gap-2 border w-fit"
              :class="persona.estado === 'activo' 
                ? (isDark ? 'bg-green-500/20 border-green-500/30 text-green-200' : 'bg-green-50 border-green-200 text-green-700') 
                : (isDark ? 'bg-red-500/20 border-red-500/30 text-red-200' : 'bg-red-50 border-red-200 text-red-700')"
            >
              <i class="fas fa-circle text-[0.5rem]" :class="persona.estado === 'activo' ? 'text-green-500' : 'text-red-500'"></i>
              {{ persona.estado }}
            </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useTheme } from '@/composables/useTheme'

const { theme, isDark } = useTheme()

const props = defineProps({
  nombreGrupo: {
    type: String,
    default: 'Personal'
  },
  personal: {
    type: Array,
    default: () => []
  }
})

defineEmits(['nuevoPersonal', 'editar', 'eliminar', 'volver'])

const busqueda = ref('')

const personalFiltrado = computed(() => {
  if (!busqueda.value) return props.personal
  
  const lowerBusqueda = busqueda.value.toLowerCase()
  return props.personal.filter(p => 
    (p.nombre_completo && p.nombre_completo.toLowerCase().includes(lowerBusqueda)) ||
    (p.dni && p.dni.toLowerCase().includes(lowerBusqueda)) ||
    (p.cargo_grado && p.cargo_grado.toLowerCase().includes(lowerBusqueda))
  )
})

// --- Lógica Visual (Igual que en Card) ---

const gradients = [
  'bg-gradient-to-br from-blue-400 to-blue-600',
  'bg-gradient-to-br from-purple-400 to-purple-600',
  'bg-gradient-to-br from-green-400 to-green-600',
  'bg-gradient-to-br from-orange-400 to-orange-600',
  'bg-gradient-to-br from-red-400 to-red-600',
  'bg-gradient-to-br from-pink-400 to-pink-600',
  'bg-gradient-to-br from-cyan-400 to-cyan-600'
]

const getAvatarGradient = (id) => {
  const safeId = id || 0
  return gradients[safeId % gradients.length]
}

const getInitials = (name) => {
  if (name) {
    const matches = name.match(/\b\w/g) || []
    return ((matches.shift() || '') + (matches.shift() || '')).toUpperCase()
  }
  return '?'
}
</script>