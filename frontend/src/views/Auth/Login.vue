<template>
  <div class="auth-container">
    <div class="auth-card">
      <h2>Login</h2>
      <form @submit.prevent="handleLogin">
        <div class="field">
          <label for="email">Email <span class="required">*</span></label>
          <InputText
            id="email"
            v-model="formData.email"
            type="email"
            :class="{ 'p-invalid': v$.email.$error }"
            placeholder="your@email.com"
            @blur="v$.email.$touch"
          />
          <small v-if="v$.email.$error" class="p-error">
            {{ v$.email.$errors[0].$message }}
          </small>
        </div>
        <div class="field">
          <label for="password">Password <span class="required">*</span></label>
          <Password
            id="password"
            v-model="formData.password"
            :class="{ 'p-invalid': v$.password.$error }"
            :feedback="false"
            toggleMask
            placeholder="••••••••"
            @blur="v$.password.$touch"
          />
          <small v-if="v$.password.$error" class="p-error">
            {{ v$.password.$errors[0].$message }}
          </small>
        </div>
        <Message v-if="error.value" severity="error">{{ error.value }}</Message>
        <Button
          type="submit"
          label="Login"
          :loading="loading.value"
          :disabled="v$.$invalid && v$.$dirty"
          class="submit-btn"
        />
      </form>
      <p class="auth-link">
        Don't have an account? <router-link to="/register">Register</router-link>
      </p>
    </div>
  </div>
</template>

<script setup>
import { reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useVuelidate } from '@vuelidate/core'
import { useAuth } from '../../composables/useAuth'
import { authApi } from '../../services/AuthApi'
import { createLoginRules } from '../../rules/loginRules'
import InputText from 'primevue/inputtext'
import Password from 'primevue/password'
import Button from 'primevue/button'
import Message from 'primevue/message'

const router = useRouter()
const { login } = useAuth()

const formData = reactive({
  email: '',
  password: ''
})

const rules = createLoginRules()

const v$ = useVuelidate(rules, formData)

const loading = reactive({ value: false })
const error = reactive({ value: '' })

async function handleLogin() {
  const isValid = await v$.value.$validate()
  if (!isValid) return

  loading.value = true
  error.value = ''

  try {
    const response = await authApi.login(formData)
    login(response.data.token, response.data.user)
    router.push('/users')
  } catch (err) {
    error.value = err.message
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.submit-btn {
  width: 100%;
  margin-top: 0.5rem;
}
</style>

