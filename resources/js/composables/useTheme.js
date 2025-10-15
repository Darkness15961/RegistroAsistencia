import { ref, computed } from 'vue'

const isDark = ref(true) // true = dark mode por defecto

export function useTheme() {
  const toggleTheme = () => {
    isDark.value = !isDark.value
  }

  const theme = (component) => {
    const themes = {
      // Backgrounds
      background: computed(() => 
        isDark.value 
          ? 'bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900' 
          : 'bg-gradient-to-br from-blue-50 via-white to-purple-50'
      ),
      
      // Cards
      card: computed(() => 
        isDark.value 
          ? 'bg-white/10 backdrop-blur-xl border-white/20' 
          : 'bg-white border-gray-200'
      ),
      
      cardTitle: computed(() => 
        isDark.value 
          ? 'text-white' 
          : 'text-gray-900'
      ),
      
      cardSubtitle: computed(() => 
        isDark.value 
          ? 'text-gray-300' 
          : 'text-gray-600'
      ),
      
      // Inputs
      input: computed(() => 
        isDark.value 
          ? 'bg-white/5 border-white/20 text-white placeholder-gray-400 focus:border-purple-400' 
          : 'bg-white border-gray-300 text-gray-900 placeholder-gray-500 focus:border-blue-500'
      ),
      
      // Buttons
      buttonPrimary: computed(() => 
        isDark.value 
          ? 'bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white' 
          : 'bg-gradient-to-r from-blue-500 to-purple-500 hover:from-blue-600 hover:to-purple-600 text-white'
      ),
      
      buttonSecondary: computed(() => 
        isDark.value 
          ? 'bg-white/10 hover:bg-white/20 border-white/20 text-white' 
          : 'bg-gray-100 hover:bg-gray-200 border-gray-300 text-gray-900'
      ),
      
      // Table
      tableHeader: computed(() => 
        isDark.value 
          ? 'bg-white/5' 
          : 'bg-gray-50'
      ),
      
      tableRow: computed(() => 
        isDark.value 
          ? 'hover:bg-white/5' 
          : 'hover:bg-gray-50'
      ),
      
      // Sidebar
      sidebar: computed(() => 
        isDark.value 
          ? 'bg-gray-900/95 backdrop-blur-xl border-white/10' 
          : 'bg-white border-gray-200'
      ),
      
      sidebarItem: computed(() => 
        isDark.value 
          ? 'text-gray-300 hover:bg-white/10 hover:text-white' 
          : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900'
      ),
      
      sidebarItemActive: computed(() => 
        isDark.value 
          ? 'bg-gradient-to-r from-purple-500/20 to-pink-500/20 text-white border-l-4 border-purple-500' 
          : 'bg-gradient-to-r from-blue-50 to-purple-50 text-blue-600 border-l-4 border-blue-500'
      ),
      
      // Header
      header: computed(() => 
        isDark.value 
          ? 'bg-gray-900/80 backdrop-blur-xl border-white/10' 
          : 'bg-white/80 backdrop-blur-xl border-gray-200'
      ),
      
      headerButton: computed(() => 
        isDark.value 
          ? 'bg-white/10 hover:bg-white/20 text-white' 
          : 'bg-gray-100 hover:bg-gray-200 text-gray-700'
      ),
      
      headerBreadcrumb: computed(() => 
        isDark.value 
          ? 'text-gray-400' 
          : 'text-gray-500'
      ),
      
      headerBreadcrumbSeparator: computed(() => 
        isDark.value 
          ? 'text-gray-600' 
          : 'text-gray-400'
      ),
      
      headerUserInfo: computed(() => 
        isDark.value 
          ? 'bg-white/5 hover:bg-white/10' 
          : 'bg-gray-100 hover:bg-gray-200'
      ),
      
      headerUserRole: computed(() => 
        isDark.value 
          ? 'text-gray-400' 
          : 'text-gray-500'
      ),
    }

    return themes[component] || computed(() => '')
  }

  return {
    isDark,
    toggleTheme,
    theme
  }
}