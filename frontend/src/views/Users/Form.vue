<template>
  <form @submit.prevent="handleSubmit" class="user-form">
    <div class="field">
      <label for="name">Name <span class="required">*</span></label>
      <InputText
        id="name"
        v-model="formData.name"
        :class="{ 'p-invalid': v$.name.$error }"
        placeholder="Enter name"
        @blur="v$.name.$touch"
      />
      <small v-if="v$.name.$error" class="p-error">
        {{ v$.name.$errors[0].$message }}
      </small>
    </div>

    <div class="field">
      <label for="email">Email <span class="required">*</span></label>
      <InputText
        id="email"
        v-model="formData.email"
        type="email"
        :class="{ 'p-invalid': v$.email.$error }"
        placeholder="Enter email"
        @blur="v$.email.$touch"
      />
      <small v-if="v$.email.$error" class="p-error">
        {{ v$.email.$errors[0].$message }}
      </small>
    </div>

    <div class="field">
      <label for="password">
        Password 
        <span v-if="!isEdit" class="required">*</span>
        <span v-else class="optional">(leave empty to keep current)</span>
      </label>
      <Password
        id="password"
        v-model="formData.password"
        :class="{ 'p-invalid': v$.password.$error }"
        :feedback="!isEdit"
        toggleMask
        placeholder="Enter password"
        @blur="v$.password.$touch"
      />
      <small v-if="v$.password.$error" class="p-error">
        {{ v$.password.$errors[0].$message }}
      </small>
    </div>

    <Message v-if="error" severity="error">{{ error }}</Message>
    <Message v-if="success" severity="success">{{ success }}</Message>

    <div class="form-actions">
      <Button
        type="button"
        label="Cancel"
        severity="secondary"
        outlined
        @click="$emit('cancel')"
      />
      <Button
        type="submit"
        :label="submitLabel"
        :loading="loading"
        :disabled="v$.$invalid && v$.$dirty"
      />
    </div>
  </form>
</template>

<script setup>
import { reactive, computed, watch } from 'vue'
import { useVuelidate } from '@vuelidate/core'
import { createUserFormRules } from '../../rules/userFormRules'
import InputText from 'primevue/inputtext'
import Password from 'primevue/password'
import Button from 'primevue/button'
import Message from 'primevue/message'

const props = defineProps({
  initialData: {
    type: Object,
    default: () => ({
      name: '',
      email: '',
      password: ''
    })
  },
  isEdit: {
    type: Boolean,
    default: false
  },
  loading: {
    type: Boolean,
    default: false
  },
  error: {
    type: String,
    default: ''
  },
  success: {
    type: String,
    default: ''
  },
  submitLabel: {
    type: String,
    default: 'Submit'
  }
})

const emit = defineEmits(['submit', 'cancel'])

const formData = reactive({
  name: props.initialData.name || '',
  email: props.initialData.email || '',
  password: props.initialData.password || ''
})

watch(() => props.initialData, (newData) => {
  if (newData) {
    formData.name = newData.name || ''
    formData.email = newData.email || ''
    formData.password = newData.password || ''
  }
}, { deep: true })

const rules = computed(() => createUserFormRules(props.isEdit))

const v$ = useVuelidate(rules, formData)

async function handleSubmit() {
  const isValid = await v$.value.$validate()
  if (!isValid) return

  const submitData = {
    name: formData.name,
    email: formData.email
  }

  if (formData.password) {
    submitData.password = formData.password
  }

  emit('submit', submitData)
}
</script>


