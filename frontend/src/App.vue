<template>
  <div class="app-shell">
    <header class="app-header">
      <div class="header-left">
        <router-link to="/" class="brand-link">
          <span class="brand-text">User MVC</span>
        </router-link>
      </div>

      <nav class="header-center app-nav" v-if="isAuthenticated">
        <router-link to="/users" class="nav-link">Users</router-link>
      </nav>

      <div class="header-right">
        <div v-if="isAuthenticated" class="user-info">
          <span class="welcome-text">Welcome, {{ user?.name }}</span>
          <Button
            label="Logout"
            severity="danger"
            text
            @click="handleLogout"
          />
        </div>

        <nav v-else class="app-nav">
          <router-link to="/login" class="nav-link">Login</router-link>
          <router-link to="/register" class="nav-link">Register</router-link>
        </nav>
      </div>

    </header>

    <main class="app-content">
      <router-view />
    </main>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
import Button from 'primevue/button'
import { useAuth } from './composables/useAuth'

const router = useRouter()
const { isAuthenticated, user, logout } = useAuth()

function handleLogout() {
  logout()
  router.push('/login')
}
</script>
