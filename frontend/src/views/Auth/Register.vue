<template>
  <div class="auth-container">
    <div class="auth-card">
      <h2>Register</h2>
      <Form
        :loading="loading"
        :error="error"
        :success="success"
        submit-label="Register"
        @submit="handleRegister"
        @cancel="() => $router.push('/login')"
      />
      <p class="auth-link">
        Already have an account? <router-link to="/login">Login</router-link>
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { authApi } from '../../services/AuthApi'
import Form from '../Users/Form.vue'

const router = useRouter()

const loading = ref(false)
const error = ref('')
const success = ref('')

async function handleRegister(formData) {
  loading.value = true
  error.value = ''
  success.value = ''

  try {
    await authApi.register(formData)
    success.value = 'Registration successful! Redirecting to login...'
    setTimeout(() => {
      router.push('/login')
    }, 1500)
  } catch (err) {
    error.value = err.message
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
</style>
