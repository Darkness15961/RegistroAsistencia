<template>
  <div class="w-full h-[300px] md:h-[400px] relative">
    <Line :data="dynamicChartData" :options="chartOptions" />
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { Line } from 'vue-chartjs'
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  LineElement,
  PointElement,
  CategoryScale,
  LinearScale,
  Filler
} from 'chart.js'
// Importación corregida con el alias @
import { useTheme } from '@/composables/useTheme'

ChartJS.register(
  Title,
  Tooltip,
  Legend,
  LineElement,
  PointElement,
  CategoryScale,
  LinearScale,
  Filler
)

// 1. DEFINIR EL PROP chartData
const props = defineProps({
  chartData: {
    type: Object,
    required: true
  }
})

const { isDark } = useTheme()

// Función para crear gradiente (DEBE PERMANECER IGUAL)
const createGradient = (ctx, chartArea) => {
  const gradient = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top)
  gradient.addColorStop(0, 'rgba(16, 185, 129, 0.0)')
  gradient.addColorStop(0.5, 'rgba(16, 185, 129, 0.3)')
  gradient.addColorStop(1, 'rgba(16, 185, 129, 0.6)')
  return gradient
}

// 2. CREAR PROPIEDAD COMPUTADA PARA INYECTAR EL GRADIENTE DINÁMICAMENTE
const dynamicChartData = computed(() => {
    // Si no hay datos, devolvemos un objeto vacío para evitar errores
    if (!props.chartData || !props.chartData.datasets) return { datasets: [] }

    // Inyectamos el callback de gradiente en el dataset principal
    const datasetsWithGradient = props.chartData.datasets.map(dataset => {
        // Solo inyectamos si el dataset es para empleados (o el que queremos con gradiente)
        if (dataset.label === 'Empleados') { 
            return {
                ...dataset,
                backgroundColor: (context) => {
                    const chart = context.chart
                    const {ctx, chartArea} = chart
                    if (!chartArea) return 'rgba(16, 185, 129, 0.2)'
                    return createGradient(ctx, chartArea)
                },
            }
        }
        return dataset
    })

    return {
        ...props.chartData,
        datasets: datasetsWithGradient
    }
})

// 3. ELIMINAR la definición estática de 'chartData' de este script.

const chartOptions = computed(() => ({
  // ... (El resto de tu código para chartOptions es correcto)
  responsive: true,
  maintainAspectRatio: false,
  interaction: {
    intersect: false,
    mode: 'index'
  },
  plugins: {
    legend: { 
      display: true,
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
      text: 'Empleados Registrados por Mes',
      color: isDark.value ? '#f3f4f6' : '#111827',
      font: {
        size: 16,
        weight: 'bold'
      },
      padding: {
        bottom: 20
      }
    },
    tooltip: {
      backgroundColor: 'rgba(0, 0, 0, 0.8)',
      titleColor: '#fff',
      bodyColor: '#fff',
      padding: 12,
      displayColors: true,
      borderColor: '#10B981',
      borderWidth: 1
    }
  },
  scales: {
    x: { 
      ticks: { 
        color: isDark.value ? '#d1d5db' : '#6b7280',
        font: { 
          size: 11,
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
          size: 11
        },
        stepSize: 2
      },
      grid: {
        color: isDark.value ? 'rgba(255, 255, 255, 0.05)' : 'rgba(0, 0, 0, 0.05)',
        drawBorder: false
      }
    }
  }
}))
</script>