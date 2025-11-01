<template>
  <div class="flex-1 p-6 overflow-x-hidden">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
      <div>
        <h1 class="text-3xl font-bold mb-2" :class="theme('cardTitle').value">
          Lista de Áreas
        </h1>
        <p class="text-sm" :class="theme('cardSubtitle').value">
          Gestiona las áreas y departamentos del colegio
        </p>
      </div>

      <button
        @click="mostrarModal = true"
        class="flex items-center gap-2 px-6 py-3 rounded-xl font-semibold shadow-lg hover:scale-105 transition-all duration-200 whitespace-nowrap"
        :class="theme('buttonPrimary').value"
      >
        <i class="fas fa-plus"></i>
        Nuevo Área
      </button>
    </div>

    <div class="relative w-full sm:max-w-md mb-6">
      <input
        v-model="busqueda"
        type="text"
        placeholder="Buscar área..."
        class="w-full rounded-xl pl-10 pr-4 py-3 outline-none border transition-colors"
        :class="theme('input').value"
      />
      <i
        class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2"
        :class="theme('cardSubtitle').value"
      ></i>
    </div>

    <div
      class="hidden md:block backdrop-blur-xl border rounded-3xl shadow-2xl overflow-hidden"
      :class="theme('card').value"
    >
      <table class="w-full text-left">
        <thead :class="theme('tableHeader').value">
          <tr class="border-b" :class="isDark ? 'border-white/20' : 'border-gray-200'">
            <th class="px-6 py-4 text-sm font-bold uppercase tracking-wide" :class="theme('cardSubtitle').value">ID</th>
            <th class="px-6 py-4 text-sm font-bold uppercase tracking-wide" :class="theme('cardSubtitle').value">Nombre</th>
            <th class="px-6 py-4 text-sm font-bold uppercase tracking-wide" :class="theme('cardSubtitle').value">Descripción</th>
            <th class="px-6 py-4 text-center text-sm font-bold uppercase tracking-wide" :class="theme('cardSubtitle').value">Acción</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(area, index) in areasFiltradas"
            :key="area.id"
            class="border-b transition-all"
            :class="[
              theme('tableRow').value,
              isDark ? 'border-white/5' : 'border-gray-100',
              index % 2 === 0 ? (isDark ? 'bg-white/[0.03]' : 'bg-gray-50/50') : ''
            ]"
          >
            <td class="px-6 py-4">
              <span class="font-mono text-sm" :class="theme('cardSubtitle').value">
                {{ area.id }}
              </span>
            </td>
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <div
                  :class="[
                    'w-10 h-10 rounded-xl flex items-center justify-center shadow-lg bg-gradient-to-br text-white',
                    getAreaStyle(area.nombre).gradient
                  ]"
                >
                  <i :class="['text-lg', getAreaStyle(area.nombre).icon]"></i>
                </div>
                <span class="font-semibold" :class="theme('cardTitle').value">
                  {{ area.nombre }}
                </span>
              </div>
            </td>
            <td class="px-6 py-4" :class="theme('cardSubtitle').value">
              {{ area.descripcion }}
            </td>
            <td class="px-6 py-4">
              <div class="flex justify-center gap-2">
                <button
                  class="p-2 rounded-xl transition-all hover:scale-110 group"
                  :class="isDark
                    ? 'bg-blue-500/20 hover:bg-blue-500/30 border border-blue-500/30'
                    : 'bg-blue-50 hover:bg-blue-100 border border-blue-200'"
                  title="Editar"
                >
                  <i
                    class="fas fa-edit"
                    :class="isDark ? 'text-blue-300 group-hover:text-blue-200' : 'text-blue-600 group-hover:text-blue-700'"
                  ></i>
                </button>
                <button
                  class="p-2 rounded-xl transition-all hover:scale-110 group"
                  :class="isDark
                    ? 'bg-red-500/20 hover:bg-red-500/30 border border-red-500/30'
                    : 'bg-red-50 hover:bg-red-100 border border-red-200'"
                  title="Eliminar"
                >
                  <i
                    class="fas fa-trash"
                    :class="isDark ? 'text-red-300 group-hover:text-red-200' : 'text-red-600 group-hover:text-red-700'"
                  ></i>
                </button>
              </div>
            </td>
          </tr>

          <tr v-if="areasFiltradas.length === 0">
            <td colspan="4" class="text-center py-10" :class="theme('cardSubtitle').value">
              <i class="fas fa-search text-4xl mb-3 opacity-60"></i>
              <p>No se encontraron áreas</p>
            </td>
          </tr>
        </tbody>
      </table>

      <div
        class="border-t px-6 py-4"
        :class="isDark ? 'bg-white/5 border-white/10' : 'bg-gray-50 border-gray-200'"
      >
        <p class="text-sm" :class="theme('cardSubtitle').value">
          Mostrando
          <span class="font-semibold" :class="theme('cardTitle').value">{{ areasFiltradas.length }}</span>
          de
          <span class="font-semibold" :class="theme('cardTitle').value">{{ areas.length }}</span>
          áreas
        </p>
      </div>
    </div>

    <div class="grid gap-4 md:hidden">
      <div
        v-for="area in areasFiltradas"
        :key="area.id"
        class="backdrop-blur-xl border rounded-3xl shadow-lg p-5"
        :class="theme('card').value"
      >
        <div class="flex items-start justify-between gap-3 mb-3">
          <div class="flex items-center gap-3">
            <div
              :class="[
                'w-12 h-12 rounded-2xl flex items-center justify-center shadow-lg bg-gradient-to-br text-white',
                getAreaStyle(area.nombre).gradient
              ]"
            >
              <i :class="['text-lg', getAreaStyle(area.nombre).icon]"></i>
            </div>
            <div>
              <h2 class="text-lg font-semibold" :class="theme('cardTitle').value">
                {{ area.nombre }}
              </h2>
              <span class="text-xs px-2 py-1 rounded-full" :class="isDark ? 'bg-white/10 text-gray-300' : 'bg-gray-100 text-gray-600'">
                ID: {{ area.id }}
              </span>
            </div>
          </div>

          <div class="flex gap-2">
            <button
              class="p-2 rounded-xl transition-all hover:scale-110 group"
              :class="isDark
                ? 'bg-blue-500/20 hover:bg-blue-500/30 border border-blue-500/30'
                : 'bg-blue-50 hover:bg-blue-100 border border-blue-200'"
              title="Editar"
            >
              <i
                class="fas fa-edit"
                :class="isDark ? 'text-blue-300 group-hover:text-blue-200' : 'text-blue-600 group-hover:text-blue-700'"
              ></i>
            </button>
            <button
              class="p-2 rounded-xl transition-all hover:scale-110 group"
              :class="isDark
                ? 'bg-red-500/20 hover:bg-red-500/30 border border-red-500/30'
                : 'bg-red-50 hover:bg-red-100 border border-red-200'"
              title="Eliminar"
            >
              <i
                class="fas fa-trash"
                :class="isDark ? 'text-red-300 group-hover:text-red-200' : 'text-red-600 group-hover:text-red-700'"
              ></i>
            </button>
          </div>
        </div>

        <p class="text-sm" :class="theme('cardSubtitle').value">
          {{ area.descripcion }}
        </p>
      </div>

      <div
        v-if="areasFiltradas.length === 0"
        class="text-center py-12 rounded-xl border"
        :class="[theme('card').value, theme('cardSubtitle').value]"
      >
        <i class="fas fa-search text-5xl mb-4 opacity-50"></i>
        <p class="text-lg">No se encontraron áreas</p>
      </div>
    </div>

    <FormularioAreaModal 
      v-if="mostrarModal"
      @close="mostrarModal = false"
      @save="handleSaveArea"
    />

  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useTheme } from '../composables/useTheme'
