import { reactive } from 'vue'

// Este estado reactivo será compartido
// por el Header.vue y cualquier página que lo necesite.
const pageInfo = reactive({
  title: 'Dashboard',
  subtitle: 'Bienvenido a tu panel',
  showButton: false,
  buttonText: 'Acción',
  buttonIcon: 'fa-plus',
  buttonAction: () => {}
})

export function usePageHeader() {
  // Esta función permitirá a cualquier página (ej. Areas.vue)
  // actualizar la información del header.
  const setPageHeader = (config) => {
    pageInfo.title = config.title || 'Dashboard'
    pageInfo.subtitle = config.subtitle || ''
    pageInfo.showButton = config.showButton || false
    pageInfo.buttonText = config.buttonText || 'Acción'
    pageInfo.buttonIcon = config.buttonIcon || 'fa-plus'
    pageInfo.buttonAction = config.buttonAction || (() => {})
  }

  return {
    pageInfo,
    setPageHeader
  }
}