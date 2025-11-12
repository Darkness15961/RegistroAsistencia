<template>
  <Line v-if="chartData.datasets.length" :data="computedChartData" :options="chartOptions" />
</template>

<script setup>
import { computed } from 'vue'
import { Line } from 'vue-chartjs'
import {
  Chart as ChartJS, CategoryScale, LinearScale, PointElement, LineElement, Title, Tooltip, Legend, Filler
} from 'chart.js'
// ✅ ¡Importa tu composable correcto!
import { useTheme } from '@/composables/useTheme'

ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, Title, Tooltip, Legend, Filler)

const props = defineProps({
  chartData: {
    type: Object,
    required: true,
    default: () => ({ labels: [], datasets: [] })
  }
})

// ✅ Usa tu hook reactivo
const { isDark } = useTheme()

const computedChartData = computed(() => {
  // ... (el código del gradiente se mantiene igual)
  const chart = { ...props.chartData }
  if (chart.datasets[0]) {
    chart.datasets[0].backgroundColor = (context) => {
        const ctx = context.chart.ctx;
        if (!ctx) return 'rgba(74, 222, 128, 0.1)';
        const gradient = ctx.createLinearGradient(0, 0, 0, 250);
        gradient.addColorStop(0, "rgba(74, 222, 128, 0.5)");
        gradient.addColorStop(1, "rgba(74, 222, 128, 0.05)");
        return gradient;
    }
  }
  return chart;
})

const chartOptions = computed(() => {
  const gridColor = isDark.value ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.05)'
  const tickColor = isDark.value ? '#9ca3af' : '#4b5563'

  return {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: {
        display: false,
      },
    },
    scales: {
      x: {
        grid: {
          display: false,
        },
        ticks: {
          color: tickColor, // Color dinámico
        },
      },
      y: {
        beginAtZero: true,
        grid: {
          color: gridColor, // Color dinámico
        },
        ticks: {
          color: tickColor, // Color dinámico
        },
      },
    },
    elements: {
      line: {
        tension: 0.4,
      },
    },
  }
})
</script>