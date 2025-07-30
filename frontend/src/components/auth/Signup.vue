<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50">
    <div class="w-full max-w-md space-y-6 p-8 bg-white rounded-lg shadow">
      <h2 class="text-2xl font-bold text-center">Sign Up</h2>
      <form @submit.prevent="onSubmit" class="space-y-4">
        <div v-if="error" class="bg-red-100 text-red-700 p-2 rounded text-sm">{{ error }}</div>
        <input v-model="name" placeholder="Name" required
               class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-primary-500"/>
        <input v-model="email" type="email" placeholder="Email" required
               class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-primary-500"/>
        <input v-model="password" type="password" placeholder="Password" required
               class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-primary-500"/>
        <input v-model="password_confirmation" type="password" placeholder="Confirm Password" required
               class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-primary-500"/>
        <button type="submit"
                class="w-full py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
          Create Account
        </button>
      </form>
      <p class="text-center text-sm">
        Already have an account?
        <router-link to="/login" class="text-primary-600 hover:underline">Log in</router-link>
      </p>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref } from 'vue'
import { useToast } from "vue-toastification"
import { register } from '../../composables/useAuth'
import { useRouter } from 'vue-router'

export default defineComponent({
  setup() {
    const name = ref(''), email = ref(''), password = ref(''), password_confirmation = ref('')
    const error = ref('')
    const router = useRouter()
    const toast = useToast()

    const onSubmit = async () => {
      error.value = ''
      try {
        await register(name.value, email.value, password.value, password_confirmation.value)
        router.push('/')
        toast.success("Registration successful!", {
          timeout: 2000
        })
      } catch (err: any) {
        if (err.response && err.response.data && err.response.data.message) {
          error.value = err.response.data.message
        } else if (err.response && err.response.data && err.response.data.errors) {
          error.value = Object.values(err.response.data.errors).flat().join(' ')
        } else {
          error.value = 'Registration failed. Please try again.'
        }
      }
    }

    return { name, email, password, password_confirmation, onSubmit, error }
  }
})
</script>
