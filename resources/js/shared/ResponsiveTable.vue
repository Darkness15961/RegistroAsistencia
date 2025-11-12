<template>
  <div class="overflow-x-auto">
    <table class="min-w-full hidden sm:table">
      <thead :class="theme('tableHeader').value">
        <tr>
          <th
            v-for="(header, index) in headers"
            :key="index"
            class="text-left px-4 py-3 text-sm font-semibold"
            :class="theme('cardSubtitle').value"
          >
            {{ header }}
          </th>
        </tr>
      </thead>
      <tbody class="divide-y" :class="isDark ? 'divide-white/10' : 'divide-gray-200'">
        <slot></slot>
      </tbody>
    </table>

    <div class="sm:hidden space-y-3">
      <div
        v-for="(row, index) in mobileRows"
        :key="index"
        class="p-4 rounded-xl border"
        :class="theme('card').value"
      >
        <slot name="mobile" :row="row"></slot>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useTheme } from '@/composables/useTheme'

const { theme, isDark } = useTheme()

defineProps({
  headers: {
    type: Array,
    required: true
  },
  mobileRows: {
    type: Array,
    default: () => []
  }
})
</script>