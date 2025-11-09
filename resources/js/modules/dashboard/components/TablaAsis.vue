<template>
  <div class="w-full h-[300px] md:h-[400px] relative">
    <Bar :data="props.chartData" :options="chartOptions" />
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { Bar } from 'vue-chartjs'
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale
} from 'chart.js'
// Importación corregida con el alias @
import { useTheme } from '@/composables/useTheme' 

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)

// 1. DEFINIR EL PROP chartData
const props = defineProps({
  chartData: {
    type: Object,
    required: true
  }
})

const { isDark } = useTheme()

// 2. ELIMINAR la definición estática de 'chartData' de este script.

const chartOptions = computed(() => ({
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      position: 'bottom',
      labels: { 
        color: isDark.value ? '#e5e7eb' : '#374151',
        font: {
          size: 13,
          weight: '500'
        },
        padding: 15
      }
    },
    title: {
      display: true,
      text: 'Asistencias Semanales',
      color: isDark.value ? '#f3f4f6' : '#111827',
      font: {
        size: 16,
        weight: 'bold'
      },
      padding: {
        bottom: 20
      }
    }
  },
  scales: {
    x: {
      ticks: { 
        color: isDark.value ? '#d1d5db' : '#6b7280',
        font: {
          size: 12,
          weight: '500'
        }
      },
      grid: { 
        color: isDark.value ? 'rgba(255, 255, 255, 0.05)' : 'rgba(0, 0, 0, 0.05)',
        drawBorder: false
      }
    },
    y: {
      beginAtZero: true,
      ticks: { 
        color: isDark.value ? '#d1d5db' : '#6b7280',
        font: {
          size: 12
        }
      },
      grid: { 
        color: isDark.value ? 'rgba(255, 255, 255, 0.05)' : 'rgba(0, 0, 0, 0.05)',
        drawBorder: false
      }
    }
  }
}))
</script>