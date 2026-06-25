FROM php:8.2-cli

# Install dependencies yang mungkin dibutuhkan Laravel
RUN apt-get update && apt-get install -y libzip-dev zip git

# Mengatur folder kerja di dalam container
WORKDIR /app

# Menyalin SEMUA file dari laptopmu ke dalam container
COPY . .

# Menyiapkan file .env (karena di-copy dari .env.example)
RUN cp .env.example .env

# Menjalankan server
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]