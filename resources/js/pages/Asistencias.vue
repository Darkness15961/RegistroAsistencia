<script setup>
import { ref } from 'vue'
import AreaCard from '@/components/AreaCard.vue'
import TablaAsistencia from '@/components/TablaAsistencia.vue'

// ----------------------
// 📘 CONFIGURACIÓN BASE
// ----------------------

const categorias = ref([
  { id: 1, nombre: 'Asistencia Empleados', descripcion: 'Registro de asistencia por área laboral', icon: 'user-tie', color: 'from-purple-400 to-purple-600' },
  { id: 2, nombre: 'Asistencia Alumnos Inicial', descripcion: 'Control de asistencia de estudiantes de inicial', icon: 'child', color: 'from-pink-400 to-pink-600' },
  { id: 3, nombre: 'Asistencia Alumnos Primaria', descripcion: 'Control de asistencia de primaria', icon: 'chalkboard-teacher', color: 'from-cyan-400 to-blue-500' },
  { id: 4, nombre: 'Asistencia Alumnos Secundaria', descripcion: 'Control de asistencia de secundaria', icon: 'user-graduate', color: 'from-orange-400 to-red-500' }
])

const categoriaSeleccionada = ref(null)
const filtroSeleccionado = ref(null)
const filtros = ref([])
const registros = ref([])

// ----------------------
// 🏢 LISTAS BASE
// ----------------------

const areasEmpleados = [
  'Dirección',
  'Administración',
  'Docentes de Primaria',
  'Docentes de Secundaria',
  'Tutoría y Psicología',
  'Mantenimiento y Limpieza',
  'Seguridad',
  'Biblioteca',
  'Laboratorio',
  'Coordinación Académica',
  'Servicio Médico'
]

const aulasInicial = ['Aula Arcoiris', 'Aula Sol', 'Aula Mariposa']
const gradosPrimaria = ['1°', '2°', '3°', '4°', '5°', '6°']
const gradosSecundaria = ['1°', '2°', '3°', '4°', '5°']

const nombres = [
  'Carlos', 'Ana', 'Lucía', 'María', 'José', 'Diego', 'Camila', 'Sofía', 'Pedro', 'Julieta',
  'Javier', 'Andrea', 'Sebastián', 'Natalia', 'Luis', 'Valeria', 'Gabriel', 'Daniela',
  'Rodrigo', 'Marta', 'Cristian', 'Bianca', 'Renzo', 'Claudia', 'Tomás', 'Rocío', 'Ángel', 'Karla'
]

const apellidos = [
  'Ramírez', 'Torres', 'Quispe', 'López', 'Gutiérrez', 'Campos', 'Ramos', 'Cárdenas', 'Soto', 'Pérez',
  'Castro', 'Salazar', 'Mendoza', 'Huamán', 'Vilca', 'Ticona', 'Condori', 'Flores', 'Vargas', 'Ortega'
]

// ----------------------
// ⚙️ FUNCIONES AUXILIARES
// ----------------------

function generarAsistenciaAleatoria() {
  const estados = ['P', 'F', 'T'] // Presente, Falta, Tarde
  const aleatorio = () => estados[Math.floor(Math.random() * estados.length)]
  return {
    Lun: aleatorio(),
    Mar: aleatorio(),
    Mié: aleatorio(),
    Jue: aleatorio(),
    Vie: aleatorio()
  }
}

function generarNombreCompleto() {
  const nombre = nombres[Math.floor(Math.random() * nombres.length)]
  const apellido1 = apellidos[Math.floor(Math.random() * apellidos.length)]
  const apellido2 = apellidos[Math.floor(Math.random() * apellidos.length)]
  return `${nombre} ${apellido1} ${apellido2}`
}

// ----------------------
// 🧩 FUNCIÓN PRINCIPAL
// ----------------------

const seleccionarCategoria = (categoria) => {
  categoriaSeleccionada.value = categoria
  filtroSeleccionado.value = null
  registros.value = []
  filtros.value = []

  if (categoria.id === 1) {
    // EMPLEADOS
    filtros.value = areasEmpleados.map((a, i) => ({ id: i + 1, nombre: a, icono: 'building' }))
    for (let i = 1; i <= 25; i++) {
      registros.value.push({
        id: i,
        nombre: generarNombreCompleto(),
        seccion: areasEmpleados[Math.floor(Math.random() * areasEmpleados.length)],
        asistencia: generarAsistenciaAleatoria()
      })
    }
  } else if (categoria.id === 2) {
    // INICIAL
    filtros.value = aulasInicial.map((a, i) => ({ id: i + 1, nombre: a, icono: 'school' }))
    for (let i = 26; i <= 50; i++) {
      registros.value.push({
        id: i,
        nombre: generarNombreCompleto(),
        seccion: aulasInicial[Math.floor(Math.random() * aulasInicial.length)],
        asistencia: generarAsistenciaAleatoria()
      })
    }
  } else if (categoria.id === 3) {
    // PRIMARIA
    filtros.value = gradosPrimaria.map((g, i) => ({ id: i + 1, nombre: `${g} Primaria`, icono: 'chalkboard' }))
    for (let i = 51; i <= 75; i++) {
      registros.value.push({
        id: i,
        nombre: generarNombreCompleto(),
        seccion: `${gradosPrimaria[Math.floor(Math.random() * gradosPrimaria.length)]} Primaria`,
        asistencia: generarAsistenciaAleatoria()
      })
    }
  } else if (categoria.id === 4) {
    // SECUNDARIA
    filtros.value = gradosSecundaria.map((g, i) => ({ id: i + 1, nombre: `${g} Secundaria`, icono: 'chalkboard' }))
    for (let i = 76; i <= 100; i++) {
      registros.value.push({
        id: i,
        nombre: generarNombreCompleto(),
        seccion: `${gradosSecundaria[Math.floor(Math.random() * gradosSecundaria.length)]} Secundaria`,
        asistencia: generarAsistenciaAleatoria()
      })
    }
  }
}
</script>

<template>
  <div>
    <div v-if="!categoriaSeleccionada" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <AreaCard
        v-for="categoria in categorias"
        :key="categoria.id"
        :nombre="categoria.nombre"
        :descripcion="categoria.descripcion"
        :icon="categoria.icon"
        :gradientClass="categoria.color"
        @click="seleccionarCategoria(categoria)"
      />
    </div>

    <TablaAsistencia
      v-else
      :registros="registros"
      :nombreArea="categoriaSeleccionada.nombre"
      :iconoArea="categoriaSeleccionada.icon"
      :filtros="filtros"
      :filtroSeleccionado="filtroSeleccionado"
      @volver="categoriaSeleccionada = null"
      @seleccionarFiltro="filtroSeleccionado = $event"
    />
  </div>
</template>
