import { required, email, helpers } from '@vuelidate/validators'

export function createLoginRules() {
  return {
    email: {
      required: helpers.withMessage('Email is required', required),
      email: helpers.withMessage('Must be a valid email', email)
    },
    password: {
      required: helpers.withMessage('Password is required', required)
    }
  }
}
