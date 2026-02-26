import { BaseApiClient } from './BaseApiClient'
import {
  LoginRequestSchema,
  LoginEnvelopeSchema
} from '../schemas/authSchemas'
import { RegisterRequestSchema } from '../schemas/userSchemas'
import { GenericResponseSchema } from '../schemas/commonSchemas'
import { parseWithSchema } from './zodValidation'

export class AuthApi extends BaseApiClient {
  async register(userData) {
    const payload = parseWithSchema(RegisterRequestSchema, userData, 'Invalid registration data')
    const response = await this.request('/register', {
      method: 'POST',
      body: payload
    })

    return parseWithSchema(GenericResponseSchema, response, 'Invalid registration response')
  }

  async login(credentials) {
    const payload = parseWithSchema(LoginRequestSchema, credentials, 'Invalid login data')
    const response = await this.request('/login', {
      method: 'POST',
      body: payload
    })

    return parseWithSchema(LoginEnvelopeSchema, response, 'Invalid login response')
  }
}

export const authApi = new AuthApi()
