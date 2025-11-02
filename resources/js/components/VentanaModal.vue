<template>
  <div 
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4"
    @click.self="$emit('close')"
  >
    <div 
      class="w-full max-w-lg rounded-3xl border shadow-2xl"
      :class="[theme('card').value, isDark ? 'border-gray-700' : 'border-gray-200']"
    >
      <div 
        class="flex items-center justify-between p-5 border-b"
        :class="isDark ? 'border-white/10' : 'border-gray-200'"
      >
        <h3 class="text-xl font-semibold" :class="theme('cardTitle').value">
          <slot name="header">Título del Modal</slot>
        </h3>
        <button 
          @click="$emit('close')"
          class="w-10 h-10 flex items-center justify-center rounded-xl transition-all"
          :class="theme('headerButton').value"
          title="Cerrar"
        >
          <i class="fas fa-times"></i>
        </button>
      </div>
      
      <div class="p-6 max-h-[70vh] overflow-y-auto">
        <slot name="body">Contenido del modal...</slot>
      </div>
      
      <div 
        class="flex justify-end gap-3 p-5 border-t"
        :class="isDark ? 'border-white/10' : 'border-gray-200'"
      >
        <slot name="footer">
          <button 
            @click="$emit('close')" 
            class="px-5 py-2.5 rounded-xl font-semibold"
            :class="theme('buttonSecondary').value"
          >
            Cerrar
          </button>
        </slot>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useTheme } from '../composables/useTheme'
const { theme, isDark } = useTheme()
defineEmits(['close'])
</script>