import { BaseApiClient } from './BaseApiClient'
import {
  RegisterRequestSchema,
  UpdateUserRequestSchema,
  UsersListSchema,
  SingleUserSchema
} from '../schemas/userSchemas'
import { GenericResponseSchema } from '../schemas/commonSchemas'
import { parseWithSchema } from './zodValidation'

export class UsersApi extends BaseApiClient {
  async getUsers() {
    const response = await this.request('/users', { method: 'GET' })
    const parsed = parseWithSchema(UsersListSchema, response, 'Invalid users list response')

    return Array.isArray(parsed) ? parsed : parsed.data
  }

  async getUser(id) {
    const response = await this.request(`/users/${id}`, { method: 'GET' })
    const parsed = parseWithSchema(SingleUserSchema, response, 'Invalid user response')

    return parsed.data ? parsed.data : parsed
  }

  async createUser(userData) {
    const payload = parseWithSchema(RegisterRequestSchema, userData, 'Invalid user data')
    const response = await this.request('/register', {
      method: 'POST',
      body: payload
    })

    return parseWithSchema(GenericResponseSchema, response, 'Invalid create user response')
  }

  async updateUser(id, userData) {
    const payload = parseWithSchema(UpdateUserRequestSchema, userData, 'Invalid update user data')
    const response = await this.request(`/users/${id}`, {
      method: 'PUT',
      body: payload
    })

    return parseWithSchema(GenericResponseSchema, response, 'Invalid update user response')
  }

  async deleteUser(id) {
    const response = await this.request(`/users/${id}`, { method: 'DELETE' })
    return parseWithSchema(GenericResponseSchema, response, 'Invalid delete user response')
  }
}

export const usersApi = new UsersApi()
