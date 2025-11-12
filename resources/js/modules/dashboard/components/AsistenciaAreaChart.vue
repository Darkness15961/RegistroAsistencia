<template>
  <Pie v-if="chartData.datasets.length" :data="chartData" :options="chartOptions" />
</template>

<script setup>
import { computed } from 'vue'
import { Pie } from 'vue-chartjs'
import { Chart as ChartJS, ArcElement, Tooltip, Legend } from 'chart.js'
// ✅ ¡Importa tu composable correcto!
import { useTheme } from '@/composables/useTheme'

ChartJS.register(ArcElement, Tooltip, Legend)

defineProps({
  chartData: {
    type: Object,
    required: true,
    default: () => ({ labels: [], datasets: [] })
  }
})

// ✅ Usa tu hook reactivo
const { isDark } = useTheme()

const chartOptions = computed(() => {
  const legendColor = isDark.value ? '#d1d5db' : '#374151'
  const borderColor = isDark.value ? '#1f2937' : '#ffffff'

  return {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: {
        position: 'bottom',
        labels: {
          color: legendColor, // Color dinámico
          boxWidth: 20,
          padding: 20,
        }
      }
    },
    datasets: {
      pie: {
        borderColor: borderColor,
        borderWidth: 4,
      }
    }
  }
})
</script>