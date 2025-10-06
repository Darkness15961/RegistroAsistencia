<template>
  <div class="w-full h-[300px] md:h-[400px] relative">
    <Line :data="chartData" :options="chartOptions" />
  </div>
</template>

<script setup>
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

// Función para crear gradiente
const createGradient = (ctx, chartArea) => {
  const gradient = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top)
  gradient.addColorStop(0, 'rgba(16, 185, 129, 0.0)')
  gradient.addColorStop(0.5, 'rgba(16, 185, 129, 0.3)')
  gradient.addColorStop(1, 'rgba(16, 185, 129, 0.6)')
  return gradient
}

const chartData = {
  labels: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio'],
  datasets: [
    {
      label: 'Empleados',
      borderColor: '#10B981',
      backgroundColor: (context) => {
        const chart = context.chart
        const {ctx, chartArea} = chart
        if (!chartArea) return 'rgba(16, 185, 129, 0.2)'
        return createGradient(ctx, chartArea)
      },
      tension: 0.4,
      fill: true,
      pointRadius: 6,
      pointHoverRadius: 8,
      pointBackgroundColor: '#10B981',
      pointBorderColor: '#fff',
      pointBorderWidth: 2,
      pointHoverBackgroundColor: '#059669',
      pointHoverBorderColor: '#fff',
      pointHoverBorderWidth: 3,
      borderWidth: 3,
      data: [5, 7, 8, 9, 11, 13]
    }
  ]
}

const chartOptions = {
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
        color: '#e5e7eb',
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
      color: '#f3f4f6',
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
        color: '#d1d5db',
        font: { 
          size: 11,
          weight: '500'
        }
      },
      grid: {
        color: 'rgba(255, 255, 255, 0.05)',
        drawBorder: false
      }
    },
    y: { 
      beginAtZero: true,
      ticks: { 
        color: '#d1d5db',
        font: { 
          size: 11
        },
        stepSize: 2
      },
      grid: {
        color: 'rgba(255, 255, 255, 0.05)',
        drawBorder: false
      }
    }
  }
}
</script>