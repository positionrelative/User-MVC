<template>
  <div class="edit-container">
    <ConfirmPopup />

    <div class="edit-card">
      <div class="header">
        <h2>Edit User</h2>
        <div class="header-actions">
          <Button
            icon="pi pi-trash"
            label="Delete"
            severity="danger"
            outlined
            :disabled="saving || loading"
            @click="deleteCurrentUser($event)"
          />
          <Button
            icon="pi pi-arrow-left"
            label="Back"
            outlined
            @click="goBack"
          />
        </div>
      </div>

      <div v-if="loading" class="loading">
        <ProgressSpinner style="width: 50px; height: 50px" />
        <p>Loading user data...</p>
      </div>
      <Message v-else-if="loadError" severity="error">{{ loadError }}</Message>
      <Form
        v-else
        :initial-data="form"
        :is-edit="true"
        :loading="saving"
        :error="error"
        :success="success"
        submit-label="Update User"
        @submit="handleUpdate"
        @cancel="goBack"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { usersApi } from '../../services/UsersApi'
import { useConfirm } from 'primevue/useconfirm'
import Button from 'primevue/button'
import Message from 'primevue/message'
import ProgressSpinner from 'primevue/progressspinner'
import ConfirmPopup from 'primevue/confirmpopup'
import Form from './Form.vue'

const router = useRouter()
const route = useRoute()
const confirm = useConfirm()

const form = ref({
  name: '',
  email: '',
  password: ''
})

const loading = ref(true)
const loadError = ref('')
const saving = ref(false)
const error = ref('')
const success = ref('')

onMounted(async () => {
  try {
    const user = await usersApi.getUser(route.params.id)
    form.value.name = user.name || ''
    form.value.email = user.email || ''
  } catch (err) {
    loadError.value = err.message
  } finally {
    loading.value = false
  }
})

async function handleUpdate(formData) {
  saving.value = true
  error.value = ''
  success.value = ''

  try {
    await usersApi.updateUser(route.params.id, formData)
    success.value = 'User updated successfully!'
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

function deleteCurrentUser(event) {
  confirm.require({
    target: event.currentTarget,
    message: `Are you sure you want to delete ${form.value.name || 'this user'}?`,
    icon: 'pi pi-exclamation-triangle',
    rejectLabel: 'Cancel',
    acceptLabel: 'Delete',
    acceptClass: 'p-button-danger',
    accept: async () => {
      saving.value = true
      error.value = ''

      try {
        await usersApi.deleteUser(route.params.id)
        router.push('/users')
      } catch (err) {
        error.value = err.message
      } finally {
        saving.value = false
      }
    }
  })
}
</script>

<style scoped>
.loading {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 3rem;
  color: #6c757d;
}

.loading p {
  margin-top: 1rem;
  font-size: 1.1rem;
}

.header-actions {
  display: flex;
  gap: 0.5rem;
}
</style>

