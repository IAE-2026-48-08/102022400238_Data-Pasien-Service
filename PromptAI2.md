# Log Prompting AI - Tugas 2 Integrasi Aplikasi Enterprise (IAE)

**Nama:** Tabitha Glorya Yobelitha Sirait  
**NIM:** 102022400238  
**Kelompok:** 13 (E-Healthcare)  
**Fokus Service:** Service Data Pasien  

---

## 1. Tahap Inisiasi Proyek
**Prompt Pengguna:**
> "saya sudah membuat file project, dan nama database dengan datapasien, jadi bantu saya melakukan penugasan kali ini. [Melampirkan PDF Tugas 2, Standard Integration Contract, dan Kontrak Kelompok]"

**Respon AI (Ringkasan):**
Menyusun rencana eksekusi menjadi 5 langkah utama:
1. Persiapan Database & Model (Migration).
2. Pembuatan Trait untuk Standardisasi Respon API.
3. Pembuatan Middleware Keamanan (X-IAE-KEY).
4. Routing & Controller REST API.
5. Instalasi & Konfigurasi Swagger (Dokumentasi) dan GraphQL.

---

## 2. Pembuatan Migration & Model
**Prompt Pengguna:**
> "ayo mulai membuatnya"

**Respon AI:**
Memberikan perintah `php artisan make:model Patient -m` dan struktur tabel untuk rekam medis dasar pasien (nik, name, birth_date, gender, address, medical_history) beserta pengaturan `$fillable` pada Model.

**Kendala & Solusi:**
* **Error:** `Could not open input file: artisan`
* **Solusi:** AI mengarahkan pengguna untuk masuk ke dalam direktori *project* menggunakan perintah `cd data-pasien-service` sebelum menjalankan perintah artisan.

---

## 3. Standardisasi Format Respon (Wrapper)
**Prompt Pengguna:**
> "lanjut"

**Respon AI:**
Mengarahkan pembuatan `app/Traits/ApiResponse.php` untuk memastikan seluruh *output* API JSON konsisten memiliki properti `status`, `message`, `data`, dan `meta` sesuai dengan Standard Integration Contract.

---

## 4. Keamanan API Key
**Prompt Pengguna:**
> "lanjut"

**Respon AI:**
Menginstruksikan pembuatan `ApiKeyMiddleware` dengan *Header Key* `X-IAE-KEY`. *Value* diatur menggunakan identitas NIM mahasiswa (`102022400238`) untuk memproteksi setiap *request* yang masuk.

---

## 5. Pembuatan Controller & Routing REST API
**Prompt Pengguna:**
> "lanjut"

**Respon AI:**
Mengarahkan pembuatan `PatientController` yang mencakup 3 jenis *endpoint* wajib:
* `GET /api/v1/patients` (Collection)
* `GET /api/v1/patients/{id}` (Resource)
* `POST /api/v1/patients` (Action)

**Kendala & Solusi:**
* **Error:** Saat men- *setup* `routes/api.php`, file default terisi dengan route Sanctum bawaan Laravel 11.
* **Solusi:** AI menginstruksikan untuk menghapus rute *default* dan menggantinya dengan rute Service Data Pasien yang diproteksi oleh *middleware* `api.key`.

---

## 6. Instalasi Swagger / OpenAPI Documentation
**Prompt Pengguna:**
> "bisakah test endpoint pakai localhost/documentations"

**Respon AI:**
Memberikan langkah instalasi `darkaonline/l5-swagger` untuk memunculkan UI dokumentasi.

**Kendala & Solusi:**
* **Error:** Saat melakukan *generate* dokumentasi, muncul peringatan `Required @OA\Info() not found` pada terminal.
* **Solusi:** AI mengubah implementasi anotasi DocBlock lama menjadi **PHP Attributes** (`#[OA\Info...]`) pada `PatientController` untuk menyesuaikan dengan arsitektur Laravel 11. Setelah cache dibersihkan (`config:clear` dan `route:clear`), Swagger berhasil di-*generate*.

---

## 7. Pengujian (Testing) REST API via Swagger
**Prompt Pengguna:**
> "[Melampirkan Screenshot pengujian Swagger yang menghasilkan Error 404 Not Found]"

**Respon AI:**
Menganalisis *error* dan menemukan bahwa keamanan Header `X-IAE-KEY` sudah berhasil, format *error* standar berjalan baik, namun data tidak ditemukan karena parameter `id` diisi dengan angka NIM (`102022400238`) yang melebihi jumlah data di *database*. AI mengarahkan pengguna untuk menggunakan ID `1`.

**Prompt Pengguna:**
> "[Melampirkan Screenshot pengujian POST (201 Created) dan GET Collection (200 OK)]"

**Respon AI:**
Mengonfirmasi bahwa seluruh fungsionalitas REST API sudah berjalan 100% sempurna dan memenuhi kriteria rubrik tugas.

---

## 8. Implementasi GraphQL
**Respon AI:**
Sebagai penutup fungsionalitas, AI memberikan perintah instalasi `nuwave/lighthouse` dan `mll-lab/laravel-graphql-playground`. Dilanjutkan dengan pendefinisian `schema.graphql` untuk memungkinkan klien melakukan *query* pengambilan daftar pasien dan detail pasien menggunakan antarmuka interaktif GraphQL Playground.

---
*Log ini di-generate sebagai bukti implementasi prompting AI pada Tugas 2 IAE.*