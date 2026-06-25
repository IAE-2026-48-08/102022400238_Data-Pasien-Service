# 1. Ambil mesin dasar PHP versi 8.2
FROM php:8.2-cli

# 2. Install perlengkapan tambahan yang dibutuhkan Laravel
RUN apt-get update && apt-get install -y libzip-dev zip git

# 3. Buat folder bernama /app di dalam Docker sebagai tempat kerja utama
WORKDIR /app

# 4. Salin SEMUA file kodinganmu dari laptop ke dalam mesin Docker
COPY . .

# 5. Otomatis buatkan file .env untuk memenuhi syarat grader
RUN cp .env.example .env
RUN php artisan key:generate
# 6. Nyalakan server Laravel saat mesin Docker dihidupkan
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]