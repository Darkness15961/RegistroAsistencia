<template>
  <div class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4"
       @click.self="onCerrar">
    <div class="rounded-3xl w-full max-w-lg shadow-2xl border px-8 sm:px-10 py-6 sm:py-8 max-h-[90vh] overflow-y-auto"
         :class="theme('card').value">
      
      <h2 class="text-xl font-bold mb-8 text-center" :class="theme('cardTitle').value">
        <i class="fas fa-user-plus mr-2"></i>
        {{ empleado ? 'Editar Personal' : 'Registrar Personal' }}
      </h2>

      <form @submit.prevent="guardar">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
          
          <!-- Grupo (Moved to top) -->
          <div class="sm:col-span-2">
            <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">
              Grupo
            </label>
            <!-- Mostrar como texto solo si es registro nuevo desde un grupo específico -->
            <div v-if="grupo && !empleado" class="w-full rounded-xl border px-4 py-3 bg-gray-100 dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 font-medium">
               {{ getNombreGrupo(grupo) }}
            </div>
            <!-- Mostrar como select si es edición o registro sin grupo preseleccionado -->
            <div v-else class="relative">
              <select v-model="form.id_grupo"
                      class="w-full rounded-xl appearance-none border px-4 py-3 pr-12 outline-none transition-colors"
                      :class="isDark ? 'bg-gray-800 border-gray-600 text-white focus:border-blue-500' : 'bg-white border-gray-200 text-gray-900 focus:border-blue-500'"
                      required>
                <option value="" :class="isDark ? 'bg-gray-800' : 'bg-white'">Seleccionar grupo</option>
                <option v-for="g in gruposDePersonal" 
                        :key="g.id_grupo" 
                        :value="g.id_grupo"
                        :class="isDark ? 'bg-gray-800 text-white' : 'bg-white text-gray-900'">
                  {{ getNombreGrupo(g) }} (Área: {{ g.area.nombre_area }})
                </option>
              </select>
              <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none" 
                   :class="isDark ? 'text-white' : 'text-gray-600'">
                <i class="fas fa-chevron-down text-xs"></i>
              </div>
            </div>
          </div>

          <!-- Area (New Field) -->
          <div class="sm:col-span-2">
            <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">
              Área
            </label>
            <div class="relative">
              <select v-model="form.id_area"
                      :disabled="isGrupoReal"
                      class="w-full rounded-xl appearance-none border px-4 py-3 pr-12 outline-none transition-colors disabled:opacity-70 disabled:cursor-not-allowed"
                      :class="isDark ? 'bg-gray-800 border-gray-600 text-white focus:border-blue-500' : 'bg-white border-gray-200 text-gray-900 focus:border-blue-500'"
                      required>
                <option value="" disabled>Seleccionar área</option>
                <option v-for="area in areas" 
                        :key="area.id_area" 
                        :value="area.id_area">
                  {{ area.nombre_area }}
                </option>
              </select>
              <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none" 
                   :class="isDark ? 'text-white' : 'text-gray-600'">
                <i class="fas fa-chevron-down text-xs"></i>
              </div>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium mb-2" :class="theme('cardSubtitle').value">
              Nombre Completo
            </label>
            <input v-model="form.nombre_completo" placeholder="Nombre completo"
                   class="w-full rounded-xl border px-4 py-3 outline-none transition-colors" 
                   :class="isDark ? 'bg-gray-800 border-gray-600 text-white placeholder-gray-500 focus:border-blue-500' : 'bg-white border-gray-200 text-gray-900 focus:border-blue-500'"
                   required />
          </div>

          <div>
            <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">
              DNI
            </label>
            <input v-model="form.dni" placeholder="DNI (8 dígitos)"
                   @input="validarDNI"
                   maxlength="8"
                   class="w-full rounded-xl border px-3 py-2 outline-none transition-colors" 
                   :class="isDark ? 'bg-gray-800 border-gray-600 text-white placeholder-gray-500 focus:border-blue-500' : 'bg-white border-gray-200 text-gray-900 focus:border-blue-500'"
                   required />
          </div>

          <div>
            <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">
              Correo Electrónico
            </label>
            <input v-model="form.correo" placeholder="Correo electrónico" type="email"
                   class="w-full rounded-xl border px-3 py-2 outline-none transition-colors" 
                   :class="isDark ? 'bg-gray-800 border-gray-600 text-white placeholder-gray-500 focus:border-blue-500' : 'bg-white border-gray-200 text-gray-900 focus:border-blue-500'"
                   required />
          </div>

          <div>
            <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">
              Teléfono
            </label>
            <input v-model="form.telefono" placeholder="Teléfono"
                   class="w-full rounded-xl border px-3 py-2 outline-none transition-colors" 
                   :class="isDark ? 'bg-gray-800 border-gray-600 text-white placeholder-gray-500 focus:border-blue-500' : 'bg-white border-gray-200 text-gray-900 focus:border-blue-500'"
                   />
          </div>

          <div>
            <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">
              Cargo
            </label>
            <div class="relative">
              <select v-model="form.cargo_grado"
                      class="w-full rounded-xl appearance-none border px-4 py-3 pr-12 outline-none transition-colors"
                      :class="isDark ? 'bg-gray-800 border-gray-600 text-white focus:border-blue-500' : 'bg-white border-gray-200 text-gray-900 focus:border-blue-500'"
                      required>
                <option value="" disabled>Seleccionar cargo</option>
                <option v-for="cargo in cargosDisponibles" :key="cargo" :value="cargo">
                  {{ cargo }}
                </option>
              </select>
              <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none" 
                   :class="isDark ? 'text-white' : 'text-gray-600'">
                <i class="fas fa-chevron-down text-xs"></i>
              </div>
            </div>
          </div>
        </div>

        <div class="mt-4">
          <p class="text-sm mb-2" :class="theme('cardSubtitle').value">Fotografía</p>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <p class="text-sm mb-2" :class="theme('cardSubtitle').value">Cámara</p>
              <div class="aspect-video rounded-2xl overflow-hidden border p-2" :class="theme('input').value">
                <video ref="videoEl" autoplay muted playsinline class="w-full h-full object-cover bg-black" v-show="cameraActive"></video>
                <div v-show="!cameraActive" class="w-full h-full flex items-center justify-center text-sm" :class="theme('cardSubtitle').value">
                  Cámara inactiva
                </div>
              </div>

              <div class="flex flex-wrap gap-2 mt-3">
                <button type="button" @click="toggleCamera" class="px-4 py-2 rounded-xl font-semibold flex-1" :class="theme('buttonSecondary').value">
                  <i :class="cameraActive ? 'fas fa-stop-circle' : 'fas fa-video'"></i>
                  {{ cameraActive ? 'Detener' : 'Activar' }}
                </button>

                <button type="button" @click="capturarFoto" :disabled="!cameraActive" class="px-4 py-2 rounded-xl font-semibold flex-1" :class="[theme('buttonPrimary').value, !cameraActive && 'opacity-50']">
                  <i class="fas fa-camera"></i>
                  Capturar
                </button>

                <!-- Botón Subir Foto -->
                <button type="button" @click="$refs.fileInput.click()" class="px-4 py-2 rounded-xl font-semibold flex-1" :class="theme('buttonSecondary').value">
                  <i class="fas fa-upload"></i>
                  Subir
                </button>
                <input ref="fileInput" type="file" accept="image/*" class="hidden" @change="handleFileUpload">
              </div>
            </div>

            <div>
              <p class="text-sm mb-2" :class="theme('cardSubtitle').value">Vista previa</p>
              <div class="rounded-2xl overflow-hidden border p-2 bg-gray-50/30 aspect-video flex items-center justify-center relative" :class="theme('input').value">
                <img v-if="previewDataUrl" :src="previewDataUrl" ref="previewImgEl" class="overlay-absolute" />
                <canvas ref="previewCanvasEl" class="overlay-absolute"></canvas>
                <div v-if="!previewDataUrl" class="text-sm" :class="theme('cardSubtitle').value">No hay foto</div>
              </div>
              
              <div class="flex flex-wrap gap-2 mt-3">
                 <button type="button" v-if="capturedImage && !currentDescriptor" @click="analizarRostro" :disabled="estadoIA.isLoading" class="px-4 py-2 rounded-xl font-semibold w-full" :class="theme('buttonPrimary').value">
                   <i v-if="!estadoIA.isLoading" class="fas fa-check"></i>
                   <i v-else class="fas fa-spinner animate-spin"></i>
                   Usar y Analizar Foto
                 </button>

                 <button v-if="capturedImage || currentDescriptor" type="button" @click="clearCapture" class="px-4 py-2 rounded-xl font-semibold w-full" :class="theme('buttonSecondary').value">
                   <i class="fas fa-trash"></i>
                   Eliminar Foto
                 </button>
              </div>
            </div>
          </div>
          
          <p v-if="estadoIA.message" class="mt-3 text-sm font-medium text-center" 
            :class="{
              'text-green-600 dark:text-green-400': estadoIA.isSuccess,
              'text-red-600 dark:text-red-400': !estadoIA.isSuccess,
              'text-gray-600 dark:text-gray-400': estadoIA.isLoading
            }">
            {{ estadoIA.message }}
          </p>
        </div>

        <div class="flex justify-end gap-3 pt-6">
          <button type="button" @click="onCerrar" class="px-5 py-2.5 rounded-xl font-semibold" :class="theme('buttonSecondary').value">
            Cancelar
          </button>
          <button type="submit" :disabled="loading" class="px-5 py-2.5 rounded-xl font-semibold shadow-lg" :class="[theme('buttonPrimary').value, loading && 'opacity-50']">
            <i v-if="loading" class="fas fa-spinner animate-spin mr-2"></i>
            {{ empleado ? 'Actualizar' : 'Guardar' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted, onUnmounted, computed, reactive } from 'vue'
import { useTheme } from '@/composables/useTheme'
import { usePersonal } from '../composables/usePersonal'
import api from '@/axiosConfig'
import * as faceapi from 'face-api.js'

const props = defineProps({ 
  empleado: Object,
  grupo: Object // Prop para el grupo preseleccionado
})
const emit = defineEmits(['cerrar', 'actualizado'])

const { theme, isDark } = useTheme()
const { crearPersonal, actualizarPersonal } = usePersonal()

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

const cargosDisponibles = [
  'Director',
  'Docente',
  'Administrativo',
  'Auxiliar',
  'Seguridad',
  'Limpieza',
  'Otro'
]

/* --------------------- Cámara y face-api --------------------- */
const videoEl = ref(null)
const previewImgEl = ref(null)
const previewCanvasEl = ref(null)

const cameraActive = ref(false)
const capturedImage = ref(null)
const previewDataUrl = ref('')
const currentDescriptor = ref(null)
let streamTracks = []
let modelosCargados = ref(false)

const estadoIA = reactive({ isLoading: false, isSuccess: false, message: '' })

const toggleCamera = async () => {
  if (cameraActive.value) { stopCamera(); return; }
  estadoIA.message = 'Cargando modelos...'; estadoIA.isLoading = true;
  try {
    await cargarModelos()
    await startCamera()
    estadoIA.message = 'Cámara activada.'
    estadoIA.isLoading = false; estadoIA.isSuccess = true;
  } catch (err) {
    console.error(err); estadoIA.message = 'Error cámara/modelos.';
    estadoIA.isLoading = false; estadoIA.isSuccess = false;
  }
}

const cargarModelos = async () => {
  if (modelosCargados.value) return
  const MODEL_URL = '/models'
  await Promise.all([
    faceapi.nets.ssdMobilenetv1.loadFromUri(MODEL_URL),
    faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL),
    faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL)
  ])
  modelosCargados.value = true
}

