import CardStats from './components/CardStats.vue'
import Footer from './components/Footer.vue'
import FormInput from './components/FormInput.vue'
import Header from './components/Header.vue'
import Sidebar from './components/Sidebar.vue'
import Table from './components/Table.vue'

export default {
  install(app) {
    app.component('CardStats', CardStats)
    app.component('Footer', Footer)
    app.component('FormInput', FormInput)
    app.component('Header', Header)
    app.component('Sidebar', Sidebar)
    app.component('Table', Table)
  }
}

export {
  CardStats,
  Footer,
  FormInput,
  Header,
  Sidebar,
  Table
}