<template>
  <Doughnut v-if="chartData.datasets.length" :data="chartData" :options="chartOptions" />
</template>

<script setup>
import { computed } from 'vue'
import { Doughnut } from 'vue-chartjs'
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
  const legendColor = isDark.value ? '#d1d5db' : '#374151' // gris-300 vs gris-700
  
  // El borde debe coincidir con el fondo de la tarjeta
  // Tu theme('card') usa 'bg-white/10' (oscuro) y 'bg-white' (claro)
  // 'bg-white/10' es translúcido, usaremos un color sólido que se le parezca
  const borderColor = isDark.value ? '#1f2937' : '#ffffff' // gris-800 vs blanco

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
    cutout: '60%',
    // Actualiza el borde para que coincida con el fondo
    datasets: {
      doughnut: {
        borderColor: borderColor,
        borderWidth: 4,
      }
    }
  }
})
</script>