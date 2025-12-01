import { ref, computed } from 'vue'
import api from '@/axiosConfig'

const usuario = ref(null)
const persona = ref(null)
const loaded = ref(false)

export function useAuth() {
    const isAdmin = computed(() => usuario.value?.rol === 'admin')
    const isSupervisor = computed(() => usuario.value?.rol === 'supervisor')
    const isDocente = computed(() => usuario.value?.rol === 'docente')
    const isSecretaria = computed(() => usuario.value?.rol === 'secretaria')

    const canViewPersonal = computed(() =>
        isAdmin.value || isSupervisor.value || isSecretaria.value
    )

    const canViewStudents = computed(() =>
        isAdmin.value || isDocente.value || isSecretaria.value
    )

    const canViewExitTimes = computed(() =>
        isAdmin.value || isSupervisor.value
    )

    const fetchUsuarioActual = async (force = false) => {
        if (loaded.value && !force) return

        try {
            const res = await api.get('/usuario-actual')
            usuario.value = res.data.usuario
            persona.value = res.data.persona
            loaded.value = true
        } catch (err) {
            console.error('Error cargando usuario:', err)
        }
    }

    return {
        usuario,
        persona,
        isAdmin,
        isSupervisor,
        isDocente,
        isSecretaria,
        canViewPersonal,
        canViewStudents,
        canViewExitTimes,
        fetchUsuarioActual
    }
}
