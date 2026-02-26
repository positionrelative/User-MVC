<template>
  <div class="create-container">
    <div class="create-card">
      <div class="header">
        <h2>Create New User</h2>
        <Button
          icon="pi pi-arrow-left"
          label="Back"
          outlined
          @click="goBack"
        />
      </div>

      <Form
        :loading="saving"
        :error="error"
        :success="success"
        submit-label="Create User"
        @submit="handleCreate"
        @cancel="goBack"
      />
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { usersApi } from '../../services/UsersApi'
import Button from 'primevue/button'
import Form from './Form.vue'

const router = useRouter()

const saving = ref(false)
const error = ref('')
const success = ref('')

async function handleCreate(formData) {
  saving.value = true
  error.value = ''
  success.value = ''

  try {
    await usersApi.createUser(formData)
    success.value = 'User created successfully!'
    setTimeout(() => {
      router.push('/users')
    }, 1500)
  } catch (err) {
    error.value = err.message
  } finally {
    saving.value = false
  }
}

function goBack() {
  router.push('/users')
}
</script>


