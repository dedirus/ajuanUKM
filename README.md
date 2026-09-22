# AjuanUKM - Sistem Informasi Pengajuan, Verifikasi Kegiatan & LPJ SIMKATMAWA

[![Laravel](https://img.shields.io/badge/Laravel-10.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![Alpine.js](https://img.shields.io/badge/Alpine.js-3.x-8BC0D0?style=for-the-badge&logo=alpine.js&logoColor=white)](https://alpinejs.dev)
[![Tests](https://img.shields.io/badge/Tests-67%20Passed-success?style=for-the-badge&logo=githubactions&logoColor=white)](#-pengujian-otomatis-automated-tests)

**AjuanUKM** adalah platform berbasis web untuk digitalisasi, transparansi, dan standardisasi alur pengajuan kegiatan mahasiswa berbasis **Unit Kegiatan Mahasiswa (UKM)**, verifikasi berjenjang oleh **Dosen Pembina** dan **Biro Kemahasiswaan**, serta pelaporan **LPJ Pasca-Kegiatan** yang terintegrasi langsung dengan borang pemeringkatan nasional **SIMKATMAWA** Kemendikbudristek.

---

## 🌟 Fitur Unggulan

### 1. Alur Persetujuan Berjenjang (Multi-Role Approval Workflow)
- **Role Mahasiswa (Pengusul UKM)**: Membuat proposal kegiatan, mengisi tabel rincian anggaran biaya (RAB) dinamis, mengunggah proposal (PDF/Word), serta memantau status persetujuan. Mahasiswa dapat mengedit usulan & detail RAB kapan saja selama belum disetujui final.
- **Role Dosen Pembina UKM**: Memeriksa kelayakan usulan kegiatan khusus UKM binaannya, memberikan catatan/arahan, meminta revisi, atau memberikan persetujuan (*approval*).
- **Role Biro Kemahasiswaan**: Memeriksa usulan seluruh UKM, menyesuaikan/mengedit baris item kebutuhan RAB, memberikan disposisi persetujuan akhir (*final approval*), serta mengelola master data.

### 2. Pengelolaan RAB & Master Satuan Fleksibel
- **Master Satuan Kebutuhan**: Pengelolaan satuan barang/jasa dinamis (Unit, Orang, Paket, Hari, Bulan, Lembar, dll.).
- **Durasi & Satuan Durasi**: Perhitungan subtotal otomatis secara realtime:  
  $$\text{Subtotal} = \text{Harga Satuan} \times \text{Jumlah} \times \text{Durasi}$$
- **Mode Edit RAB Kemahasiswaan**: Verifikator universitas dapat mengubah harga satuan, kuantitas, durasi, menambah baris baru, maupun mencoret item anggaran yang tidak disetujui secara langsung di antarmuka web.

### 3. Laporan Pertanggungjawaban (LPJ) & Data SIMKATMAWA
- **Pencatatan Multi-Prestasi / Multi-Juara**: Perekaman banyak capaian juara dalam satu kegiatan perlombaan (misal: *Juara 1 Gaya Dada*, *Juara 2 Gaya Bebas*, *Juara 3 Estafet*) dilengkapi nomor lomba, tingkat wilayah (*Internasional s.d. Internal PT*), dan nama mahasiswa peraih medali 🥇🥈🥉.
- **Pendataan Peserta SIMKATMAWA**: Pencatatan Nama Lengkap, NIM/NPM, dan Peran kontingen mahasiswa untuk pelaporan borang akreditasi/pemeringkatan universitas.
- **Kalkulasi Realisasi Anggaran**: Perhitungan otomatis penghematan/efisiensi anggaran riil vs pagu yang disetujui.
- **Unggah 4 Berkas Dokumentasi**: Sertifikat kejuaraan, foto dokumentasi kegiatan, foto penyerahan piala, dan surat undangan resmi.

### 4. Log & Visual Linimasa Riwayat Usulan Kegiatan
- **Audit Trail Komprehensif**: Pencatatan jejak riwayat secara otomatis setiap kali usulan diajukan, diedit, disetujui, diminta revisi, disesuaikan RAB-nya, hingga LPJ diverifikasi.
- **Komponen Linimasa Vertikal (`<x-timeline-riwayat />`)**: Tampilan alur visual kronologis dengan status peralihan, indikator *"Status Terkini"*, waktu lengkap WIB, dan kutipan catatan verifikator.

### 5. Tab Menu Interaktif Kemahasiswaan
- Pemisahan antarmuka halaman verifikasi (`/kemahasiswaan/pengajuan/{id}`) menjadi dua tab menu responsif:
  - 📋 **Tab 1: `Verifikasi & Penyesuaian Anggaran`**: Checklist RAB, mode edit kebutuhan, dan formulir persetujuan.
  - ⏱️ **Tab 2: `Riwayat Jejak & Aktivitas Usulan`**: Linimasa lengkap kronologi perjalanan usulan.

### 6. Dokumen Resmi & Rekapitulasi Laporan
- **Cetak Lembar Pengesahan PDF (A4)**: Dilengkapi QR-code verifikasi digital, rincian anggaran per akun, dan kolom tanda tangan berjenjang.
- **Dashboard & Ekspor Excel (.xlsx)**: Rekapitulasi realisasi anggaran per UKM, multi-sheet export, filter periode tanggal, dan metrik visual capaian kegiatan.

---

## 👥 Kredensial Uji Coba Default

Semua akun menggunakan kata sandi standar: **`password`**

| Peran (Role) | Nama Akun | Email Login | Cakupan Hak Akses |
|---|---|---|---|
| **Kemahasiswaan** | Biro Kemahasiswaan & Alumni | `kemahasiswaan@univ.ac.id` | Administrator Universitas, Verifikasi Final, Master Data |
| **Pembina** | Dr. Ir. Hendra Setiawan, M.T. | `pembina.robotika@univ.ac.id` | Pembina UKM Robotika & Otomasi |
| **Pembina** | Rina Wijayanti, M.Pd. | `pembina.musik@univ.ac.id` | Pembina UKM Seni Musik Kampus |
| **Pembina** | Bambang Sudibyo, M.Or. | `pembina.basket@univ.ac.id` | Pembina UKM Bola Basket Patriot |
| **Mahasiswa** | Ahmad Fauzi | `mahasiswa.robotika@univ.ac.id` | Pengusul UKM Robotika & Otomasi |
| **Mahasiswa** | Putri Ayu | `mahasiswa.musik@univ.ac.id` | Pengusul UKM Seni Musik Kampus |
| **Mahasiswa** | Dimas Arya | `mahasiswa.basket@univ.ac.id` | Pengusul UKM Bola Basket Patriot |

*Tersedia juga fitur 1-Click Quick Login Switcher pada halaman login untuk mempermudah demonstrasi dan evaluasi peran.*

---

## 🚀 Panduan Instalasi & Menjalankan Aplikasi

### Kebutuhan Sistem (Prerequisites)
- PHP >= 8.1 (Disarankan PHP 8.2 / 8.4)
- Composer >= 2.x
- Node.js >= 18.x & NPM
- MySQL >= 8.0

### Langkah Instalasi
```bash
# 1. Clone repositori
git clone https://github.com/dedirus/ajuanUKM.git
cd ajuanUKM

# 2. Pasang dependensi PHP
composer install

# 3. Pasang dependensi Frontend (JavaScript/CSS)
npm install

# 4. Salin berkas lingkungan (.env) dan generate application key
cp .env.example .env
php artisan key:generate

# 5. Konfigurasikan koneksi database di berkas .env
# DB_DATABASE=ajuan_ukm
# DB_USERNAME=root
# DB_PASSWORD=

# 6. Jalankan migrasi dan seeder
php artisan migrate --seed

# 7. Buat symbolic link ke folder penyimpanan berkas
php artisan storage:link

# 8. Kompilasi aset frontend
npm run build

# 9. Jalankan server lokal
php artisan serve
```

Akses aplikasi di browser melalui: **`http://127.0.0.1:8000`**

---

## 🧪 Pengujian Otomatis (Automated Tests)

Aplikasi memiliki rangkaian pengujian terotomatisasi (*Feature & Unit Tests*) komprehensif:

```bash
php artisan test
```

### Hasil Uji Terkini:
```
PASS  Tests\Feature\AjuanUKMWorkflowTest (20 passed)
PASS  Tests\Feature\RiwayatKegiatanTest (6 passed)
PASS  Tests\Feature\LpjWorkflowTest (8 passed)
PASS  Tests\Feature\MasterSatuanTest (8 passed)
PASS  Tests\Feature\Auth\... (18 passed)
PASS  Tests\Feature\ProfileTest (5 passed)
PASS  Tests\Unit\... (2 passed)

Total: 67 passed (429 assertions)
```

---

## 📄 Lisensi
Aplikasi ini dikembangkan di bawah lisensi open-source [MIT License](LICENSE).
