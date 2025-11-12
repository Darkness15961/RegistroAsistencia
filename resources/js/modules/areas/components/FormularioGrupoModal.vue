<template>
  <div 
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4"
    @click.self="$emit('cerrar')"
  >
    <div 
      class="w-full max-w-lg rounded-3xl border shadow-2xl p-6" 
      :class="theme('card').value"
    >
      <h3 class="text-xl font-bold mb-6 flex items-center" :class="theme('cardTitle').value">
        <span :class="['w-8 h-8 rounded-lg flex items-center justify-center mr-3 shadow-lg', getAreaStyle(area.nombre_area).gradient]">
           <i :class="[getAreaStyle(area.nombre_area).icon, 'text-white text-base']"></i>
        </span>
        {{ grupo ? 'Editar Grupo' : 'Nuevo Grupo' }}
      </h3>

      <form @submit.prevent="guardar" class="space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <div>
            <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">Nivel</label>
            <input v-model="form.nivel" placeholder="Ej: Secundaria" class="w-full rounded-xl" :class="theme('input').value" required />
          </div>
          <div>
            <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">Grado</label>
            <input v-model="form.grado" placeholder="Ej: Quinto" class="w-full rounded-xl" :class="theme('input').value" required />
          </div>
          <div>
            <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">Sección</label>
            <input v-model="form.seccion" placeholder="Ej: 'A'" class="w-full rounded-xl" :class="theme('input').value" required />
          </div>
        </div>
        
        <div>
          <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">Tutor (Opcional)</label>
          <select v-model="form.id_tutor" class="w-full rounded-xl appearance-none pr-8" :class="[theme('input').value, isDark ? 'bg-gray-900/50' : 'bg-white']">
            <option :value="null">No asignado</option>
            <option v-for="tutor in tutores" :key="tutor.id_persona" :value="tutor.id_persona" :class="isDark ? 'bg-gray-800' : 'bg-white'">
              {{ tutor.nombre_completo }}
            </option>
          </select>
        </div>

        <div class="flex justify-end gap-3 pt-4">
          <button type="button" @click="$emit('cerrar')" class="px-5 py-2.5 rounded-xl font-semibold" :class="theme('buttonSecondary').value">
            Cancelar
          </button>
          <button type="submit" :disabled="loading" class="px-5 py-2.5 rounded-xl font-semibold shadow-lg" :class="[theme('buttonPrimary').value, loading && 'opacity-50']">
            <i v-if="loading" class="fas fa-spinner animate-spin mr-2"></i>
            {{ grupo ? 'Actualizar' : 'Guardar' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'
import { useTheme } from '@/composables/useTheme'
import { useGrupos } from '@/composables/useGrupos'
import api from '@/axiosConfig'

const props = defineProps({ 
  grupo: Object,
  area: { type: Object, required: true } // ✅ Recibimos el objeto 'area' completo
})
const emit = defineEmits(['cerrar', 'actualizado'])

const { theme, isDark } = useTheme()
const { crearGrupo, actualizarGrupo } = useGrupos()
const loading = ref(false)
const tutores = ref([]) // Para el dropdown

const form = ref({
  nivel: '',
  grado: '',
  seccion: '',
  id_tutor: null,
  id_area: props.area.id_area // Asignamos el ID del área
})

watch(() => props.grupo, (val) => {
  if (val) {
    form.value = {
      id_area: val.id_area,
      nivel: val.nivel,
      grado: val.grado,
      seccion: val.seccion,
      id_tutor: val.id_tutor
    }
  }
}, { immediate: true })

// ✅ Carga los usuarios (para tutores) al montar
const obtenerTutores = async () => {
  try {
    const res = await api.get('/usuarios')
    // Filtramos usuarios que tengan una persona asociada
    tutores.value = (res.data.data || res.data)
      .filter(u => u.persona)
      .map(u => u.persona)
  } catch (e) {
    console.error("No se pudo cargar la lista de tutores", e)
  }
}
onMounted(obtenerTutores)

const guardar = async () => {
  loading.value = true
  try {
    const payload = { ...form.value }
    if (!payload.id_tutor) {
      payload.id_tutor = null // Asegura que se envíe null si está vacío
    }

    if (props.grupo) {
      await actualizarGrupo(props.grupo.id_grupo, payload)
    } else {
      await crearGrupo(payload)
    }
    emit('actualizado')
    emit('cerrar')
  } catch (err) {
    alert(err.error || 'Error al guardar el grupo')
  } finally {
    loading.value = false
  }
}

// --- Lógica de Estilos (copiada de AreaCard) ---
const styleCatalog = {
  'Dirección':            { gradient: 'bg-gradient-to-br from-green-400 to-emerald-600', icon: 'fas fa-building' },
  'Administración':       { gradient: 'bg-gradient-to-br from-pink-400 to-pink-600',    icon: 'fas fa-briefcase' },
  'Docentes de Primaria': { gradient: 'bg-gradient-to-br from-orange-400 to-orange-600', icon: 'fas fa-chalkboard-teacher' },
  'Docentes de Secundaria': { gradient: 'bg-gradient-to-br from-violet-400 to-indigo-500',  icon: 'fas fa-user-graduate' },
  'Alumnos de Inicial':   { gradient: 'bg-gradient-to-br from-emerald-400 to-green-500', icon: 'fas fa-child' },
  'Alumnos de Primaria':  { gradient: 'bg-gradient-to-br from-blue-400 to-sky-500',     icon: 'fas fa-book-reader' },
  'Alumnos de Secundaria':{ gradient: 'bg-gradient-to-br from-purple-400 to-fuchsia-500', icon: 'fas fa-users' },
  'Tutoría y Psicología': { gradient: 'bg-gradient-to-br from-red-400 to-pink-500',   icon: 'fas fa-hands-helping' },
  'Mantenimiento y Limpieza': { gradient: 'bg-gradient-to-br from-gray-400 to-gray-700',   icon: 'fas fa-broom' },
  'Seguridad':            { gradient: 'bg-gradient-to-br from-cyan-400 to-cyan-600',     icon: 'fas fa-shield-alt' },
  'Biblioteca':           { gradient: 'bg-gradient-to-br from-yellow-400 to-amber-500', icon: 'fas fa-book' },
  'Laboratorio':          { gradient: 'bg-gradient-to-br from-green-400 to-lime-500',   icon: 'fas fa-flask' },
  'Coordinación Académica': { gradient: 'bg-gradient-to-br from-teal-400 to-cyan-500', icon: 'fas fa-graduation-cap' },
  'Servicio Médico':      { gradient: 'bg-gradient-to-br from-red-400 to-rose-500',     icon: 'fas fa-medkit' },
  'Juegos':               { gradient: 'bg-gradient-to-br from-indigo-400 to-purple-500', icon: 'fas fa-gamepad' },
  'Prueba':               { gradient: 'bg-gradient-to-br from-gray-400 to-gray-500',     icon: 'fas fa-vial' },
};
const defaultStyle = { gradient: 'bg-gradient-to-br from-slate-400 to-slate-600', icon: 'fas fa-layer-group' };
const getAreaStyle = (nombre) => styleCatalog[nombre] ?? defaultStyle;
</script>