import FormularioAreaModal from '../components/FormularioAreaModal.vue' // <-- 1. Importar modal

const { theme, isDark } = useTheme()

// --- 2. Lógica del Modal ---
const mostrarModal = ref(false)

const handleSaveArea = (nuevaArea) => {
  console.log('Guardando nueva área:', nuevaArea)
  // Lógica simulada para añadir la nueva área
  const nuevaId = Math.max(...areas.value.map(a => a.id)) + 1
  areas.value.push({
    id: nuevaId,
    ...nuevaArea
  })
  mostrarModal.value = false // Cerrar el modal
}
// --- Fin Lógica del Modal ---

const busqueda = ref('')

const areas = ref([
  { id: 1, nombre: 'Dirección', descripcion: 'Supervisión general y gestión institucional del colegio' },
  { id: 2, nombre: 'Administración', descripcion: 'Gestión administrativa, contable y de recursos del colegio' },
  { id: 3, nombre: 'Docentes de Primaria', descripcion: 'Profesores encargados del nivel de educación primaria' },
  { id: 4, nombre: 'Docentes de Secundaria', descripcion: 'Profesores encargados del nivel de educación secundaria' },
  { id: 5, nombre: 'Alumnos de Inicial', descripcion: 'Estudiantes pertenecientes al nivel inicial' },
  { id: 6, nombre: 'Alumnos de Primaria', descripcion: 'Estudiantes pertenecientes al nivel primaria' },
  { id: 7, nombre: 'Alumnos de Secundaria', descripcion: 'Estudiantes pertenecientes al nivel secundaria' },
  { id: 8, nombre: 'Tutoría y Psicología', descripcion: 'Área de orientación y apoyo emocional y académico' },
  { id: 9, nombre: 'Mantenimiento y Limpieza', descripcion: 'Personal encargado del mantenimiento y limpieza de las instalaciones' },
  { id: 10, nombre: 'Seguridad', descripcion: 'Personal de vigilancia y control de accesos del colegio' },
  { id: 11, nombre: 'Biblioteca', descripcion: 'Gestión de recursos bibliográficos y apoyo académico' },
  { id: 12, nombre: 'Laboratorio', descripcion: 'Gestión de prácticas científicas y tecnológicas' },
  { id: 13, nombre: 'Coordinación Académica', descripcion: 'Supervisión y coordinación de las áreas educativas' },
  { id: 14, nombre: 'Servicio Médico', descripcion: 'Atención médica y primeros auxilios dentro del colegio' }
])

