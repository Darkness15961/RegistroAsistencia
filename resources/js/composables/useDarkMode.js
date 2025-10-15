import { ref, watch } from 'vue'

// Estado global del tema (compartido entre todos los componentes)
const isDark = ref(true) // Por defecto: modo oscuro

export function useDarkMode() {
  // Cargar preferencia guardada al iniciar
  if (typeof window !== 'undefined') {
    const saved = localStorage.getItem('darkMode')
    if (saved !== null) {
      isDark.value = saved === 'true'
    }
  }

  // Función para cambiar el tema
  const toggleDark = () => {
    isDark.value = !isDark.value
  }

  // Guardar en localStorage cuando cambie
  watch(isDark, (newValue) => {
    if (typeof window !== 'undefined') {
      localStorage.setItem('darkMode', String(newValue))
      console.log('Tema cambiado a:', newValue ? 'oscuro' : 'claro')
    }
  })

  return {
    isDark,
    toggleDark
  }
}