const startCamera = async () => {
  if (!videoEl.value) throw new Error('video element not ready')
  const stream = await navigator.mediaDevices.getUserMedia({ video: { width: 640, height: 480 } })
  videoEl.value.srcObject = stream
  streamTracks = stream.getTracks()
  cameraActive.value = true
}

const stopCamera = () => {
  try { streamTracks.forEach(t => t.stop()) } catch (e) {}
  streamTracks = []
  cameraActive.value = false
}

const onCerrar = () => { stopCamera(); emit('cerrar'); }
onUnmounted(() => { stopCamera() })

const capturarFoto = async () => {
  if (!videoEl.value) return
  clearCapture()
  const video = videoEl.value
  const canvas = document.createElement('canvas')
  canvas.width = video.videoWidth || 640
  canvas.height = video.videoHeight || 480
  const ctx = canvas.getContext('2d')
  ctx.drawImage(video, 0, 0, canvas.width, canvas.height)
  const dataUrl = canvas.toDataURL('image/jpeg', 0.9)
  previewDataUrl.value = dataUrl
  capturedImage.value = dataUrl
  estadoIA.message = 'Foto capturada.'
  estadoIA.isSuccess = true
}

const handleFileUpload = (event) => {
  const file = event.target.files[0]
  if (!file) return

  const reader = new FileReader()
  reader.onload = (e) => {
    const dataUrl = e.target.result
    clearCapture()
    previewDataUrl.value = dataUrl
    capturedImage.value = dataUrl
    estadoIA.message = 'Foto subida. Presione "Usar y Analizar Foto".'
    estadoIA.isSuccess = true
  }
  reader.readAsDataURL(file)
  event.target.value = ''
}

