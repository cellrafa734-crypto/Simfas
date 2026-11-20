# SIMFAS Kampus — modul Laravel + MySQL

## Status proyek
Arsip ini berisi **modul SIMFAS**, bukan instalasi Laravel utuh. Laravel, Composer dependencies, folder `vendor`, file `artisan`, konfigurasi, dan bootstrap harus dibuat oleh Composer. File `setup-laragon.bat` membantu membuat proyek Laravel baru dan menyalin modul SIMFAS ke dalamnya.

Fitur yang sudah disediakan dalam modul:
- Registrasi mahasiswa, login, logout, dan pembatasan akses berdasarkan role.
- Dashboard ringkasan sesuai role.
- CRUD fasilitas oleh admin.
- Pengajuan, persetujuan, penolakan, dan pembatalan peminjaman.
- Pemeriksaan bentrok jadwal dan kapasitas unit saat pengajuan/persetujuan.
- Penilaian prioritas pemeliharaan menggunakan metode SAW (benefit/cost, bobot, matriks X, normalisasi R, dan ranking V).
- Seeder akun demo, fasilitas, kriteria, dan nilai SAW.

Belum termasuk: laporan kerusakan, pemeliharaan petugas, upload foto, CRUD kategori/kriteria, ekspor PDF, grafik, dan profil.

## Cara instal di Windows + Laragon
1. Ekstrak ZIP ini.
2. Pastikan Laragon (PHP dan MySQL) serta Composer sudah terpasang.
3. Jalankan `setup-laragon.bat` dengan internet aktif. Script membuat folder `SIMFAS_Kampus_App` di folder induk.
4. Buka Laragon, klik **Start All**.
5. Buka terminal pada folder `SIMFAS_Kampus_App`.
6. Buat database bernama `simfas` melalui phpMyAdmin.
7. Buka file `.env`, sesuaikan pengaturan database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=simfas
DB_USERNAME=root
DB_PASSWORD=
```

8. Jalankan perintah berikut satu per satu:

```bash
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

9. Buka `http://127.0.0.1:8000`.

## Akun demo
Semua akun demo memakai password `password123`:

| Role | Email |
|---|---|
| Admin | admin@simfas.test |
| Mahasiswa | mahasiswa@simfas.test |
| Organisasi | organisasi@simfas.test |
| Petugas | petugas@simfas.test |

Untuk keamanan, ganti password demo dan jangan gunakan akun demo pada server publik.

## Tes
Jalankan `php artisan test`. Pengujian memerlukan konfigurasi environment testing Laravel. Pastikan database testing terpisah dari database yang berisi data penting.

## Catatan aturan peminjaman
Peminjaman yang berstatus `disetujui` atau `sedang_digunakan` dihitung sebagai unit terpakai pada rentang waktu yang tumpang tindih. Pengajuan berstatus menunggu belum mengunci unit; pemeriksaan dilakukan kembali saat admin menyetujui. Status fasilitas pemeliharaan/tidak dapat digunakan menolak pengajuan baru.
