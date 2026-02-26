import { required, email, minLength, helpers } from '@vuelidate/validators'

export function createUserFormRules(isEdit) {
  return {
    name: {
      required: helpers.withMessage('Name is required', required)
    },
    email: {
      required: helpers.withMessage('Email is required', required),
      email: helpers.withMessage('Must be a valid email', email)
    },
    password: isEdit
      ? {
          minLength: helpers.withMessage(
            'Password must be at least 6 characters',
            (value) => !value || value.length >= 6
          )
        }
      : {
          required: helpers.withMessage('Password is required', required),
          minLength: helpers.withMessage('Password must be at least 6 characters', minLength(6))
        }
  }
}
