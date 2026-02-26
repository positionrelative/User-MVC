import { createRouter, createWebHistory } from 'vue-router'
import { useAuth } from '../composables/useAuth'
import Login from '../views/Auth/Login.vue'
import Register from '../views/Auth/Register.vue'
import Users from '../views/Users/List.vue'
import CreateUser from '../views/Users/Create.vue'
import EditUser from '../views/Users/Edit.vue'

const routes = [
  {
    path: '/',
    redirect: '/login'
  },
  {
    path: '/login',
    name: 'Login',
    component: Login,
    meta: { guest: true }
  },
  {
    path: '/register',
    name: 'Register',
    component: Register,
    meta: { guest: true }
  },
  {
    path: '/users',
    name: 'Users',
    component: Users,
    meta: { requiresAuth: true }
  },
  {
    path: '/users/create',
    name: 'CreateUser',
    component: CreateUser,
    meta: { requiresAuth: true }
  },
  {
    path: '/users/:id/edit',
    name: 'EditUser',
    component: EditUser,
    meta: { requiresAuth: true }
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach((to, from, next) => {
  const { isAuthenticated } = useAuth()

  if (to.meta.requiresAuth && !isAuthenticated.value) {
    next('/login')
  } else if (to.meta.guest && isAuthenticated.value) {
    next('/users')
  } else {
    next()
  }
})

export default router
