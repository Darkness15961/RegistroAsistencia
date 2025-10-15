// Definiciones centralizadas de estilos por tema
export const themes = {
  // Componentes de layout
  sidebar: {
    dark: 'bg-white/5 backdrop-blur-xl border-white/10 text-white',
    light: 'bg-white border-gray-200 text-gray-900 shadow-xl'
  },
  
  sidebarBorder: {
    dark: 'border-b border-white/10',
    light: 'border-b border-gray-200'
  },
  
  sidebarItem: {
    dark: 'hover:bg-white/10',
    light: 'hover:bg-gray-100'
  },
  
  sidebarItemActive: {
    dark: 'bg-white/10',
    light: 'bg-blue-50 text-blue-600'
  },
  
  header: {
    dark: 'text-white',
    light: 'text-gray-900 bg-white/80 backdrop-blur-sm border-b border-gray-200'
  },
  
  headerButton: {
    dark: 'hover:bg-white/10',
    light: 'hover:bg-gray-100'
  },
  
  headerBreadcrumb: {
    dark: 'text-gray-400',
    light: 'text-gray-500'
  },
  
  headerBreadcrumbSeparator: {
    dark: 'text-gray-600',
    light: 'text-gray-400'
  },
  
  headerUserInfo: {
    dark: 'hover:bg-white/5',
    light: 'hover:bg-gray-100'
  },
  
  headerUserRole: {
    dark: 'text-gray-400',
    light: 'text-gray-500'
  },
  
  // Componentes de contenido
  card: {
    dark: 'bg-white/5 backdrop-blur-sm border border-white/10',
    light: 'bg-white border border-gray-200 shadow-md'
  },
  
  cardTitle: {
    dark: 'text-white',
    light: 'text-gray-900'
  },
  
  cardSubtitle: {
    dark: 'text-gray-300',
    light: 'text-gray-600'
  },
  
  // Stats cards
  statsCard: {
    dark: 'bg-white/5 backdrop-blur-sm border border-white/10',
    light: 'bg-white border border-gray-200 shadow-sm'
  },
  
  statsValue: {
    dark: 'text-white',
    light: 'text-gray-900'
  },
  
  statsLabel: {
    dark: 'text-gray-300',
    light: 'text-gray-600'
  },
  
  // Tablas
  table: {
    dark: 'bg-white/5 backdrop-blur-sm',
    light: 'bg-white shadow-md'
  },
  
  tableHeader: {
    dark: 'bg-white/5 border-b border-white/10',
    light: 'bg-gray-50 border-b border-gray-200'
  },
  
  tableHeaderText: {
    dark: 'text-gray-300',
    light: 'text-gray-700'
  },
  
  tableRow: {
    dark: 'border-b border-white/10 hover:bg-white/5',
    light: 'border-b border-gray-200 hover:bg-gray-50'
  },
  
  tableCell: {
    dark: 'text-white',
    light: 'text-gray-900'
  },
  
  tableCellSecondary: {
    dark: 'text-gray-400',
    light: 'text-gray-600'
  },
  
  // Inputs y formularios
  input: {
    dark: 'bg-white/5 border-white/10 text-white placeholder-gray-400 focus:border-purple-500',
    light: 'bg-white border-gray-300 text-gray-900 placeholder-gray-400 focus:border-blue-500'
  },
  
  label: {
    dark: 'text-gray-300',
    light: 'text-gray-700'
  },
  
  // Botones
  buttonPrimary: {
    dark: 'bg-purple-600 hover:bg-purple-700 text-white',
    light: 'bg-blue-600 hover:bg-blue-700 text-white'
  },
  
  buttonSecondary: {
    dark: 'bg-white/10 hover:bg-white/20 text-white',
    light: 'bg-gray-200 hover:bg-gray-300 text-gray-900'
  },
  
  buttonDanger: {
    dark: 'bg-red-600 hover:bg-red-700 text-white',
    light: 'bg-red-600 hover:bg-red-700 text-white'
  },
  
  // Badges/Pills
  badgeSuccess: {
    dark: 'bg-green-500/20 text-green-400 border border-green-500/30',
    light: 'bg-green-100 text-green-700 border border-green-200'
  },
  
  badgeWarning: {
    dark: 'bg-yellow-500/20 text-yellow-400 border border-yellow-500/30',
    light: 'bg-yellow-100 text-yellow-700 border border-yellow-200'
  },
  
  badgeDanger: {
    dark: 'bg-red-500/20 text-red-400 border border-red-500/30',
    light: 'bg-red-100 text-red-700 border border-red-200'
  },
  
  badgeInfo: {
    dark: 'bg-blue-500/20 text-blue-400 border border-blue-500/30',
    light: 'bg-blue-100 text-blue-700 border border-blue-200'
  },
  
  // Modales
  modal: {
    dark: 'bg-gray-900/95 backdrop-blur-sm',
    light: 'bg-white'
  },
  
  modalOverlay: {
    dark: 'bg-black/70',
    light: 'bg-black/50'
  },
  
  // Dividers
  divider: {
    dark: 'border-white/10',
    light: 'border-gray-200'
  }
}

// Función helper para obtener clases según tema
export function getThemeClass(component, isDark) {
  return themes[component]?.[isDark ? 'dark' : 'light'] || ''
}