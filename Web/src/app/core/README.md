# Core Module

This module contains singleton services, global guards, interceptors, and models that are used throughout the application.

## Contents
- **auth/**: Authentication logic, guards, and interceptors.
- **http/**: Base HTTP services and interceptors.
- **services/**: Global singleton services (e.g., LoaderService, NotificationService).
- **models/**: Domain models shared across the entire application.

**Note:** Do not import Feature modules here. This module should only be imported by the Root Module (AppModule) or provided in `app.config.ts`.
