<template>
  <div class="users-container">
    <ConfirmPopup />

    <div class="header-row">
      <h2>Users</h2>
      <div class="header-actions">
        <Button label="Create User" icon="pi pi-plus" @click="createUser" />
      </div>
    </div>

    <Message v-if="error" severity="error" class="mb-3">{{ error }}</Message>

    <DataTable
      :value="users"
      :loading="loading"
      dataKey="id"
      paginator
      :rows="10"
      stripedRows
      removableSort
      tableStyle="min-width: 50rem"
      emptyMessage="No users found"
    >
      <Column field="id" header="ID" sortable style="width: 90px" />
      <Column field="name" header="Name" sortable />
      <Column field="email" header="Email" sortable />
      <Column header="Joined" sortable sortField="created_at">
        <template #body="slotProps">
          {{ formatDate(slotProps.data.created_at) }}
        </template>
      </Column>
      <Column header="Actions" style="width: 160px">
        <template #body="slotProps">
          <div class="action-buttons">
            <Button
              icon="pi pi-pencil"
              text
              rounded
              aria-label="Edit"
              @click="editUser(slotProps.data.id)"
            />
            <Button
              icon="pi pi-trash"
              text
              rounded
              severity="danger"
              aria-label="Delete"
              @click="confirmDelete($event, slotProps.data)"
            />
          </div>
        </template>
      </Column>
    </DataTable>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { usersApi } from '../../services/UsersApi'
import { useConfirm } from 'primevue/useconfirm'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Button from 'primevue/button'
import Message from 'primevue/message'
import ConfirmPopup from 'primevue/confirmpopup'

const router = useRouter()
const confirm = useConfirm()

const users = ref([])
const loading = ref(true)
const error = ref('')

async function loadUsers() {
  loading.value = true
  error.value = ''

  try {
    const data = await usersApi.getUsers()
    users.value = Array.isArray(data) ? data : []
  } catch (err) {
    error.value = err.message
  } finally {
    loading.value = false
  }
}

onMounted(loadUsers)

function createUser() {
  router.push('/users/create')
}

function editUser(id) {
  router.push(`/users/${id}/edit`)
}

function confirmDelete(event, selectedUser) {
  confirm.require({
    target: event.currentTarget,
    message: `Are you sure you want to delete ${selectedUser.name}?`,
    icon: 'pi pi-exclamation-triangle',
    rejectLabel: 'Cancel',
    acceptLabel: 'Delete',
    acceptClass: 'p-button-danger',
    accept: async () => {
      try {
        await usersApi.deleteUser(selectedUser.id)
        await loadUsers()
      } catch (err) {
        error.value = err.message
      }
    }
  })
}

function formatDate(dateString) {
  if (!dateString) return 'N/A'
  return new Date(dateString).toLocaleDateString()
}
</script>

<style scoped>
.users-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem;
}

.header-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.header-actions {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.action-buttons {
  display: flex;
  gap: 0.25rem;
}

.mb-3 {
  margin-bottom: 1rem;
}
</style>
