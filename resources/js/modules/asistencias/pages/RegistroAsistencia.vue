<template>
  <div 
    class="w-full min-h-screen flex flex-col items-center justify-center p-4"
    :class="isDark ? 'bg-dark-gradient' : 'bg-light-gradient'"
  >
    
    <div 
      class="w-full max-w-6xl flex flex-col md:flex-row rounded-3xl shadow-2xl overflow-hidden" 
      :class="theme('card').value"
    >
      
      <div 
        class="w-full md:w-1/2 p-8 sm:p-12 flex flex-col justify-center items-center text-center"
        :class="isDark ? 'bg-gray-800' : 'bg-gradient-to-br from-blue-500 to-purple-600'"
      >
        <div class="w-20 h-20 bg-gradient-to-br from-blue-400 to-cyan-400 rounded-full flex items-center justify-center mb-6 shadow-lg">
          <img src="@/images/logo1.png" alt="Logo" class="w-12 h-12" />
        </div>
        
        <h1 class="text-3xl font-bold mb-4 text-white">
          Registro de Asistencia
        </h1>
        
        <div class="font-mono text-6xl font-extrabold text-white">
          {{ horaActual }}
        </div>
        
        <p class="text-xl mt-2 text-blue-100">
          {{ fechaActual }}
        </p>
      </div>
      
      <div class="w-full md:w-1/2 p-8 sm:p-12 flex flex-col justify-center items-center relative">
        
        <button 
          @click="toggleTheme"
          class="absolute top-6 right-6 z-20 w-10 h-10 flex items-center justify-center rounded-xl transition-all duration-200"
          :class="theme('headerButton').value"
          title="Cambiar tema"
        >
          <svg v-if="isDark" class="w-6 h-6 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd" /></svg>
          <svg v-else class="w-6 h-6 text-gray-700" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" /></svg>
        </button>
        
        <div 
          class="w-full aspect-video bg-black rounded-2xl flex items-center justify-center text-center p-4 border relative overflow-hidden mb-4"
          :class="theme('input').value"
        >
          <video ref="videoEl" autoplay muted playsinline class="w-full h-full object-cover" v-show="cameraActive"></video>
          
          <div v-show="!cameraActive" class="text-gray-500 flex flex-col items-center">
             <i class="fas fa-video-slash text-4xl mb-2"></i>
             <p class="mt-2 font-medium">Cámara desactivada</p>
             <p class="text-xs opacity-70">Active la cámara para registrar asistencia</p>
          </div>

          <div 
            v-if="cameraActive"
            class="absolute inset-0 flex flex-col items-center justify-center p-4 transition-all duration-300"
            :class="[
              estado.isRecognized ? 'bg-green-500/80' : 'bg-black/50',
              estado.message ? 'opacity-100' : 'opacity-0'
            ]"
          >
            <div :class="theme('cardSubtitle').value" class="text-white text-center">
              <i v-if="estado.isRecognized" class="fas fa-check-circle text-5xl mb-4"></i>
              <i v-else class="fas fa-spinner fa-spin text-5xl mb-4"></i>
              <p class="font-medium text-lg whitespace-pre-line">{{ estado.message }}</p>
            </div>
          </div>
        </div>
        
        <div class="flex gap-4 w-full justify-center mb-6">
           <button 
             @click="toggleCamera"
             :disabled="!modelosCargados"
             class="px-8 py-3 rounded-xl font-bold transition-all shadow-lg flex items-center gap-2 text-lg transform hover:scale-105 active:scale-95"
             :class="[
               !modelosCargados ? 'opacity-50 cursor-not-allowed bg-gray-400 text-gray-700' :
               cameraActive ? 'bg-red-500 hover:bg-red-600 text-white ring-2 ring-red-300' : 
                              'bg-blue-600 hover:bg-blue-700 text-white ring-2 ring-blue-300'
             ]"
           >
             <i :class="cameraActive ? 'fas fa-power-off' : 'fas fa-video'"></i>
             {{ cameraActive ? 'Desactivar Cámara' : 'Activar Cámara' }}
           </button>
        </div>
        
        <div class="text-center" v-if="cameraActive">
          <p class="text-sm" :class="theme('cardSubtitle').value">
            Mire fijamente a la cámara.
          </p>
        </div>
        
         <router-link 
            to="/login"
            class="text-sm font-medium mt-6 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white"
          >
            Ir a Iniciar Sesión <i class="fas fa-arrow-right ml-1"></i>
          </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, reactive } from 'vue'
import { useTheme } from '@/composables/useTheme'
import * as faceapi from 'face-api.js'
import axios from '@/axiosConfig.js'

/* ------------------------------- UI ------------------------------- */
const { theme, isDark, toggleTheme } = useTheme()

const horaActual = ref('')
const fechaActual = ref('')
let timerId = null

const actualizarHora = () => {
  const now = new Date()
  horaActual.value = now.toLocaleTimeString('es-PE', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true })
  fechaActual.value = now.toLocaleDateString('es-PE', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })
}

/* --------------------------- Face Recognition --------------------------- */
const videoEl = ref(null)
let detectionInterval = null
const processing = ref(false)
const cameraActive = ref(false)
const modelosCargados = ref(false)
let stream = null

const estado = reactive({
  message: 'Cargando modelos...',
  isRecognized: false
})

