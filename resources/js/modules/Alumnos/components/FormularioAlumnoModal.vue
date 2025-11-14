<template>
  <div class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4"
       @click.self="$emit('cerrar')">
    <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl border p-6" :class="theme('card').value">
      <h2 class="text-xl font-bold mb-6" :class="theme('cardTitle').value">
        <i class="fas fa-user-graduate mr-2"></i>
        {{ alumno ? 'Editar Alumno' : 'Registrar Alumno' }}
      </h2>

      <form @submit.prevent="guardar">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">
              Nombre Completo
            </label>
            <input v-model="form.nombre_completo" placeholder="Nombre completo" class="w-full rounded-xl" :class="theme('input').value" required />
          </div>
          <div>
            <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">
              DNI
            </label>
            <input v-model="form.dni" placeholder="DNI" class="w-full rounded-xl" :class="theme('input').value" required />
          </div>
          <div>
            <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">
              Correo Electrónico
            </label>
            <input v-model="form.correo" placeholder="Correo electrónico" type="email" class="w-full rounded-xl" :class="theme('input').value" required />
          </div>
          <div>
            <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">
              Teléfono (Opcional)
            </label>
            <input v-model="form.telefono" placeholder="Teléfono" class="w-full rounded-xl" :class="theme('input').value" />
          </div>
          <div>
            <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">
              Grado y Sección
            </label>
            <input v-model="form.cargo_grado" placeholder="Ej: 5to Secundaria 'A'" class="w-full rounded-xl" :class="theme('input').value" />
          </div>
          
          <div>
            <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">
              Grupo
            </label>
            <select v-model="form.id_grupo" class="w-full rounded-xl appearance-none pr-8" :class="[theme('input').value, isDark ? 'bg-gray-900/50' : 'bg-white']" required>
              <option value="">Seleccionar grupo</option>
              <option v-for="grupo in gruposDeAlumnos" :key="grupo.id_grupo" :value="grupo.id_grupo" :class="isDark ? 'bg-gray-800' : 'bg-white'">
                {{ getNombreGrupo(grupo) }}
              </option>
            </select>
          </div>
        </div>

        <div class="flex justify-end gap-3 pt-6">
          <button type="button" @click="$emit('cerrar')" class="px-5 py-2.5 rounded-xl font-semibold" :class="theme('buttonSecondary').value">
            Cancelar
          </button>
          <button type="submit" :disabled="loading" class="px-5 py-2.5 rounded-xl font-semibold shadow-lg" :class="[theme('buttonPrimary').value, loading && 'opacity-50']">
            <i v-if="loading" class="fas fa-spinner animate-spin mr-2"></i>
            {{ alumno ? 'Actualizar' : 'Guardar' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted, computed } from 'vue'
import { useTheme } from '@/composables/useTheme'
import { useAlumnos } from '@/composables/useAlumnos'
import api from '@/axiosConfig'

const props = defineProps({ 
  alumno: Object,
})
const emit = defineEmits(['cerrar', 'actualizado'])

const { theme, isDark } = useTheme()
const { crearAlumno, actualizarAlumno } = useAlumnos()
const loading = ref(false)

const areas = ref([])
const grupos = ref([])

const form = ref({
  nombre_completo: '',
  dni: '',
  correo: '',
  telefono: '',
  cargo_grado: '',
  id_area: '',
  id_grupo: '',
})

const gruposConArea = computed(() => {
  return grupos.value.map(g => ({
    ...g,
    area: areas.value.find(a => a.id_area === g.id_area)
  }))
})

// ✅ CORREGIDO: de 'estudiante' a 'alumno'
const gruposDeAlumnos = computed(() => {
  return gruposConArea.value.filter(g => 
    g.area && g.area.nombre_area.toLowerCase().includes('alumno')
  )
})

onMounted(async () => {
  try {
    const [resAreas, resGrupos] = await Promise.all([
      api.get('/areas'),
      api.get('/grupos')
    ])
    areas.value = resAreas.data
    grupos.value = resGrupos.data.data || resGrupos.data
  } catch (e) {
    console.error("Error cargando datos para el modal", e)
  }
})

watch(() => props.alumno, (nuevo) => {
  if (nuevo) {
    form.value = { 
      nombre_completo: nuevo.nombre_completo || '',
      dni: nuevo.dni || '',
      correo: nuevo.correo || '',
      telefono: nuevo.telefono || '',
      cargo_grado: nuevo.cargo_grado || '',
      id_area: nuevo.id_area || '',
      id_grupo: nuevo.id_grupo || '',
    }
  } else {
    form.value = { nombre_completo: '', dni: '', correo: '', telefono: '', cargo_grado: '', id_area: '', id_grupo: '' }
  }
}, { immediate: true })

const guardar = async () => {
  loading.value = true
  try {
    const grupoSeleccionado = grupos.value.find(g => g.id_grupo === form.value.id_grupo)
    if (grupoSeleccionado) {
      form.value.id_area = grupoSeleccionado.id_area
    } else {
      alert("Error: No se pudo encontrar el área para el grupo seleccionado.")
      loading.value = false
      return
    }

    if (props.alumno) {
      await actualizarAlumno(props.alumno.id_persona, form.value)
    } else {
      await crearAlumno(form.value)
    }
    emit('actualizado')
    emit('cerrar')
  } catch (err) {
    alert(err.error || 'Error al guardar alumno')
  } finally {
    loading.value = false
  }
}

const getNombreGrupo = (grupo) => {
  if (grupo.grado || grupo.nivel) {
    return `${grupo.nivel || ''} ${grupo.grado || ''} "${grupo.seccion || ''}"`.trim()
  }
  return grupo.area?.nombre_area || 'Grupo'
}
</script>