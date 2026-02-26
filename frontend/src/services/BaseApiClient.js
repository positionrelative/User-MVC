import { useAuth } from '../composables/useAuth'

export class BaseApiClient {
  constructor(baseUrl = '/api') {
    this.baseUrl = baseUrl
  }

  async request(endpoint, options = {}) {
    const { token, logout } = useAuth()
    const isProtectedEndpoint = endpoint.startsWith('/users')

    if (isProtectedEndpoint && !token.value) {
      logout()

      if (typeof window !== 'undefined' && window.location.pathname !== '/login') {
        window.location.assign('/login')
      }

      throw new Error('Session expired. Please log in again.')
    }

    const headers = {
      'Content-Type': 'application/json',
      ...options.headers
    }

    if (token.value) {
      headers.Authorization = `Bearer ${token.value}`
    }

    const response = await fetch(`${this.baseUrl}${endpoint}`, {
      ...options,
      headers,
      body: options.body ? JSON.stringify(options.body) : undefined
    })

    const payload = await this.parseResponseBody(response)

    if (response.status === 401 && isProtectedEndpoint) {
      logout()

      if (typeof window !== 'undefined' && window.location.pathname !== '/login') {
        window.location.assign('/login')
      }

      throw new Error('Session expired. Please log in again.')
    }

    if (!response.ok) {
      const errorMessage = payload?.error || payload?.message || 'Request failed'
      throw new Error(errorMessage)
    }

    return payload
  }

  async parseResponseBody(response) {
    const contentType = response.headers.get('content-type') || ''

    if (contentType.includes('application/json')) {
      return response.json()
    }

    const text = await response.text()
    return text ? { message: text } : {}
  }
}
