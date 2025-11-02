<template>
  <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
    <div>
      <h1 class="text-3xl font-bold mb-2" :class="theme('cardTitle').value">{{ title }}</h1>
      <p class="text-sm" :class="theme('cardSubtitle').value">{{ subtitle }}</p>
    </div>

    <button
      v-if="showButton"
      @click="$emit('button-click')"
      class="flex items-center gap-2 px-6 py-3 rounded-xl font-semibold whitespace-nowrap shadow-lg hover:scale-105 transition-all duration-200"
      :class="theme('buttonPrimary').value"
    >
      <i :class="`fas ${buttonIcon}`"></i>
      {{ buttonText }}
    </button>
  </div>
</template>

<script setup>
import { useTheme } from '../composables/useTheme'
const { theme } = useTheme()

defineProps({
  title: String,
  subtitle: String,
  showButton: { type: Boolean, default: false },
  buttonText: { type: String, default: 'Action' },
  buttonIcon: { type: String, default: 'fa-plus' } // Prop opcional para el ícono
})
defineEmits(['button-click'])
</script>