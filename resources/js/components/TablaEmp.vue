<template>
  <div class="w-full h-[300px] md:h-[400px] bg-white rounded-xl shadow-md p-6">
    <h3 class="text-lg font-semibold text-gray-700 mb-4">
      Empleados Registrados por Mes
    </h3>
    <div class="relative w-full h-full">
      <Line :data="chartData" :options="chartOptions" />
    </div>
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
  LinearScale
} from 'chart.js'

ChartJS.register(
  Title,
  Tooltip,
  Legend,
  LineElement,
  PointElement,
  CategoryScale,
  LinearScale
)

const chartData = {
  labels: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio'],
  datasets: [
    {
      label: 'Empleados',
      borderColor: '#10B981', // Tailwind green-500
      backgroundColor: 'rgba(16, 185, 129, 0.2)',
      tension: 0.4,
      fill: true,
      pointRadius: 5,
      pointBackgroundColor: '#10B981',
      data: [5, 7, 8, 9, 11, 13]
    }
  ]
}

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { display: false },
  },
  scales: {
    x: { ticks: { color: '#6B7280', font: { size: 10 } } },
    y: { ticks: { color: '#6B7280', font: { size: 10 } } },
  },
  elements: {
    point: { radius: 3 }, // puntos más pequeños
  }
}

</script>
