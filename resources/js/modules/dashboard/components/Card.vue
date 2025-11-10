<!-- resources/js/modules/dashboard/components/Card.vue -->
<template>
  <div class="p-4 rounded-2xl shadow-md text-white" :class="gradientClass">
    <div class="flex items-center justify-between">
      <div>
        <p class="text-sm font-medium">{{ title }}</p>
        <p class="text-2xl font-bold mt-1">{{ value }}</p>
      </div>
      <i :class="`fas fa-${icon} text-3xl`"></i>
    </div>
  </div>
</template>

<script setup>
defineProps({
  title: String,
  value: [String, Number],
  icon: String,
  gradientClass: String,
})
</script>
