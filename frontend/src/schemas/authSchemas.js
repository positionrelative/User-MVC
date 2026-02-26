import { z } from 'zod'
import { UserSchema } from './userSchemas'

export const LoginRequestSchema = z.object({
  email: z.email(),
  password: z.string().min(1)
})

export const LoginEnvelopeSchema = z.union([
  z.looseObject({
    data: z.object({
      token: z.string().min(1),
      user: UserSchema
    })
  }),
  z.looseObject({
    token: z.string().min(1),
    user: UserSchema
  }).transform((value) => ({ data: value }))
])