const analizarRostro = async () => {
  if (!previewImgEl.value) return
  estadoIA.isLoading = true; estadoIA.isSuccess = false; estadoIA.message = 'Cargando modelos y analizando...'
  try {
    await cargarModelos()
    await previewImgEl.value.decode()
    const detection = await faceapi.detectSingleFace(previewImgEl.value, new faceapi.SsdMobilenetv1Options()).withFaceLandmarks().withFaceDescriptor()
    if (!detection) {
      estadoIA.message = 'IA: No se detectó rostro.'; estadoIA.isLoading = false; return;
    }
    estadoIA.message = 'IA: ¡Rostro detectado!'; estadoIA.isLoading = false; estadoIA.isSuccess = true;
    currentDescriptor.value = Array.from(detection.descriptor)
    const canvas = previewCanvasEl.value
    const displaySize = { width: previewImgEl.value.width, height: previewImgEl.value.height }
    faceapi.matchDimensions(canvas, displaySize)
    const resized = faceapi.resizeResults(detection, displaySize)
    const ctx = canvas.getContext('2d')
    ctx.clearRect(0, 0, canvas.width, canvas.height)
    faceapi.draw.drawFaceLandmarks(canvas, resized)
  } catch (err) {
    console.error(err); estadoIA.message = 'Error análisis.'; estadoIA.isLoading = false;
  }
}