/* ---------------------------- Cargar Modelos ---------------------------- */
const cargarModelos = async () => {
  estado.message = 'Cargando modelos de IA...'
  const MODEL_URL = '/models'

  try {
    await Promise.all([
      faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),
      faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL),
      faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL)
    ])
    modelosCargados.value = true
    estado.message = 'Modelos listos. Inicie la cámara.'
  } catch (error) {
    console.error("Error cargando modelos:", error)
    estado.message = "Error al cargar IA."
  }
}

/* ---------------------------- Control de Cámara (Toggle) ---------------------------- */
const toggleCamera = () => {
  if (cameraActive.value) {
    detenerCamara()
  } else {
    iniciarCamara()
  }
}

const iniciarCamara = async () => {
  if (!modelosCargados.value) return
  
  estado.message = 'Abriendo cámara...'
  try {
    stream = await navigator.mediaDevices.getUserMedia({ 
      video: { width: 640, height: 480 } 
    })
    videoEl.value.srcObject = stream
    cameraActive.value = true
    
    // Iniciar detección cuando el video empiece a reproducirse
    videoEl.value.onplaying = async () => {
        const matcher = await cargarDescriptores()
        if (matcher) {
           iniciarDeteccion(matcher)
        } else {
          // Si falla la carga de descriptores, detenemos para no dejar la cámara en el limbo
          detenerCamara()
          alert("No se pudieron cargar los datos faciales. Revise si hay usuarios registrados.")
        }
    }

  } catch (error) {
    console.error("Error cámara:", error)
    estado.message = "No se pudo acceder a la cámara."
  }
}

const detenerCamara = () => {
  if (stream) {
    stream.getTracks().forEach(track => track.stop())
    stream = null
  }
  if (videoEl.value) {
    videoEl.value.srcObject = null
  }
  
  clearInterval(detectionInterval)
  cameraActive.value = false
  estado.message = "Cámara detenida."
}


/* ------------------------ Cargar Descriptores (BLINDADO) ------------------------ */
const cargarDescriptores = async () => {
  estado.message = 'Cargando rostros...'

  try {
    const response = await axios.get('/reconocimientos/descriptores')
    const data = response.data.data || response.data

    if (!data || data.length === 0) {
      estado.message = 'No hay rostros registrados en el sistema.'
      return null
    }

    const labeled = []
    
    data.forEach(rec => {
      try {
        // Aseguramos que sea un array
        const descriptorArray = typeof rec.face_descriptor === 'string' 
             ? JSON.parse(rec.face_descriptor) 
             : rec.face_descriptor

        const descriptorFloat = new Float32Array(descriptorArray)

        // Validación de longitud exacta para evitar el error de euclideanDistance
        if (descriptorFloat.length === 128) {
          labeled.push(new faceapi.LabeledFaceDescriptors(
            String(rec.id_persona),
            [descriptorFloat]
          ))
        }
      } catch (e) {
        console.error(`Error procesando rostro ID ${rec.id_persona}`, e)
      }
    })

    if (labeled.length === 0) {
       estado.message = "Error: Datos de rostros inválidos en BD."
       return null
    }

    estado.message = 'Escaneando...'
    return new faceapi.FaceMatcher(labeled, 0.5)

  } catch (error) {
    console.error("Error al cargar descriptores:", error)
    estado.message = "Error de conexión."
    return null
  }
}

/* ------------------------ Registrar Asistencia ------------------------ */
const registrarAsistencia = async (idPersona) => {
  if (processing.value) return
  processing.value = true

  try {
    const response = await axios.post('/asistencias/registrar', {
      id_persona: idPersona
    })

    estado.message = `¡Bienvenido, ${response.data.persona.nombre_completo}!\nEstado: ${response.data.estado_asistencia}`
    estado.isRecognized = true

    setTimeout(() => {
      estado.message = 'Escaneando...'
      estado.isRecognized = false
      processing.value = false
    }, 4000)

  } catch (e) {
    console.error("Error registro:", e)
    estado.message = 'Registro procesado.' 
    setTimeout(() => {
      estado.message = 'Escaneando...'
      estado.isRecognized = false
      processing.value = false
    }, 3000)
  }
}

/* ----------------------------- Inicio detección ----------------------------- */
const iniciarDeteccion = (matcher) => {
  if (!matcher) return

  clearInterval(detectionInterval)

  detectionInterval = setInterval(async () => {
    if (!videoEl.value || videoEl.value.paused || !cameraActive.value || processing.value) return

    try {
        const detection = await faceapi.detectSingleFace(
          videoEl.value,
          new faceapi.TinyFaceDetectorOptions()
        ).withFaceLandmarks().withFaceDescriptor()

        if (detection) {
          const bestMatch = matcher.findBestMatch(detection.descriptor)

          if (bestMatch.label !== 'unknown') {
            registrarAsistencia(bestMatch.label)
          } else {
            if (!estado.isRecognized) estado.message = 'Rostro detectado.\nNo reconocido.'
          }
        } else {
           if (!estado.isRecognized) estado.message = 'Escaneando...'
        }
    } catch (err) {
        console.error("Error en detección loop:", err)
    }
  }, 1000)
}

/* ------------------------------- Lifecycle ------------------------------- */
onMounted(async () => {
  actualizarHora()
  timerId = setInterval(actualizarHora, 1000)
  await cargarModelos()
  // La cámara NO inicia automáticamente, espera al botón
})

onUnmounted(() => {
  clearInterval(timerId)
  detenerCamara()
})
</script>