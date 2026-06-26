# Data Pasien Service

Service Laravel untuk Tugas 2 Integrasi Aplikasi Enterprise.

## Kontrak API

Header autentikasi:

```text
X-IAE-KEY: 102022400238
```

REST endpoint utama:

```text
GET  /api/v1
GET  /api/v1/{id}
POST /api/v1
```

Alias `/api/v1/patients` juga tersedia untuk resource pasien.

Semua respons REST memakai wrapper:

```json
{
  "status": "success",
  "message": "...",
  "data": {},
  "errors": null
}
```

## Dokumentasi dan GraphQL

```text
Swagger UI: /api/documentation
OpenAPI JSON: /docs atau /openapi.json
GraphQL endpoint: /graphql
GraphQL Playground: /graphql-playground
```

## Menjalankan Lokal

```powershell
docker compose up -d --build
php artisan test
```

Service berjalan di `http://localhost:8001`.
