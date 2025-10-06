<template>
  <div class="w-full h-[300px] md:h-[400px] relative">
    <Bar :data="chartData" :options="options" />
  </div>
</template>

<script setup>
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

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)

const chartData = {
  labels: ['Lun', 'Mar', 'Mié', 'Jue', 'Vie'],
  datasets: [
    {
      label: 'Asistencias',
      backgroundColor: '#3b82f6', // azul estilo Tailwind
      borderRadius: 6,
      data: [18, 22, 21, 25, 20]
    }
  ]
}

const options = {
  responsive: true,
  maintainAspectRatio: false, // responsividad
  plugins: {
    legend: {
      position: 'bottom',
      labels: { color: '#374151' } // gris oscuro para mejor contraste
    }
  },
  scales: {
    x: {
      ticks: { color: '#6b7280' }, // gris Tailwind
      grid: { color: 'rgba(0,0,0,0.05)' }
    },
    y: {
      beginAtZero: true,
      ticks: { color: '#6b7280' },
      grid: { color: 'rgba(0,0,0,0.05)' }
    }
  }
}
</script>