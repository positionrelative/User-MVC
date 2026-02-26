import { z } from 'zod'

export const UserSchema = z.looseObject({
  id: z.union([z.number(), z.string()]).optional(),
  name: z.string().min(1),
  email: z.email(),
  created_at: z.string().nullable().optional(),
  updated_at: z.string().nullable().optional()
})

export const RegisterRequestSchema = z.object({
  name: z.string().min(1),
  email: z.email(),
  password: z.string().min(6)
})

export const UpdateUserRequestSchema = z.object({
  name: z.string().min(1),
  email: z.email(),
  password: z.string().min(6).optional()
})

export const UsersListSchema = z.union([
  z.array(UserSchema),
  z.looseObject({ data: z.array(UserSchema) })
])

export const SingleUserSchema = z.union([
  UserSchema,
  z.looseObject({ data: UserSchema })
])
