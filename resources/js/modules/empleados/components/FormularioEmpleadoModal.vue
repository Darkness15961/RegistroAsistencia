<template>
  <div class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4"
       @click.self="onCerrar">
    <div class="rounded-3xl w-full max-w-lg shadow-2xl border p-6"
         :class="theme('card').value">
      
      <h2 class="text-xl font-bold mb-6" :class="theme('cardTitle').value">
        <i class="fas fa-user-plus mr-2"></i>
        {{ empleado ? 'Editar Empleado' : 'Registrar Empleado' }}
      </h2>

      <form @submit.prevent="guardar">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">
              Nombre Completo
            </label>
            <input v-model="form.nombre_completo" placeholder="Nombre completo"
                   class="w-full rounded-xl border px-3 py-2 outline-none transition-colors" 
                   :class="isDark ? 'bg-gray-800 border-gray-600 text-white placeholder-gray-500 focus:border-blue-500' : 'bg-white border-gray-200 text-gray-900 focus:border-blue-500'"
                   required />
          </div>

          <div>
            <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">
              DNI
            </label>
            <input v-model="form.dni" placeholder="DNI"
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
            <input v-model="form.cargo_grado" placeholder="Cargo o puesto"
                   class="w-full rounded-xl border px-3 py-2 outline-none transition-colors" 
                   :class="isDark ? 'bg-gray-800 border-gray-600 text-white placeholder-gray-500 focus:border-blue-500' : 'bg-white border-gray-200 text-gray-900 focus:border-blue-500'"
                   />
          </div>

          <div>
            <label class="block text-sm font-medium mb-1.5" :class="theme('cardSubtitle').value">
              Grupo
            </label>
            <div class="relative">
              <select v-model="form.id_grupo"
                      class="w-full rounded-xl appearance-none border px-3 py-2 pr-8 outline-none transition-colors"
                      :class="isDark ? 'bg-gray-800 border-gray-600 text-white focus:border-blue-500' : 'bg-white border-gray-200 text-gray-900 focus:border-blue-500'"
                      required>
                <option value="" :class="isDark ? 'bg-gray-800' : 'bg-white'">Seleccionar grupo</option>
                <option v-for="grupo in gruposDeEmpleados" 
                        :key="grupo.id_grupo" 
                        :value="grupo.id_grupo"
                        :class="isDark ? 'bg-gray-800 text-white' : 'bg-white text-gray-900'">
                  {{ getNombreGrupo(grupo) }} (Área: {{ grupo.area.nombre_area }})
                </option>
              </select>
              <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none" 
                   :class="isDark ? 'text-white' : 'text-gray-600'">
                <i class="fas fa-chevron-down text-xs"></i>
              </div>
            </div>
          </div>
        </div>

        <div class="mt-4">
          <p class="text-sm mb-2" :class="theme('cardSubtitle').value">Fotografía para reconocimiento facial</p>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
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
              </div>
            </div>

            <div>
              <p class="text-sm mb-2" :class="theme('cardSubtitle').value">Vista previa</p>
              <div class="rounded-2xl overflow-hidden border p-2 bg-gray-50/30 aspect-video flex items-center justify-center relative" :class="theme('input').value">
                <img v-if="previewDataUrl" :src="previewDataUrl" ref="previewImgEl" class="absolute inset-0 w-full h-full object-cover" />
                
                <canvas ref="previewCanvasEl" class="absolute inset-0 w-full h-full"></canvas>
                
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
import { useEmpleados } from '../composables/useEmpleados'
import api from '@/axiosConfig'
import * as faceapi from 'face-api.js'

/* Props / Emits */
const props = defineProps({ empleado: Object })
const emit = defineEmits(['cerrar', 'actualizado'])

/* Theme */
const { theme, isDark } = useTheme()

/* Composable Empleados */
const { crearEmpleado, actualizarEmpleado } = useEmpleados()

/* Estado formulario */
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

/* --------------------- Cámara y face-api (Lógica Idéntica) --------------------- */
const videoEl = ref(null)
const previewImgEl = ref(null)
const previewCanvasEl = ref(null)

const cameraActive = ref(false)
const capturedImage = ref(null)
const previewDataUrl = ref('')
const currentDescriptor = ref(null)
let streamTracks = []
let modelosCargados = ref(false)

const estadoIA = reactive({
  isLoading: false,
  isSuccess: false,
  message: ''
})


const toggleCamera = async () => {
  if (cameraActive.value) {
    stopCamera()
    return
  }
  estadoIA.message = 'Cargando modelos y cámara...'
  estadoIA.isLoading = true
  try {
    await cargarModelos()
    await startCamera()
    estadoIA.message = 'Cámara activada.'
    estadoIA.isLoading = false
    estadoIA.isSuccess = true
  } catch (err) {
    console.error(err)
    estadoIA.message = 'No se pudo activar la cámara. Revise permisos.'
    estadoIA.isLoading = false
    estadoIA.isSuccess = false
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
  try {
    streamTracks.forEach(t => t.stop())
  } catch (e) { /* ignore */ }
  streamTracks = []
  cameraActive.value = false
}

const onCerrar = () => {
  stopCamera()
  emit('cerrar')
}

onUnmounted(() => {
  stopCamera()
})

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
  estadoIA.message = 'Foto capturada. Presione "Usar y Analizar Foto".'
  estadoIA.isSuccess = true
}

