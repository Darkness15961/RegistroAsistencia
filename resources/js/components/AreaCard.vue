<template>
  <div
    @click="$emit('click')"
    class="flex flex-col bg-white/10 backdrop-blur-xl border border-white/15 rounded-3xl shadow-md p-4 sm:p-5 transition-all transform hover:scale-[1.02] hover:border-white/30 cursor-pointer w-full group"
    :class="{ 'ring-2 ring-cyan-400 border-cyan-400/50': seleccionada }"
  >
    <!-- Header con ícono y badge contador -->
    <div class="flex items-start justify-between mb-3">
      <div
        class="text-white text-3xl flex items-center justify-center w-14 h-14 rounded-2xl shadow-lg transition-transform group-hover:scale-110"
        :class="gradientClass"
      >
        <i :class="`fas fa-${icon}`"></i>
      </div>
      
      <!-- Badge con cantidad de personas -->
      <div class="bg-cyan-500/20 border border-cyan-400/30 px-3 py-1 rounded-full">
        <span class="text-cyan-200 font-bold text-sm">{{ cantidadPersonas }}</span>
      </div>
    </div>

    <!-- Contenido -->
    <div class="flex flex-col flex-1">
      <h3 class="text-white text-base sm:text-lg font-bold mb-2 group-hover:text-cyan-300 transition-colors">
        {{ nombre }}
      </h3>
      <p class="text-gray-300 text-xs sm:text-sm leading-relaxed line-clamp-2">
        {{ descripcion }}
      </p>
      
      <!-- Indicador de selección -->
      <div v-if="seleccionada" class="mt-3 flex items-center gap-2 text-cyan-400 text-xs font-medium">
        <i class="fas fa-check-circle"></i>
        <span>Área seleccionada</span>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  nombre: {
    type: String,
    required: true
  },
  descripcion: {
    type: String,
    default: ''
  },
  icon: {
    type: String,
    default: 'users'
  },
  gradientClass: {
    type: String,
    default: 'bg-gradient-to-br from-cyan-400 to-blue-500'
  },
  cantidadPersonas: {
    type: Number,
    default: 0
  },
  seleccionada: {
    type: Boolean,
    default: false
  }
})

defineEmits(['click'])
</script>