const styleCatalog = {
  'Dirección':            { gradient: 'from-indigo-400 to-indigo-600', icon: 'fas fa-building' },
  'Administración':       { gradient: 'from-cyan-400 to-blue-500',    icon: 'fas fa-briefcase' },
  'Docentes de Primaria': { gradient: 'from-amber-400 to-orange-500', icon: 'fas fa-chalkboard-teacher' },
  'Docentes de Secundaria': { gradient: 'from-pink-400 to-rose-500',  icon: 'fas fa-user-graduate' },
  'Alumnos de Inicial':   { gradient: 'from-emerald-400 to-green-500', icon: 'fas fa-child' },
  'Alumnos de Primaria':  { gradient: 'from-blue-400 to-sky-500',     icon: 'fas fa-book-reader' },
  'Alumnos de Secundaria':{ gradient: 'from-purple-400 to-fuchsia-500', icon: 'fas fa-users' },
  'Tutoría y Psicología': { gradient: 'from-teal-400 to-green-500',   icon: 'fas fa-hands-helping' },
  'Mantenimiento y Limpieza': { gradient: 'from-slate-400 to-slate-600', icon: 'fas fa-broom' },
  'Seguridad':            { gradient: 'from-red-400 to-rose-500',     icon: 'fas fa-shield-alt' },
  'Biblioteca':           { gradient: 'from-yellow-400 to-amber-500', icon: 'fas fa-book' },
  'Laboratorio':          { gradient: 'from-lime-400 to-green-500',   icon: 'fas fa-flask' },
  'Coordinación Académica': { gradient: 'from-sky-400 to-blue-500',   icon: 'fas fa-sitemap' },
  'Servicio Médico':      { gradient: 'from-rose-400 to-red-500',     icon: 'fas fa-heartbeat' }
}

const defaultStyle = { gradient: 'from-slate-400 to-slate-600', icon: 'fas fa-building' }

const getAreaStyle = (nombre) => styleCatalog[nombre] ?? defaultStyle

const areasFiltradas = computed(() =>
  areas.value.filter(a =>
    a.nombre.toLowerCase().includes(busqueda.value.toLowerCase()) ||
    a.descripcion.toLowerCase().includes(busqueda.value.toLowerCase())
  )
)
</script>

<style scoped>
table {
  border-collapse: collapse;
}
</style>