const analizarRostro = async () => {
  if (!previewImgEl.value) return
  
  estadoIA.isLoading = true
  estadoIA.isSuccess = false
  estadoIA.message = 'Analizando rostro...'

  try {
    await previewImgEl.value.decode()
    const detection = await faceapi.detectSingleFace(previewImgEl.value, new faceapi.SsdMobilenetv1Options())
                                .withFaceLandmarks()
                                .withFaceDescriptor()

    if (!detection) {
      estadoIA.message = 'IA: No se detectó ningún rostro. Intente otra captura.'
      estadoIA.isLoading = false
      estadoIA.isSuccess = false
      currentDescriptor.value = null
      return
    }

    estadoIA.message = 'IA: ¡Rostro detectado exitosamente!'
    estadoIA.isLoading = false
    estadoIA.isSuccess = true
    currentDescriptor.value = Array.from(detection.descriptor) 

    const canvas = previewCanvasEl.value
    const displaySize = { width: previewImgEl.value.width, height: previewImgEl.value.height }
    faceapi.matchDimensions(canvas, displaySize)
    
    const resizedDetection = faceapi.resizeResults(detection, displaySize)
    const ctx = canvas.getContext('2d')
    ctx.clearRect(0, 0, canvas.width, canvas.height)
    faceapi.draw.drawFaceLandmarks(canvas, resizedDetection)

  } catch (err) {
    console.error(err)
    estadoIA.message = 'Error durante el análisis facial.'
    estadoIA.isLoading = false
    estadoIA.isSuccess = false
    currentDescriptor.value = null
  }
}

const clearCapture = () => {
  capturedImage.value = null
  previewDataUrl.value = ''
  currentDescriptor.value = null
  estadoIA.message = ''
  estadoIA.isLoading = false
  
  const canvas = previewCanvasEl.value
  if (canvas) {
    const ctx = canvas.getContext('2d')
    ctx.clearRect(0, 0, canvas.width, canvas.height)
  }
}

/* --------------------- Lógica de Carga y Guardado (Formulario) --------------------- */

/* Sync props -> form */
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
    form.value = { nombre_completo: '', dni: '', correo: '', telefono: '', cargo_grado: '', id_area: '', id_grupo: '' }
    clearCapture()
  }
}, { immediate: true })

/* Cargar areas y grupos */
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

/* Helper para nombres de grupo */
const gruposConArea = computed(() => {
  return grupos.value.map(g => ({
    ...g,
    area: areas.value.find(a => a.id_area === g.id_area) || {}
  }))
})

// ======================================================
// LÓGICA DE FILTRADO (Misma que en Alumnos pero para empleados)
// ======================================================
const gruposDeEmpleados = computed(() => {
  return gruposConArea.value.filter(g =>
    // Usamos 'nombre_area' que es el campo correcto en tu BD
    g.area && g.area.nombre_area && !String(g.area.nombre_area).toLowerCase().includes('alumno')
  )
})
// ======================================================

const getNombreGrupo = (grupo) => {
  if (!grupo) return ''
  if (grupo.grado || grupo.nivel) {
    return `${grupo.nivel || ''} ${grupo.grado || ''} "${grupo.seccion || ''}"`.trim()
  }
  return grupo.area?.nombre_area || 'Grupo'
}


/* --------------------- Guardado (Lógica Principal) --------------------- */
const guardar = async () => {
  loading.value = true

  try {
    // 1. Validar y setear id_area
    const grupoSeleccionado = grupos.value.find(g => g.id_grupo === form.value.id_grupo)
    if (grupoSeleccionado) {
      form.value.id_area = grupoSeleccionado.id_area
    } else {
      alert("Error: No se encontró el área para el grupo seleccionado.")
      loading.value = false
      return
    }

    // NOTA: Asumimos que 'useEmpleados.js' añade 'tipo_persona: "empleado"'
    
    // 2. Crear o Actualizar la Persona (Empleado)
    let personaId = null
    if (props.empleado) {
      // Actualizar
      await actualizarEmpleado(props.empleado.id_persona, form.value)
      personaId = props.empleado.id_persona
    } else {
      // Crear nuevo
      const personaCreada = await crearEmpleado(form.value)
      personaId = personaCreada?.data?.id_persona || personaCreada?.id_persona
    }

    if (!personaId) {
      throw new Error("No se pudo obtener el ID de la persona.")
    }

    // 3. Registrar el Rostro (SI FUE DETECTADO)
    if (currentDescriptor.value) {
      
      const payload = {
        id_persona: personaId,
        face_descriptor: currentDescriptor.value, 
        image_base64: capturedImage.value      
      }
      
      await api.post('/reconocimientos', payload)
    
    } else if (capturedImage.value && !currentDescriptor.value && !props.empleado) {
      alert("Advertencia: Se guardó el empleado, pero no se analizó ni guardó el rostro. Debe presionar 'Usar y Analizar Foto' antes de guardar.")
    }

    // 4. Terminar
    stopCamera()
    emit('actualizado')
    emit('cerrar')
    
  } catch (err) {
    console.error("Error guardando empleado o reconocimiento:", err)
    
    const errorData = err?.response?.data
    if (errorData && errorData.errors) {
        const primerError = Object.values(errorData.errors)[0][0]
        alert(`Error de Validación: ${primerError}`)
    } else {
        alert(errorData?.message || err?.message || 'Error al guardar empleado')
    }

  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
/* Ajustes para el canvas sobre la imagen */
.relative { position: relative; }
.absolute { position: absolute; top: 0; left: 0; width: 100%; height: 100%; }
img.absolute { object-fit: cover; }
canvas.absolute { z-index: 10; }
</style>