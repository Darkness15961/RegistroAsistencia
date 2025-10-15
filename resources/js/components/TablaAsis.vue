<template>
  <div class="w-full h-[300px] md:h-[400px] relative">
    <Bar :data="chartData" :options="chartOptions" />
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
import { useTheme } from '../composables/useTheme'

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)

const { isDark } = useTheme()

const chartData = {
  labels: ['Lun', 'Mar', 'Mié', 'Jue', 'Vie'],
  datasets: [
    {
      label: 'Asistencias',
      backgroundColor: [
        'rgba(59, 130, 246, 0.8)',   // Azul
        'rgba(16, 185, 129, 0.8)',   // Verde
        'rgba(139, 92, 246, 0.8)',   // Morado
        'rgba(236, 72, 153, 0.8)',   // Rosa
        'rgba(251, 146, 60, 0.8)'    // Naranja
      ],
      borderColor: [
        'rgb(59, 130, 246)',
        'rgb(16, 185, 129)',
        'rgb(139, 92, 246)',
        'rgb(236, 72, 153)',
        'rgb(251, 146, 60)'
      ],
      borderWidth: 2,
      borderRadius: 8,
      data: [18, 22, 21, 25, 20]
    }
  ]
}

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