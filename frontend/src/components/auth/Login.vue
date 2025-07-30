<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50">
    <div class="w-full max-w-md space-y-6 p-8 bg-white rounded-lg shadow">
      <h2 class="text-2xl font-bold text-center">Log In</h2>
      <form @submit.prevent="onSubmit" class="space-y-4">
        <div v-if="error" class="bg-red-100 text-red-700 p-2 rounded text-sm">{{ error }}</div>
        <input v-model="email" type="email" placeholder="Email" required
               class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-primary-500"/>
        <input v-model="password" type="password" placeholder="Password" required
               class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-primary-500"/>
        <button type="submit"
                class="w-full py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
          Log In
        </button>
      </form>
      <p class="text-center text-sm">
        Don’t have an account?
        <router-link to="/register" class="text-primary-600 hover:underline">Sign up</router-link>
      </p>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useToast } from "vue-toastification"

import { login } from '../../composables/useAuth'

export default defineComponent({
  setup() {
    const email = ref(''), password = ref('')
    const error = ref('')
    const router = useRouter()
    const toast = useToast()

    const onSubmit = async () => {
      error.value = ''
      try {
        await login(email.value, password.value)
        router.push('/')
        toast.success("Login successful!", {
          timeout: 2000
        })
      } catch (err: any) {
        if (err.response && err.response.data && err.response.data.message) {
          error.value = err.response.data.message
        } else if (err.response && err.response.data && err.response.data.errors) {
          error.value = Object.values(err.response.data.errors).flat().join(' ')
        } else {
          error.value = 'Login failed. Please try again.'
        }
      }
    }

    return { email, password, onSubmit, error }
  }
})
</script>
