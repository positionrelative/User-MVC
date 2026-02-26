import { ref, computed } from 'vue'

const TOKEN_KEY = 'auth_token'
const USER_KEY = 'auth_user'

const token = ref(getToken())
const user = ref(getUser())

function syncAuthState() {
  const currentToken = getToken()

  if (token.value !== currentToken) {
    token.value = currentToken
  }

  if (!currentToken && user.value) {
    setUser(null)
  }
}

function getToken() {
  if (typeof document === 'undefined') return null
  const cookies = document.cookie.split('; ')
  const tokenCookie = cookies.find(c => c.startsWith(TOKEN_KEY + '='))
  return tokenCookie ? tokenCookie.split('=')[1] : null
}

function getUser() {
  if (typeof localStorage === 'undefined') return null
  const stored = localStorage.getItem(USER_KEY)
  return stored ? JSON.parse(stored) : null
}

function setToken(value) {
  token.value = value
  if (value) {
    document.cookie = `${TOKEN_KEY}=${value}; path=/; secure; samesite=strict; max-age=604800`
  } else {
    document.cookie = `${TOKEN_KEY}=; path=/; expires=Thu, 01 Jan 1970 00:00:00 GMT`
  }
}

function setUser(value) {
  user.value = value
  if (value) {
    localStorage.setItem(USER_KEY, JSON.stringify(value))
  } else {
    localStorage.removeItem(USER_KEY)
  }
}

export function useAuth() {
  const isAuthenticated = computed(() => {
    syncAuthState()
    return !!token.value
  })

  const login = (authToken, userData) => {
    setToken(authToken)
    setUser(userData)
  }

  const logout = () => {
    setToken(null)
    setUser(null)
  }

  return {
    token: computed(() => {
      syncAuthState()
      return token.value
    }),
    user: computed(() => {
      syncAuthState()
      return user.value
    }),
    isAuthenticated,
    login,
    logout
  }
}