const clearCapture = () => {
  capturedImage.value = null; previewDataUrl.value = ''; currentDescriptor.value = null;
  estadoIA.message = ''; estadoIA.isLoading = false;
  const canvas = previewCanvasEl.value
  if (canvas) { const ctx = canvas.getContext('2d'); ctx.clearRect(0, 0, canvas.width, canvas.height) }
}

/* --------------------- Lógica Formulario --------------------- */
const validarDNI = (event) => {
  const value = event.target.value.replace(/\D/g, '').slice(0, 8)
  form.value.dni = value
  event.target.value = value
}

watch(() => props.empleado, (nuevo) => {
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
    if (nuevo.image_url) {
       previewDataUrl.value = nuevo.image_url
       capturedImage.value = nuevo.image_url
    }
    clearCapture()
  } else {
    // Nuevo registro
    form.value = { 
      nombre_completo: '', 
      dni: '', 
      correo: '', 
      telefono: '', 
      cargo_grado: '', 
      id_area: props.grupo ? props.grupo.id_area : '', // Pre-llenar área
      id_grupo: props.grupo ? props.grupo.id_grupo : '' // Pre-llenar grupo
    }
    clearCapture()
  }
}, { immediate: true })

onMounted(async () => {
  try {
    const [resAreas, resGrupos] = await Promise.all([ api.get('/areas'), api.get('/grupos') ])
    areas.value = resAreas.data
    grupos.value = resGrupos.data.data || resGrupos.data
  } catch (e) { console.error("Error carga modal", e) }
})

const gruposConArea = computed(() => grupos.value.map(g => ({ ...g, area: areas.value.find(a => a.id_area === g.id_area) || {} })))
const gruposDePersonal = computed(() => gruposConArea.value.filter(g => g.area && g.area.nombre_area && !String(g.area.nombre_area).toLowerCase().includes('alumno')))

const isGrupoReal = computed(() => {
  return form.value.id_grupo && form.value.id_grupo !== 'unassigned'
})

const getNombreGrupo = (grupo) => {
  if (!grupo) return ''
  if (grupo.grado || grupo.nivel) return `${grupo.nivel || ''} ${grupo.grado || ''} "${grupo.seccion || ''}"`.trim()
  return grupo.area?.nombre_area || 'Grupo'
}

const guardar = async () => {
  if (form.value.dni.length !== 8) {
    alert('El DNI debe tener exactamente 8 dígitos.')
    return
  }

  if (!currentDescriptor.value && !props.empleado) {
    alert('Debe tomar una foto y analizarla para registrar al personal.')
    return
  }

  loading.value = true
  try {
    const grupoSel = grupos.value.find(g => g.id_grupo === form.value.id_grupo)
    // Si seleccionó un grupo real, forzar el área del grupo. Si es 'unassigned' o null, respetar el área seleccionada manualmente.
    if (grupoSel) {
      form.value.id_area = grupoSel.id_area
    }
    
    // Si el grupo es 'unassigned', enviarlo como null
    if (form.value.id_grupo === 'unassigned') {
      form.value.id_grupo = null
    }

    let personaId = null
    if (props.empleado) {
      await actualizarPersonal(props.empleado.id_persona, form.value)
      personaId = props.empleado.id_persona
    } else {
      const personaCreada = await crearPersonal(form.value)
      personaId = personaCreada?.data?.id_persona || personaCreada?.id_persona
    }

    if (currentDescriptor.value) {
      await api.post('/reconocimientos', {
        id_persona: personaId,
        face_descriptor: currentDescriptor.value
        // No enviamos image_base64 porque no es necesario para el reconocimiento
      })
    }

    stopCamera(); emit('actualizado'); emit('cerrar')
  } catch (err) {
    const errData = err?.response?.data
    const msg = errData?.errors ? Object.values(errData.errors)[0][0] : (errData?.message || 'Error al guardar')
    alert(msg)
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.overlay-absolute { position: absolute; top: 0; left: 0; width: 100%; height: 100%; }
img.overlay-absolute { object-fit: cover; }
canvas.overlay-absolute { z-index: 10; }
</style>