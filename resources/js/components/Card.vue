<template>
  <div
    class="flex items-center bg-white/10 backdrop-blur-xl border border-white/15 rounded-3xl shadow-md p-4 sm:p-5 transition-transform transform hover:scale-[1.02] w-full"
  >
    <!-- Ícono con color dinámico -->
    <div
      class="text-white text-4xl mr-4 flex items-center justify-center w-16 h-16 rounded-2xl shadow-inner"
      :class="gradientClass"
    >
      <i :class="`fas fa-${icon}`"></i>
    </div>

    <!-- Texto -->
    <div class="flex flex-col flex-1">
      <h3 class="text-gray-200 text-xs sm:text-sm font-medium">{{ title }}</h3>
      <p class="text-xl sm:text-2xl font-bold text-white mt-1">{{ value }}</p>
      <span
        v-if="subtitle"
        class="bg-green-500 text-white text-[10px] sm:text-xs px-2 py-0.5 rounded-full mt-2 w-fit"
      >
        {{ subtitle }}
      </span>
    </div>
  </div>
</template>

<script setup>
defineProps({
  title: String,
  value: [String, Number],
  icon: String,
  subtitle: {
    type: String,
    default: ''
  },
  gradientClass: {
    type: String,
    default: 'bg-gradient-to-br from-cyan-400 to-blue-500'
  }
})
</script>