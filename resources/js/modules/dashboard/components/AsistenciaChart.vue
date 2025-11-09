<template>
  <div style="height: 350px">
    <Line :data="chartData" :options="chartOptions" />
  </div>
</template>

<script setup>
import { Line } from 'vue-chartjs'
import { 
  Chart as ChartJS, 
  CategoryScale, 
  LinearScale, 
  PointElement, 
  LineElement, 
  Title, 
  Tooltip, 
  Legend,
  Filler 
} from 'chart.js'

ChartJS.register(
  CategoryScale, 
  LinearScale, 
  PointElement, 
  LineElement, 
  Title, 
  Tooltip, 
  Legend,
  Filler
)

// Estos son datos de ejemplo. Luego los cargaremos desde la API.
const chartData = {
  labels: ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'],
  datasets: [
    {
      label: 'Asistencias',
      backgroundColor: 'rgba(59, 130, 246, 0.2)', // Color de fondo (área)
      borderColor: 'rgba(59, 130, 246, 1)', // Color de la línea
      pointBackgroundColor: 'rgba(59, 130, 246, 1)',
      pointBorderColor: '#fff',
      pointHoverBackgroundColor: '#fff',
      pointHoverBorderColor: 'rgba(59, 130, 246, 1)',
      data: [12, 19, 15, 22, 18, 20, 25], // Datos de ejemplo
      fill: true, // Rellenar el área bajo la línea
      tension: 0.4 // Suavizar la línea
    }
  ]
}

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      display: false // Ocultar la leyenda (como en la imagen)
    }
  },
  scales: {
    y: {
      beginAtZero: true
    }
  }
}
</script>