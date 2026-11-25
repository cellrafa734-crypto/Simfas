# SIMFAS Kampus

Sistem Informasi Manajemen Fasilitas Kampus: pengelolaan data fasilitas, pengajuan peminjaman dengan pengecekan bentrok jadwal, dan penentuan prioritas pemeliharaan memakai metode SAW (Simple Additive Weighting).

**Teknologi:** Laravel 13, PHP 8.3+, MySQL, Blade, Bootstrap 5.3.3 (CDN), font Inter.

## Fitur

- Registrasi mahasiswa, login, dan logout.
- Pembatasan akses berdasarkan role: admin, mahasiswa, organisasi, petugas.
- Dashboard ringkasan sesuai role (statistik, grafik peminjaman per bulan, komposisi status fasilitas, aktivitas terbaru).
- Fasilitas: semua role dapat melihat daftar; admin dapat menambah, mengubah, menghapus, dan mengunggah foto fasilitas.
- Peminjaman: mahasiswa dan organisasi mengajukan, melihat detail, dan membatalkan pengajuan; admin menyetujui atau menolak (dengan alasan).
- Pemeriksaan bentrok jadwal dan ketersediaan unit saat pengajuan dan saat persetujuan.
- Prioritas pemeliharaan dengan metode SAW (khusus admin): atur bobot kriteria, isi nilai fasilitas (1-5), lihat matriks keputusan X, matriks normalisasi R, dan ranking V.
- Seeder data demo: akun, fasilitas, kriteria, dan nilai SAW.

### Belum termasuk

- Modul petugas (tugas pemeliharaan). Akun petugas sudah ada, tetapi baru bisa melihat dashboard dan daftar fasilitas.
- Laporan kerusakan.
- Ekspor PDF.
- Tambah/ubah/hapus kriteria SAW (saat ini hanya bobot yang bisa diubah).

## Kebutuhan

- PHP 8.3 atau lebih baru, Composer, dan MySQL (contoh: Laragon atau XAMPP).
- Koneksi internet saat membuka aplikasi, karena Bootstrap, font, dan Chart.js dimuat dari CDN.
- Tidak perlu `npm`: tidak ada halaman yang memakai Vite.

## Cara instal

1. Ambil kode:

   ```bash
   git clone <url-repository>
   cd Simfas
   ```

2. Pasang dependensi:

   ```bash
   composer install
   ```

3. Salin file pengaturan:

   ```bash
   cp .env.example .env
   ```

   Di Windows (CMD): `copy .env.example .env`

4. Buat database kosong bernama `simfas` lewat phpMyAdmin.

5. Buka file `.env`, lalu ubah bagian database menjadi MySQL (bawaan `.env.example` masih SQLite):

   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=simfas
   DB_USERNAME=root
   DB_PASSWORD=
   ```

   Sesuaikan `DB_USERNAME` dan `DB_PASSWORD` dengan MySQL di komputermu.

6. Jalankan perintah berikut satu per satu:

   ```bash
   php artisan key:generate
   php artisan migrate --seed
   php artisan storage:link
   php artisan serve
   ```

7. Buka `http://127.0.0.1:8000`.

## Akun demo

Semua akun demo memakai password `password123`.

| Role | Email | Nama |
|---|---|---|
| Admin | admin@simfas.test | Admin SIMFAS |
| Mahasiswa | mahasiswa@simfas.test | Mahasiswa Demo |
| Organisasi | organisasi@simfas.test | BEM Demo |
| Petugas | petugas@simfas.test | Petugas Demo |

Akun baru yang mendaftar lewat halaman Register otomatis berperan sebagai mahasiswa. Jangan memakai akun demo di server publik.

## Menjalankan tes otomatis

```bash
php artisan test
```

Tes memakai SQLite di memori (lihat `phpunit.xml`), jadi tidak menyentuh database MySQL `simfas`. Pastikan ekstensi PHP `pdo_sqlite` aktif.

## Checklist tes manual login per role

Login di `/login` memakai akun demo di atas. Untuk memeriksa 403, ketik alamatnya langsung di browser setelah login. Setelah login, semua role masuk ke `/dashboard`.

| Role | Menu di sidebar | Halaman yang menghasilkan 403 |
|---|---|---|
| Admin | Dashboard, Fasilitas, Peminjaman, Prioritas SAW | `/borrowings/create` |
| Mahasiswa | Dashboard, Fasilitas, Peminjaman | `/saw`, `/facilities/create`, `/facilities/{id}/edit` |
| Organisasi | Dashboard, Fasilitas, Peminjaman | `/saw`, `/facilities/create`, `/facilities/{id}/edit` |
| Petugas | Dashboard, Fasilitas | `/borrowings`, `/borrowings/create`, `/saw`, `/facilities/create` |

Centang satu per satu:

- [ ] **Semua role:** salah password menampilkan "Email atau password salah."; tombol Keluar mengembalikan ke `/login`; membuka `/dashboard` tanpa login diarahkan ke `/login`.
- [ ] **Admin:** daftar Fasilitas punya tombol Tambah, Ubah, dan Hapus. Halaman Peminjaman tidak punya tombol "Ajukan" tetapi ada tombol Setujui dan Tolak untuk pengajuan berstatus menunggu. `/saw` terbuka dan dapat menyimpan bobot serta nilai.
- [ ] **Mahasiswa:** menu "Prioritas SAW" tidak tampil. Daftar Fasilitas tidak punya tombol Tambah, Ubah, Hapus. Bisa membuka `/borrowings/create` dan mengajukan peminjaman. Daftar Peminjaman hanya berisi pengajuan miliknya; membuka `/borrowings/{id}` milik orang lain menghasilkan 403.
- [ ] **Organisasi:** sama seperti mahasiswa.
- [ ] **Petugas:** menu Peminjaman dan Prioritas SAW tidak tampil. Dashboard menampilkan info bahwa modul tugas pemeliharaan petugas belum tersedia. Daftar Fasilitas hanya bisa dilihat.

## Aturan peminjaman

Peminjaman berstatus `disetujui` atau `sedang_digunakan` dihitung sebagai unit terpakai pada rentang waktu yang tumpang tindih. Pengajuan berstatus menunggu belum mengunci unit; pemeriksaan dilakukan lagi saat admin menyetujui. Fasilitas berstatus pemeliharaan atau tidak dapat digunakan menolak pengajuan baru.
