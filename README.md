# SIMTAG — Sistem Informasi Survei PDAM Tirta Pinang

Aplikasi web survei kepuasan pelanggan Perumda Air Minum Tirta Pinang Kota Pangkalpinang.
Dibangun dengan Laravel 12, Blade, Tailwind CSS 4, Alpine.js, dan Chart.js. Responsif untuk HP, tablet, dan desktop.

## Fitur

**Publik**
- Landing page (statistik, survei aktif, cara kerja, FAQ) → Masuk / Daftar
- Login memakai email **atau** nomor pelanggan, registrasi mandiri, lupa kata sandi via OTP email

**Pelanggan** (`/app`)
- Beranda: survei yang menunggu, ringkasan, status pengaduan
- Survei: isi survei (rating bintang 1–5, pilihan ganda, isian teks, saran) — satu kali per survei, lihat kembali jawaban
- Pengaduan: kirim laporan gangguan (kategori, lokasi, foto) dengan nomor tiket, pantau status & tanggapan
- Riwayat aktivitas, profil & ubah kata sandi

**Admin & Petugas** (`/admin`)
- Ringkasan: indeks kepuasan, tren 6 bulan, sebaran kategori, per kecamatan, aspek prioritas perbaikan
- Manajemen survei: buat/ubah/duplikat, publikasi/tutup, batas akhir, kelola & urutkan pertanyaan
- Hasil survei per pertanyaan, daftar responden, detail jawaban, **ekspor Excel (.xlsx)**, **cetak / PDF**
- Pengaduan: filter, tanggapi, ubah status
- Data pelanggan (CRUD, reset kata sandi), laporan kepuasan gabungan + ekspor Excel
- Khusus administrator: pengguna sistem (admin/petugas) dan log aktivitas

## Instalasi

Prasyarat: PHP 8.2+ (ekstensi `pdo_mysql`, `zip`, `fileinfo`), Composer, Node.js 20+, MySQL/MariaDB (mis. XAMPP).

```bash
composer install
npm install
cp .env.example .env        # sesuaikan DB_* (default: database "simtag", user root)
php artisan key:generate
php artisan migrate:fresh --seed   # buat tabel + data demo
php artisan storage:link
npm run build
php artisan serve
```

Buka http://127.0.0.1:8000

## Akun demo

| Peran     | Email                        | Kata sandi    |
|-----------|------------------------------|---------------|
| Admin     | admin@tirtapinang.co.id      | admin123      |
| Petugas   | petugas@tirtapinang.co.id    | petugas123    |
| Pelanggan | budi.pelanggan@example.com (atau `TP-26100001`) | pelanggan123 |

Semua pelanggan demo lain memakai kata sandi `pelanggan123`.

## Catatan

- **Email OTP**: secara default `MAIL_MAILER=log`, jadi email dicatat di `storage/logs/laravel.log`; di mode `local` kode OTP juga ditampilkan di layar. Untuk produksi isi konfigurasi SMTP di `.env`.
- **Indeks Kepuasan** = (rata-rata skor rating ÷ 5) × 100. Hanya pertanyaan bertipe rating yang dihitung.
- Fitur tagihan dan pembayaran sudah dihapus; sistem berfokus pada survei dan pengaduan.
- Kode lama (Vue, tagihan, pembayaran) disimpan di folder `_backup_lama/` dan bisa dihapus bila tidak diperlukan.
