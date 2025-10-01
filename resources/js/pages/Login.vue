<template>
  <div class="flex items-center justify-center min-h-screen bg-gradient-to-r from-blue-600 to-green-400">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl p-8">
      <!-- Título -->
      <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">
        Sistema de Registro de Asistencia
      </h2>

      <!-- Formulario -->
      <form @submit.prevent="login" class="space-y-5">
        <!-- Usuario -->
        <div>
          <label class="block text-gray-700 font-medium mb-1">Usuario</label>
          <input
            type="text"
            v-model="username"
            placeholder="Ingrese su usuario"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
            required
          />
        </div>

        <!-- Contraseña -->
        <div>
          <label class="block text-gray-700 font-medium mb-1">Contraseña</label>
          <input
            type="password"
            v-model="password"
            placeholder="Ingrese su contraseña"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
            required
          />
        </div>

        <!-- Botón -->
        <button
          type="button" 
          @click="$router.push('/home')"
          class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition font-semibold"
        >
          Ingresar
        </button>
      </form>
    </div>
  </div>
</template>

<script>
export default {
  name: "Login",
  data() {
    return {
      username: "",
      password: "",
    };
  },
  methods: {
    login() {
      if (this.username && this.password) {
        // simulación -> redirige al home
        this.$router.push("/home");
      } else {
        alert("Complete todos los campos");
      }
    },
  },
};
</script>
