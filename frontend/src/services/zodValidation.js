function formatIssues(issues, fallbackMessage) {
  if (!Array.isArray(issues) || issues.length === 0) {
    return fallbackMessage
  }

  const firstIssue = issues[0]
  const fieldPath = Array.isArray(firstIssue.path) && firstIssue.path.length
    ? `${firstIssue.path.join('.')} `
    : ''

  return `${fieldPath}${firstIssue.message}`.trim()
}

export function parseWithSchema(schema, payload, fallbackMessage = 'Validation failed') {
  const result = schema.safeParse(payload)

  if (result.success) {
    return result.data
  }

  throw new Error(formatIssues(result.error.issues, fallbackMessage))
}
