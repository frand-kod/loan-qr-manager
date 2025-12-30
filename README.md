# Loan QR Manager & Automation Reminder

Sistem manajemen hutang piutang yang dilengkapi dengan otomatisasi pengingat jatuh tempo melalui WhatsApp serta pencatatan log aktivitas.

## Fitur Utama

-   Manajemen Data Nasabah: Penyimpanan informasi kontak dan nomor WhatsApp nasabah.
-   Manajemen Tagihan: Pencatatan nominal hutang, tanggal jatuh tempo, dan status pembayaran.
-   Otomatisasi WhatsApp: Pengiriman pesan pengingat secara otomatis pada periode H-7, H-3, Hari H, dan H+3 dari tanggal jatuh tempo.
-   Log Aktivitas: Pencatatan otomatis setiap aktivitas sistem untuk keperluan audit dan monitoring.
-   API Terintegrasi: Endpoint API untuk pembuatan dan pengelolaan data hutang dari sistem eksternal.

## Persyaratan Sistem

-   PHP >= 8.2
-   Composer
-   Database (MySQL, PostgreSQL, atau SQLite)
-   Ekstensi PHP yang relevan (BCMath, Ctype, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML)

## Instalasi

1. Duplikasi repositori ke direktori lokal Anda.
2. Jalankan perintah instalasi dependensi:
   composer install
3. Salin file konfigurasi lingkungan:
   cp .env.example .env
4. Lakukan konfigurasi pada file .env, terutama bagian DB_DATABASE dan kredensial API WhatsApp.
5. Generate application key:
   php artisan key:generate
6. Jalankan migrasi database:
   php artisan migrate

## Penggunaan Command

Sistem menyediakan command line interface untuk menjalankan pengingat secara manual:
php artisan reminder:send-wa

Untuk penggunaan di server produksi, tambahkan perintah berikut pada crontab server Anda agar otomatisasi berjalan setiap menit:

-   -   -   -   -   cd /path-ke-proyek-anda && php artisan schedule:run >> /dev/null 2>&1

## Pengujian (Testing)

Proyek ini dilengkapi dengan Automated Testing untuk memastikan stabilitas fitur. Jalankan perintah berikut untuk mengeksekusi semua unit dan feature test:
php artisan test

Fitur yang diuji meliputi:

-   DebtApiTest: Validasi input dan keberhasilan pembuatan data hutang melalui API.
-   WhatsAppReminderTest: Akurasi logika perhitungan tanggal jatuh tempo dan verifikasi pengiriman pesan.

## Struktur Database Utama

-   customers: Menyimpan data profil nasabah.
-   debts: Menyimpan detail hutang dan relasi ke nasabah.
-   activity_logs: Menyimpan rekaman jejak aktivitas sistem dan pengguna.

made with love and the thousand hugs 🤗
