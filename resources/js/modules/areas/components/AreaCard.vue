<template>
  <div
    @click="$emit('click')"
    :class="[theme('card').value, 'transition-all duration-300 hover:shadow-2xl hover:scale-105 rounded-2xl p-6 cursor-pointer w-full group relative']"
  >
    <div class="flex items-start justify-between mb-4">
      <div
        :class="['flex items-center justify-center w-14 h-14 rounded-2xl shadow text-2xl font-bold', iconClass]"
      >
        <i :class="`fas fa-${icon}`"></i>
      </div>
      <div class="bg-cyan-100 text-cyan-600 px-3 py-1 rounded-full text-xs font-bold flex items-center border-none dark:bg-cyan-200/10 dark:text-cyan-200">
        <span>{{ cantidadPersonas }}</span>
      </div>
    </div>
    <div class="flex flex-col flex-1">
      <h3 :class="[theme('cardTitle').value,'font-bold mb-2 text-lg']">
        {{ nombre }}
      </h3>
      <p :class="[theme('cardSubtitle').value, 'text-sm']">
        {{ descripcion }}
      </p>
      <div v-if="seleccionada" class="mt-3 flex items-center gap-2 text-cyan-600 dark:text-yellow-300 text-xs font-medium">
        <i class="fas fa-check-circle"></i>
        <span>Área seleccionada</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useTheme } from '../composables/useTheme'

const { theme } = useTheme()
defineProps({
  nombre: String,
  descripcion: String,
  icon: String,
  cantidadPersonas: Number,
  seleccionada: Boolean,
  iconClass: String,
})
</script>