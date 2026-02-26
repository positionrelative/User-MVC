import { z } from 'zod'

export const GenericResponseSchema = z.looseObject({
  success: z.boolean().optional(),
  message: z.string().optional(),
  error: z.string().optional(),
  data: z.unknown().optional()
})
