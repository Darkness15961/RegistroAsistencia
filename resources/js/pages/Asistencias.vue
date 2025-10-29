<script setup>
import { ref, computed } from 'vue'
import AreaCard from '@/components/AreaCard.vue'
import TablaAsistencia from '@/components/TablaAsistencia.vue'

// Colores gradiente para cada área (idéntico a empleados)
const iconGradients = {
  'Dirección': 'bg-gradient-to-br from-blue-400 to-blue-600 text-white',
  'Administración': 'bg-gradient-to-br from-pink-400 to-pink-600 text-white',
  'Docentes de Primaria': 'bg-gradient-to-br from-orange-400 to-orange-600 text-white',
  'Docentes de Secundaria': 'bg-gradient-to-br from-violet-400 to-indigo-500 text-white',
  'Tutoría y Psicología': 'bg-gradient-to-br from-red-400 to-pink-500 text-white',
  'Mantenimiento y Limpieza': 'bg-gradient-to-br from-gray-400 to-gray-700 text-white',
  'Seguridad': 'bg-gradient-to-br from-cyan-400 to-cyan-600 text-white'
}

const areas = ref([
  { id: 1, nombre: 'Dirección', icon: 'user-tie' },
  { id: 2, nombre: 'Administración', icon: 'briefcase' },
  { id: 3, nombre: 'Docentes de Primaria', icon: 'chalkboard-teacher' },
  { id: 4, nombre: 'Docentes de Secundaria', icon: 'graduation-cap' },
  { id: 5, nombre: 'Tutoría y Psicología', icon: 'heart' },
  { id: 6, nombre: 'Mantenimiento y Limpieza', icon: 'broom' },
  { id: 7, nombre: 'Seguridad', icon: 'shield-alt' }
])

const empleados = ref([
  { id: 1, nombre: 'Carlos Ramírez López', cargo: 'Coordinador', areaId: 1, asistencia: { Lun: 'P', Mar: 'T', Mié: 'F', Jue: 'P', Vie: 'P' } },
  { id: 2, nombre: 'Ana García Torres', cargo: 'Secretaria', areaId: 2, asistencia: { Lun: 'P', Mar: 'P', Mié: 'P', Jue: 'P', Vie: 'T' } },
  { id: 3, nombre: 'María López Quispe', cargo: 'Docente Matemáticas', areaId: 4, asistencia: { Lun: 'P', Mar: 'P', Mié: 'F', Jue: 'T', Vie: 'P' } },
  { id: 4, nombre: 'Juan Pérez Silva', cargo: 'Docente Primaria', areaId: 3, asistencia: { Lun: 'F', Mar: 'P', Mié: 'P', Jue: 'P', Vie: 'P' } },
  { id: 5, nombre: 'Lucía Mendoza', cargo: 'Psicóloga', areaId: 5, asistencia: { Lun: 'P', Mar: 'P', Mié: 'P', Jue: 'T', Vie: 'F' } },
  { id: 6, nombre: 'Marco Ruiz', cargo: 'Mantenimiento', areaId: 6, asistencia: { Lun: 'P', Mar: 'P', Mié: 'P', Jue: 'P', Vie: 'P' } },
  { id: 7, nombre: 'Sofía Vargas', cargo: 'Vigilante', areaId: 7, asistencia: { Lun: 'P', Mar: 'P', Mié: 'T', Jue: 'P', Vie: 'P' } }
])

const areaSeleccionada = ref(null)

const cardsAsistencia = computed(() =>
  areas.value.map(area => ({
    ...area,
    nombre: `Asistencia ${area.nombre}`,
    descripcion: `Empleados del área de ${area.nombre.toLowerCase()}`,
    cantidadPersonas: empleados.value.filter(e => e.areaId === area.id).length,
    iconClass: iconGradients[area.nombre]
  }))
)

const empleadosDelArea = computed(() =>
  areaSeleccionada.value
    ? empleados.value.filter(e => e.areaId === areaSeleccionada.value.id)
    : []
)

function seleccionarArea(area) {
  areaSeleccionada.value = area
}
function volver() {
  areaSeleccionada.value = null
}
</script>

<template>
  <div class="flex-1 p-6 overflow-x-hidden">
    <div v-if="!areaSeleccionada" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
      <AreaCard
        v-for="area in cardsAsistencia"
        :key="area.id"
        :nombre="area.nombre"
        :descripcion="area.descripcion"
        :icon="area.icon"
        :iconClass="area.iconClass"
        :cantidadPersonas="area.cantidadPersonas"
        @click="seleccionarArea(area)"
      />
    </div>

    <TablaAsistencia
      v-else
      :registros="empleadosDelArea"
      :nombreArea="areaSeleccionada.nombre.replace(/^Asistencia /, '')"
      :iconoArea="areaSeleccionada.icon"
      @volver="volver"
    />
  </div>
</template>