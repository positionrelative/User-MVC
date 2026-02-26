## How to run it

1. Build and start all containers:

```bash
docker compose up -d --build
```

2. Open the apps:
- Backend/API (PHP): http://localhost:8080
- Frontend (Vue): http://localhost:5173
- phpMyAdmin: http://localhost:8081

## Default login credentials

- Email: `admin@example.com`
- Password: `demopass`

## Assumptions and design decisions

- Backend uses a lightweight custom MVC + Router architecture.
- User APIs are protected with bearer-token auth.
- Frontend is Vue 3 + PrimeVue for UI and Vuelidate for form validation.
- API access is split into service classes (`AuthApi`, `UsersApi`) with Zod validation for request/response shaping.

## What was skipped due to time limits

- Forgot password flow is not implemented.
- Password confirmation flow is not implemented.
- Automated tests (unit/integration/e2e) were not added.
- No advanced auth hardening (refresh token flow, token rotation, RBAC).