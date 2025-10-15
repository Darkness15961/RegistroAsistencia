<template>
  <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
    <div>
      <h1 
        class="text-3xl font-bold mb-2"
        :class="theme('cardTitle').value"
      >
        {{ title }}
      </h1>
      <p :class="theme('cardSubtitle').value">
        {{ subtitle }}
      </p>
    </div>
    <button 
      v-if="showButton"
      class="px-6 py-3 rounded-xl transition-all duration-200 flex items-center gap-2 shadow-lg"
      :class="theme('buttonPrimary').value"
      @click="$emit('button-click')"
    >
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
      </svg>
      {{ buttonText }}
    </button>
  </div>
</template>

<script setup>
import { useTheme } from '../composables/useTheme'

const { theme } = useTheme()

defineProps({
  title: {
    type: String,
    required: true
  },
  subtitle: {
    type: String,
    default: ''
  },
  showButton: {
    type: Boolean,
    default: false
  },
  buttonText: {
    type: String,
    default: 'Nuevo'
  }
})

defineEmits(['button-click'])
</script>