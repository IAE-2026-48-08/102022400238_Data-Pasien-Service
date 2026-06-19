# Log Penggunaan AI (Prompt Engineering)
**Tugas 3 - Integrasi Aplikasi Enterprise**

**Nama:** Tabitha Glorya Yobelitha Sirait  
**NIM:** 102022400238  
**Layanan:** Service Data Pasien  

---

## Deskripsi Penggunaan AI
Dalam pengerjaan Tugas 3 ini, saya menggunakan bantuan *Generative AI* (Gemini) sebagai *pair-programmer* dan tutor untuk memahami konsep integrasi *Enterprise*, melakukan *debugging* *error*, serta menyusun visualisasi *Sequence Diagram*. 

Berikut adalah riwayat *prompt* utama yang saya gunakan selama proses pengerjaan:

### 1. Debugging Modul 2 (SOAP XML Client)
* **Konteks:** Saat melakukan *testing* pengiriman data ke server SOAP Dosen, saya mendapatkan respons *error* dari server.
* **Prompt yang digunakan:** > *(Melampirkan Screenshot)* "Kalau muncul pesan error `<faultstring>Invalid XML document.</faultstring>` saat memanggil endpoint API SOAP dosen, kalau begini artinya apa dan bagaimana solusinya?"
* **Hasil:** AI membantu mengidentifikasi bahwa token SSO sudah berhasil masuk, namun format *body* XML ditolak karena ada spasi tersembunyi. AI memberikan solusi untuk menggunakan `Http::withBody()` dengan format XML yang rata kiri (kaku) di `IaeCloudService.php`.

### 2. Integrasi & Implementasi Controller
* **Konteks:** Setelah fungsi SOAP dan RabbitMQ berhasil dites secara terpisah, saya perlu menggabungkannya ke dalam *Controller* utama.
* **Prompt yang digunakan:** > *(Melampirkan Screenshot kodingan PatientController)* "Buat kode integrasinya di buat di bagian mana pada fungsi store(Request $request) agar data pasien baru tersimpan di database lokal dulu sebelum dikirim ke Cloud Dosen?"
* **Hasil:** AI memberikan struktur penempatan kode yang tepat, yaitu di bawah proses penyimpanan `$patient = Patient::create($request->all());` dan memastikan token SSO membalut transaksi SOAP dan RabbitMQ.

### 3. Pengujian Endpoint via CLI (Terminal)
* **Konteks:** Menghindari *error* *Unauthorized* karena pengujian via *browser* tidak bisa membawa *header* otorisasi (`X-IAE-KEY`).
* **Prompt yang digunakan:** > "Bisa ga eksekusi nya (testing API POST /patients) langsung dari terminal?"
* **Hasil:** AI memberikan perintah `Invoke-RestMethod` khusus untuk PowerShell yang secara otomatis mengirimkan *Header* `X-IAE-KEY` dan *Body* JSON pasien baru. Eksekusi ini berhasil menembus sistem dan memunculkan log di *dashboard* dosen.

### 4. Pemahaman Konseptual Keamanan (SSO & JWT)
* **Konteks:** Saya melihat ada perbedaan data antara *dashboard* dosen (menampilkan TEAM-13) dengan *payload* JSON yang saya kirim.
* **Prompt yang digunakan:** > *(Melampirkan Screenshot detail log Dosen)* "Jadi fungsi akun warga dan API-Key ini apa? Di JSON nya emang ga muncul ya, tapi pengerjaan kita berhasil dan namanya berubah jadi TEAM-13."
* **Hasil:** AI menjelaskan konsep dekripsi JWT Token. API-Key digunakan untuk masuk pertama kali ke *SSO Server*, kemudian ditukar dengan *JWT Token*. Token inilah yang membawa identitas rahasia tim, sehingga alasan keamanan melarang penulisan API-Key secara eksplisit di dalam *Body JSON* RabbitMQ.

### 5. Pembuatan Sequence Diagram
* **Konteks:** Membutuhkan visualisasi *Sequence Diagram* untuk dokumen analisis menggunakan *software* pilihan saya.
* **Prompt yang digunakan:** > "Sekarang saatnya kita membuat sequence diagram. Buatkan sequence nya saya menggunakan Visual Paradigm."
  > "Garis panah putus-putus adalah Return Message, kok tidak ada ya di paradigm saya? Cara nya gimana soalnya masih begitu."
  > "Return JWT artinya apa di diagram ini?"
* **Hasil:** AI memberikan panduan langkah demi langkah menggambar *Lifeline* dan panah di Visual Paradigm, memberikan trik khusus (klik kanan -> *Type* -> *Return Message*) untuk mengubah jenis panah, serta menjelaskan filosofi balasan token SSO sebagai tiket otorisasi dalam diagram.

---
*Dokumen ini disusun sebagai bukti akuntabilitas dan kemandirian dalam proses problem-solving pada Tugas 3 IAE.*

