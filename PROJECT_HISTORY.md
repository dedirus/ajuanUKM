# Riwayat Pengembangan Projek: AjuanUKM (Project History)

Dokumen ini mencatat seluruh riwayat inisialisasi, rancangan arsitektur, implementasi alur kerja, pengujian, dan panduan operasional projek **AjuanUKM**.

---

## 1. Ringkasan Projek (Project Overview)

- **Nama Projek**: AjuanUKM
- **Deskripsi**: Sistem Informasi Pengajuan dan Verifikasi Kegiatan Mahasiswa berbasis Unit Kegiatan Mahasiswa (UKM) dengan persetujuan berjenjang.
- **Framework**: Laravel 10 (`laravel/framework: ^10.10`)
- **Bahasa Pemrograman**: PHP 8.4.20
- **Database**: MySQL 8.4.9 (`ajuan_ukm`)
- **Frontend Stack**: Blade Template Engine, Tailwind CSS, Alpine.js, Vite
- **Environment**: ServBay Local Web Server (`c:\Applications\ServBay\www\AjuanUKM`)
- **Waktu Inisialisasi**: 10 September 2026

---

## 2. Alur Kerja Sistem (Business Workflow)

Aplikasi mengimplementasikan 3 peran pengguna (*Role-Based Access Control*):

```mermaid
flowchart TD
    subgraph RoleMahasiswa["1. Role: Mahasiswa (Perwakilan UKM)"]
        M1[Login mewakili UKM tertentu] --> M2[Form Usulan: Nama Kegiatan & Tanggal]
        M2 --> M3[Upload Dokumen Proposal/TOR Doc/PDF]
        M3 --> M4[CRUD Tabel Detail Kebutuhan Kegiatan RAB]
        M4 --> M5[Keterangan: Read-only 'Menunggu Kemahasiswaan']
        M5 --> M6[Simpan Pengajuan Kegiatan]
    end

    subgraph RolePembina["2. Role: Pembina UKM"]
        P1[Login Pembina UKM binaan] --> P2[Tabel Ajuan Kegiatan UKM terkait]
        P2 --> P3[Klik link Detail pada kolom Keterangan]
        P3 --> P4[Periksa Dokumen & Tabel Detail Kegiatan]
        P4 --> P5[Input Catatan Pembina & Tekan Tombol Disetujui]
    end

    subgraph RoleKemahasiswaan["3. Role: Kemahasiswaan (Universitas)"]
        K1[Login Biro Kemahasiswaan] --> K2[Tabel Ajuan Kegiatan Seluruh UKM]
        K2 --> K3[Menu Filter: Nama UKM & Tanggal Kegiatan]
        K3 --> K4[Buka Detail Ajuan Terpilih]
        K4 --> K5[Checklist Keterangan: Disetujui / Tidak Disetujui tiap item]
        K5 --> K6[Simpan Status Checklist Detail]
        K6 --> K7[Tekan Tombol Disetujui Akhir untuk Keseluruhan Ajuan]
    end

    M6 --> P2
    P5 --> K2
```

---

## 3. Tahapan & Riwayat Implementasi (Milestones)

### Fase 1: Riset Environment & Database Setup
- Memeriksa ketersediaan tool lokal ServBay: PHP 8.4, Composer 2.9, Node.js 23.11, MySQL 8.4.
- Membuat database MySQL `ajuan_ukm` dengan kredensial ServBay (`root:root`, host `127.0.0.1:3306`).
- Konfigurasi file `.env` dengan nama aplikasi `AjuanUKM` dan koneksi database MySQL.

### Fase 2: Inisialisasi Laravel 10 & Frontend Scaffolding
- Membuat skeleton project Laravel 10 menggunakan Composer.
- Memasang Laravel Breeze untuk modul autentikasi Blade.
- Memasang dependensi Node (`tailwindcss`, `@tailwindcss/forms`, `alpinejs`, `vite`).
- Menjalankan `php artisan storage:link` untuk akses publik file proposal di `storage/app/public`.

### Fase 3: Perancangan Skema Database (Database Migrations)
Dibuat 4 berkas migrasi:
1. `2024_09_10_070001_create_ukms_table.php`:
   - Menyimpan data master UKM (`nama_ukm`, `kode`, `kategori`, `deskripsi`, `pembina_nama`).
2. `2024_09_10_070002_add_role_and_ukm_id_to_users_table.php`:
   - Menambahkan kolom `role` (`mahasiswa`, `pembina`, `kemahasiswaan`), `ukm_id` (foreign key ke `ukms`), `nim_nip`, `no_hp`.
3. `2024_09_10_070003_create_ajuan_kegiatans_table.php`:
   - Header pengajuan kegiatan: `user_id`, `ukm_id`, `nama_kegiatan`, `tanggal_kegiatan`, `dokumen_proposal`, `dokumen_nama_asli`, `total_anggaran`, `status_pembina`, `catatan_pembina`, `verified_pembina_at`, `status_kemahasiswaan`, `catatan_kemahasiswaan`, `verified_kemahasiswaan_at`, `status_akhir`.
4. `2024_09_10_070004_create_detail_kegiatans_table.php`:
   - Rincian kebutuhan anggaran (RAB): `ajuan_kegiatan_id`, `no_urut`, `nama_akun`, `nama_barang`, `harga_satuan`, `jumlah`, `total`, `status_keterangan` (`belum_diverifikasi`, `disetujui`, `tidak_disetujui`), `catatan`.

### Fase 4: Eloquent Models & Relasi
- `App\Models\Ukm`: Relasi `hasMany` ke `User` dan `AjuanKegiatan`.
- `App\Models\User`: Relasi `belongsTo` ke `Ukm`, `hasMany` ke `AjuanKegiatan`, helper role `isMahasiswa()`, `isPembina()`, `isKemahasiswaan()`.
- `App\Models\AjuanKegiatan`: Relasi `belongsTo` ke `User`, `Ukm`, `pembinaVerifier`, `kemahasiswaanVerifier`, dan `hasMany` ke `DetailKegiatan`.
- `App\Models\DetailKegiatan`: Relasi `belongsTo` ke `AjuanKegiatan`.

### Fase 5: Middleware & Kontrol Akses Berbasis Peran
- Dibuat middleware `App\Http\Middleware\EnsureUserHasRole` (`role:mahasiswa,pembina,kemahasiswaan`).
- Didaftarkan alias `'role'` pada `app/Http/Kernel.php`.
- Dibuat `DashboardController` untuk mengarahkan pengguna secara dinamis ke dashboard perannya masing-masing saat login.

### Fase 6: Controllers & Tampilan UI (Blade + Tailwind + Alpine.js)
1. **Mahasiswa**:
   - `MahasiswaController`: method `index`, `create`, `store`, `show`, `downloadDokumen`.
   - `resources/views/mahasiswa/index.blade.php`: Tabel riwayat ajuan UKM miliknya dengan status Pembina & Kemahasiswaan.
   - `resources/views/mahasiswa/create.blade.php`: Form pengajuan + upload dokumen proposal (.pdf/.doc/.docx) + Alpine.js dynamic CRUD table dengan live subtotal/grand total dan status Keterangan read-only.
   - `resources/views/mahasiswa/show.blade.php`: Halaman detail pengajuan, timeline verifikasi, dan tabel detail item RAB.
2. **Pembina**:
   - `PembinaController`: method `index`, `show`, `verify`, `downloadDokumen`.
   - `resources/views/pembina/index.blade.php`: Tabel ajuan khusus UKM yang dibina dengan kolom Nama Kegiatan, Tanggal, Total Ajuan, Keterangan (berisi link detail), Catatan, Aksi.
   - `resources/views/pembina/show.blade.php`: Tampilan detail usulan + tabel detail kebutuhan + form catatan dan tombol persetujuan *"✓ Disetujui"*.
3. **Kemahasiswaan**:
   - `KemahasiswaanController`: method `index`, `show`, `updateDetailItems`, `approveOverall`, `downloadDokumen`.
   - `resources/views/kemahasiswaan/index.blade.php`: Tabel seluruh ajuan UKM lengkap dengan menu filter Nama UKM, Tanggal Kegiatan, Pencarian, kolom Verifikasi Pembina, Keterangan, Catatan, dan tombol Detail.
   - `resources/views/kemahasiswaan/show.blade.php`: Pemeriksaan tabel detail item dengan checklist radio *"✓ Disetujui"* / *"✗ Tdk Disetujui"* per baris item, tombol *"Centang Semua Disetujui"*, serta tombol utama *"✓ Disetujui (Setujui Seluruh Usulan Kegiatan)"*.
4. **Halaman Login Interaktif**:
   - `resources/views/auth/login.blade.php`: Dilengkapi panel pemilih akun cepat (1-Click Quick Demo Login Switcher).

### Fase 7: Seeder & Data Simulasi Realistis
- Dibuat `database/seeders/AjuanUKMSeeder.php`:
  - 4 Data Master UKM (Robotika, Seni Musik, Bola Basket, MAPALA).
  - 7 Akun Pengguna (Mahasiswa, Pembina, Kemahasiswaan).
  - File dummy PDF proposal di storage publik (`dokumen_proposal/sample_proposal.pdf`).
  - 3 Sampel Ajuan Kegiatan dalam tahapan status berbeda:
    1. *Workshop IoT & Robotika* (UKM Robotika): Menunggu Verifikasi Pembina.
    2. *Festival Musik Akustik* (UKM Seni Musik): Telah Disetujui Pembina, Siap Diverifikasi Kemahasiswaan.
    3. *Piala Rektor Basket 3x3* (UKM Basket): Telah Disetujui Keseluruhan.

### Fase 8: Pengujian & Validasi Awal
- Dibuat automated feature test `tests/Feature/AjuanUKMWorkflowTest.php` yang memvalidasi:
  1. Mahasiswa berhasil input pengajuan & CRUD detail kebutuhan.
  2. Pembina berhasil memeriksa dan menyetujui ajuan UKM binaannya.
  3. Kemahasiswaan berhasil memfilter UKM/tanggal, mencentang checklist detail item, dan menyetujui ajuan secara keseluruhan.
- Hasil pengujian: **28 tes lulus 100% (91 assertions)**.

### Fase 9: Penyempurnaan Prosedur - Alur Revisi Usulan (Feedback & Resubmission Loop)
- Menambahkan opsi **"⚠️ Minta Revisi"** pada panel verifikasi Pembina (`revisi_pembina`) dan Kemahasiswaan (`revisi_kemahasiswaan`) dengan catatan revisi wajib diisi.
- Menambahkan form interaktif `mahasiswa.edit` (`mahasiswa/edit.blade.php`) bagi mahasiswa untuk:
  - Melihat catatan revisi dari verifikator.
  - Memperbarui data kegiatan & mengganti proposal jika diperlukan.
  - Memodifikasi, menambah, atau membuang baris item kebutuhan kegiatan pada tabel RAB.
  - Menghitung ulang total anggaran secara real-time dan melacak `revisi_count`.
- Tombol **"Kirim Ulang Usulan (Resubmit)"**:
  - Jika revisi dari Pembina $\rightarrow$ status dikembalikan ke `menunggu_pembina`.
  - Jika revisi dari Kemahasiswaan $\rightarrow$ status langsung kembali ke `menunggu_kemahasiswaan`.
- Menambahkan 2 skenario uji end-to-end baru di `AjuanUKMWorkflowTest.php`:
  - `test_pembina_can_request_revision_and_student_can_resubmit()` [PASS]
  - `test_kemahasiswaan_can_request_revision_and_student_can_resubmit()` [PASS]
- Total pengujian meningkat menjadi: **30 tes lulus 100% (114 assertions)**.

### Fase 10: Penyempurnaan Prosedur - Modul Cetak Lembar Pengesahan PDF (Fitur No. 3)
- Memasang paket `barryvdh/laravel-dompdf:^3.1` untuk pembuatan dokumen PDF berstandar A4 portrait.
- Menerbitkan file konfigurasi `config/dompdf.php`.
- Membuat Controller `App\Http\Controllers\PengesahanPdfController`:
  - Mengatur otorisasi akses berjenjang (Mahasiswa & Pembina hanya dapat mengakses dokumen milik UKM-nya, Kemahasiswaan dapat mengakses seluruh UKM).
  - Format penomoran disposisi otomatis: `DISP/{KODE_UKM}/{TAHUN}/{ID_4DIGIT}`.
  - Perhitungan akumulasi anggaran yang disetujui secara otomatis.
  - Menghasilkan respon preview interaktif (*stream*) di tab browser atau unduh langsung (*download attachment*) dengan nama file `Lembar_Pengesahan_{NAMA_UKM}_{ID}.pdf`.
- Mendesain template cetak resmi `resources/views/pdf/lembar_pengesahan.blade.php`:
  1. Kop surat dinas universitas lengkap dengan logo dan garis ganda tebal-tipis.
  2. Judul dokumen resmi dan badge status keabsahan dokumen usulan kegiatan.
  3. Tabel identitas usulan kegiatan lengkap dengan pelacak revisi (`revisi_count`).
  4. Tabel Rincian Anggaran Biaya (RAB) terverifikasi dengan subtotal dan total biaya yang disetujui.
  5. Kotak catatan & rekomendasi verifikator (Pembina & Kemahasiswaan).
  6. Lembar pengesahan tanda tangan ganda berdampingan (Dosen Pembina UKM & Kepala Biro Kemahasiswaan) dilengkapi stempel/token keabsahan digital.
  7. Footer keabsahan sistem dan metadata cetak digital.
- Menambahkan tombol aksi **"🖨️ Cetak Pengesahan (PDF)"** pada:
  - Header halaman rincian Mahasiswa (`mahasiswa.show`), Pembina (`pembina.show`), dan Kemahasiswaan (`kemahasiswaan.show`).
  - Kolom aksi tabel daftar usulan Mahasiswa (`mahasiswa.index`), Pembina (`pembina.index`), dan Kemahasiswaan (`kemahasiswaan.index`).
- Mendaftarkan rute cetak pada `routes/web.php`:
  - `mahasiswa.cetak-pengesahan`
  - `pembina.cetak-pengesahan`
  - `kemahasiswaan.cetak-pengesahan`
- Menambahkan 2 automated feature test di `tests/Feature/AjuanUKMWorkflowTest.php`:
  - `test_authorized_users_can_generate_lembar_pengesahan_pdf()` [PASS]
  - `test_unauthorized_user_cannot_access_other_ukm_pdf()` [PASS]
- Total pengujian meningkat menjadi: **32 tes lulus 100% (125 assertions)**.

### Fase 11: Penyempurnaan Alur Perbaikan untuk Usulan Ditolak (Rejection Recovery & Resubmission)
- **Masalah**: Ketika usulan kegiatan ditolak (`status_akhir === 'ditolak'`) oleh Pembina maupun Kemahasiswaan disertai catatan alasan penolakan, helper `isPerluRevisi()` sebelumnya hanya mengecek status `revisi_pembina` dan `revisi_kemahasiswaan`, sehingga tombol **"Perbaiki Usulan (Revisi)"** dan banner instruksi perbaikan tidak muncul di antarmuka Mahasiswa.
- **Solusi & Perbaikan**:
  1. Memperbarui method `isPerluRevisi()`, `isRevisiPembina()`, dan `isRevisiKemahasiswaan()` pada `App\Models\AjuanKegiatan` untuk mendukung status `ditolak`, sehingga mahasiswa tetap dapat memperbaiki usulan kegiatan yang ditolak.
  2. Memperbarui `PembinaController::verify` dan `KemahasiswaanController::approveOverall` agar catatan penolakan selalu tersimpan ke kolom `catatan_revisi_terakhir`.
  3. Memperbarui tampilan `mahasiswa/show.blade.php`, `mahasiswa/index.blade.php`, dan `mahasiswa/edit.blade.php` dengan badge, banner instruksi, dan tombol aksi yang jelas: *"Perbaiki Usulan (Ditolak)"* / *"Alasan Penolakan & Arahan Perbaikan"*.
  4. Menambahkan test case `test_rejected_ajuan_can_be_revised_and_resubmitted_by_student()` di `AjuanUKMWorkflowTest.php`.
- Total pengujian meningkat menjadi: **33 tes lulus 100% (142 assertions)**.

### Fase 12: Menu Laporan, Statistik Realisasi Usulan per UKM & Ekspor Excel (.xlsx)
- Memasang paket `maatwebsite/excel:^3.1` yang terintegrasi dengan `phpoffice/phpspreadsheet`.
- Menerbitkan konfigurasi `config/excel.php`.
- Membuat Controller `App\Http\Controllers\LaporanKemahasiswaanController`:
  - `index`: Menghitung ringkasan KPI eksekutif universitas dan tabel rekapitulasi data agregasi seluruh UKM (total usulan, disetujui, dalam proses, ditolak, persentase realisasi, serapan dana disetujui).
  - `detail`: Menyajikan tampilan drill-down rincian kegiatan khusus untuk UKM tertentu.
  - `exportExcel`: Mengunduh file Excel multi-sheet (`.xlsx`) berstandar resmi.
- Membuat Export Class `App\Exports\LaporanRealisasiUkmExport`:
  - Sheet 1: *Rekapitulasi per UKM* (Data agregasi performa setiap UKM + baris total akumulasi universitas bergaya spreadsheet).
  - Sheet 2: *Rincian Seluruh Usulan* (Daftar terperinci seluruh ajuan kegiatan, tanggal, pengusul, status verifikasi pembina & kemahasiswaan, riwayat revisi, dan total anggaran).
- Merancang Blade Views:
  - `resources/views/kemahasiswaan/laporan/index.blade.php`: Dashboard KPI cards, toolbar filter/pencarian, tabel rekapitulasi realisasi per UKM dengan progress bar visual persentase, dan tombol drill-down.
  - `resources/views/kemahasiswaan/laporan/detail.blade.php`: Halaman rincian usulan UKM terpilih dengan metrik dan tabel daftar kegiatan.
- Menambahkan tautan menu **"Laporan & Statistik UKM"** di navigasi desktop & responsive mobile ([`navigation.blade.php`](file:///c:/Applications/ServBay/www/AjuanUKM/resources/views/layouts/navigation.blade.php)).
- Mendaftarkan rute `kemahasiswaan.laporan`, `kemahasiswaan.laporan.detail`, dan `kemahasiswaan.laporan.export` pada `routes/web.php`.
- Menambahkan 3 automated feature test di `tests/Feature/AjuanUKMWorkflowTest.php`:
  - `test_kemahasiswaan_can_access_laporan_dashboard_and_detail()` [PASS]
  - `test_kemahasiswaan_can_export_laporan_excel()` [PASS]
  - `test_unauthorized_users_cannot_access_laporan()` [PASS]
- Total pengujian meningkat menjadi: **36 tes lulus 100% (155 assertions)**.

### Fase 13: Manajemen Master Data UKM & Manajemen Pengguna (User Management)
- Membuat Controller `App\Http\Controllers\MasterUkmController`:
  - `index`: Daftar seluruh UKM dengan filter pencarian dan kategori, serta ringkasan metrik organisasi.
  - `create` & `store`: Penambahan UKM baru dengan kode unik uppercase, kategori, deskripsi, dan penugasan Dosen Pembina (pilih akun pembina terdaftar atau input manual).
  - `show`: Profil detail kelembagaan UKM, daftar dosen pembina terdaftar, daftar akun mahasiswa terdaftar, serta riwayat usulan kegiatan lengkap.
  - `edit` & `update`: Pembaruan profil UKM dan sinkronisasi penugasan pembina.
  - `destroy`: Penghapusan UKM dengan proteksi integritas data (mencegah penghapusan UKM yang telah memiliki riwayat usulan kegiatan).
- Membuat Controller `App\Http\Controllers\MasterUserController`:
  - `index`: Daftar pengguna sistem dengan filter peran (Mahasiswa, Pembina, Kemahasiswaan), filter UKM, pencarian teks, dan ringkasan metrik.
  - `create` & `store`: Pembuatan akun baru dengan validasi kata sandi, pemilihan peran, dan dropdown dinamis UKM.
  - `edit` & `update`: Pembaruan profil pengguna, hak akses, UKM, serta fasilitas opsional ubah/reset kata sandi baru.
  - `destroy`: Penghapusan akun pengguna dengan proteksi keamanan (mencegah penghapusan akun diri sendiri yang sedang aktif dan mencegah penghapusan akun mahasiswa yang memiliki riwayat usulan kegiatan).
- Merancang Blade Views:
  - `resources/views/kemahasiswaan/ukm/index.blade.php`: Tabel master UKM, filter, badge kategori & pembina, serta tombol aksi.
  - `resources/views/kemahasiswaan/ukm/create.blade.php`: Form penambahan UKM baru.
  - `resources/views/kemahasiswaan/ukm/edit.blade.php`: Form edit data UKM.
  - `resources/views/kemahasiswaan/ukm/show.blade.php`: Halaman profil UKM, anggota, dan riwayat kegiatan.
  - `resources/views/kemahasiswaan/users/index.blade.php`: Tabel pengguna, filter peran & UKM, serta tombol aksi.
  - `resources/views/kemahasiswaan/users/create.blade.php`: Form registrasi pengguna baru dengan toggle dinamis.
  - `resources/views/kemahasiswaan/users/edit.blade.php`: Form edit data pengguna dan reset password.
- Menambahkan tautan menu **"Master UKM"** dan **"Data Pengguna"** di bilah navigasi utama Kemahasiswaan ([`navigation.blade.php`](file:///c:/Applications/ServBay/www/AjuanUKM/resources/views/layouts/navigation.blade.php)).
- Mendaftarkan rute resource `kemahasiswaan.ukm.*` dan `kemahasiswaan.users.*` pada `routes/web.php` dengan middleware `role:kemahasiswaan`.
- Menambahkan 3 skenario test komprehensif di `tests/Feature/AjuanUKMWorkflowTest.php`:
  - `test_kemahasiswaan_can_crud_master_ukm()` [PASS]
  - `test_kemahasiswaan_can_crud_users_and_safety_checks()` [PASS]
  - `test_unauthorized_users_cannot_access_master_data()` [PASS]
- Total automated tests meningkat menjadi: **39 tes lulus 100% (197 assertions)**.
- Seluruh repositori proyek berhasil diunggah ke GitHub: [https://github.com/dedirus/ajuanUKM](https://github.com/dedirus/ajuanUKM) pada branch `main`.

### Fase 9: Penataan Navigasi Bilah Menu 'Setting'
- Merapikan navigasi Kemahasiswaan dengan menggabungkan Master UKM, Master Satuan, dan User Management ke dalam dropdown **"Setting"**.
- Menjadikan navbar lebih ringkas: *Kelola Ajuan*, *Laporan & Statistik*, dan *Setting*.

### Fase 10: Master Satuan RAB & Durasi Kebutuhan Kegiatan
- Membuat tabel `satuans` dengan seeder awal satuan umum (Unit, Orang, Paket, Hari, Bulan, dll.).
- Menambahkan kolom `satuan`, `durasi`, dan `satuan_durasi` pada tabel `detail_kegiatans`.
- Menghadirkan CRUD Master Satuan untuk Kemahasiswaan (`MasterSatuanController`).
- Menghubungkan dropdown satuan dinamis pada formulir pengajuan Mahasiswa dan verifikasi Kemahasiswaan.

### Fase 11: Fleksibilitas Edit Usulan & Detail RAB Sebelum Disetujui (Role Mahasiswa)
- Memungkinkan mahasiswa mengedit proposal dan mengubah rincian baris RAB selama usulan belum berstatus disetujui final (`status_akhir != 'disetujui'`).
- Status usulan tetap berjalan lancar dan total anggaran diperbarui secara otomatis.

### Fase 12: Pilihan Dropdown "Jenis Kegiatan"
- Menambahkan kolom `jenis_kegiatan` (`Lomba`, `Pelatihan`, `Lainnya`) pada tabel `ajuan_kegiatans`.
- Ditampilkan pada formulir pengajuan/edit dan badge penanda pada detail usulan serta lembar pengesahan.

### Fase 13: Kemahasiswaan Dapat Mengedit & Memperbarui Tabel RAB
- Menambahkan mode edit interaktif (Alpine.js) pada halaman verifikasi Kemahasiswaan (`/kemahasiswaan/pengajuan/{id}`).
- Kemahasiswaan dapat mengubah pos akun, nama barang, harga satuan, durasi, menghapus baris, atau menambah baris pos kebutuhan baru secara realtime.

### Fase 14: Laporan Pertanggungjawaban (LPJ) Pasca-Kegiatan & Data SIMKATMAWA
- Membuat tabel `lpj_kegiatans` dan `lpj_pesertas` untuk mencatat pelaporan pasca-pelaksanaan kegiatan.
- Tombol **"Lapor LPJ"** muncul otomatis setelah kegiatan selesai dan tanggal lewat.
- Pendataan nama mahasiswa, NIM, dan peran untuk borang pemeringkatan nasional SIMKATMAWA Kemendikbudristek.
- Menghadirkan kalkulator realisasi anggaran terpakai vs pagu serta 4 berkas unggahan bukti kegiatan opsional.

### Fase 15: Pencatatan Multi-Prestasi / Multi-Juara pada LPJ Kegiatan
- Membuat tabel relasional `lpj_prestasies` untuk mencatat banyak raihan juara dalam satu kegiatan (misal kejuaraan Renang: Juara 1 Gaya Dada, Juara 2 Gaya Bebas, Juara 3 Estafet).
- Formulir interaktif Alpine.js dengan tombol `+ Tambah Prestasi / Juara` dan penghapusan baris.
- Showcase kartu perolehan medali (🥇 Emas, 🥈 Perak, 🥉 Perunggu) pada halaman detail Mahasiswa, Pembina, dan Kemahasiswaan.

### Fase 16: Log & Visual Timeline Riwayat Jejak Usulan Kegiatan
- Membuat tabel `riwayat_kegiatans` dan model `RiwayatKegiatan` untuk mencatat audit trail aktivitas usulan secara kronologis.
- Logging hooks terpasang otomatis pada `MahasiswaController`, `PembinaController`, dan `KemahasiswaanController`.
- Komponen Blade terpadu `<x-timeline-riwayat />` menyajikan linimasa vertikal lengkap dengan indikator status terkini, badge pelaku, waktu lengkap WIB, dan kutipan catatan verifikator.
- Dilengkapi otomatisasi backfill data log pada data usulan lama saat migrasi dijalankan.

### Fase 17: Tab Menu Interaktif pada Halaman Pemeriksaan Kemahasiswaan
- Memisahkan area kerja verifikasi pada `/kemahasiswaan/pengajuan/{id}` menjadi dua tab menu:
  1. **Tab 1: `Verifikasi & Penyesuaian Anggaran`**: Tabel checklist RAB, edit anggaran, form persetujuan akhir, dan verifikasi LPJ.
  2. **Tab 2: `Riwayat Jejak & Aktivitas Usulan`**: Kartu linimasa vertikal jejak aktivitas usulan.
- Perpindahan tab instan tanpa reload (Alpine.js) dan mendukung URL parameter/hash (`#riwayat` / `?tab=riwayat`).

### Fase 18: Refactoring Validasi Data dengan Dedicated Form Requests (Fase Awal)
- Membuat berkas Form Request terdedikasi:
  1. `App\Http\Requests\StoreAjuanKegiatanRequest`: Validasi pengajuan usulan baru dan detail baris RAB (dokumen proposal wajib diunggah).
  2. `App\Http\Requests\UpdateAjuanKegiatanRequest`: Validasi pembaruan/revisi usulan (dokumen proposal bersifat opsional).
  3. `App\Http\Requests\ReviewAjuanRequest`: Validasi telaah Pembina (status `disetujui`, `revisi`, atau `ditolak`; catatan wajib saat status revisi).
- Memindahkan seluruh aturan `rules()` dan pesan bahasa Indonesia kustom `messages()` dari controller ke dalam masing-masing Form Request.
- Mengatur `authorize(): bool` mengembalikan `true` karena otorisasi hak akses peran ditangani oleh middleware `EnsureUserHasRole`.
- Menyuntikkan Form Request ke dalam `MahasiswaController::store`, `MahasiswaController::update`, dan `PembinaController::verify`.

### Fase 19: Implementasi Penuh Rekomendasi Audit Kode, Service Layer & Optimasi Database
- **Abstraksi Service Layer (`app/Services`)**:
  - `App\Services\AjuanKegiatanService`: Memisahkan business logic manajemen usulan (store, update, verifikasi Pembina, update detail RAB, dan persetujuan Kemahasiswaan) dari controller.
  - `App\Services\LpjKegiatanService`: Memisahkan business logic pelaporan LPJ (sinkronisasi kontingen SIMKATMAWA, raihan multi-prestasi, penanganan 4 berkas unggahan, dan verifikasi/revisi LPJ) dari controller.
  - Mengonversi `MahasiswaController`, `PembinaController`, dan `KemahasiswaanController` menjadi *skinny controllers* yang terisolasi rapi.
- **Dedicated Form Requests 100% Coverage**:
  - Menuntaskan eksekusi Form Request untuk seluruh endpoint aplikasi yang tersisa:
    - Mahasiswa: `StoreLpjRequest`
    - Kemahasiswaan: `UpdateRabDetailItemsRequest`, `ApproveAjuanRequest`, `VerifyLpjRequest`
    - Master Data: `StoreMasterUkmRequest`, `UpdateMasterUkmRequest`, `StoreMasterSatuanRequest`, `UpdateMasterSatuanRequest`, `StoreMasterUserRequest`, `UpdateMasterUserRequest`
- **Optimasi Database dengan Indeks Komposit**:
  - Migrasi `2026_10_16_035100_add_composite_indexes_to_ajuan_kegiatans_table.php` menambahkan 3 composite index pada tabel `ajuan_kegiatans`:
    - `idx_ajuan_ukm_status` pada `(ukm_id, status_akhir)`
    - `idx_ajuan_tgl_status` pada `(tanggal_kegiatan, status_akhir)`
    - `idx_ajuan_user_status` pada `(user_id, status_akhir)`
- **Verifikasi Rangkaian Uji Otomatis**:
  - Menjalankan seluruh rangkaian tes: **67 tests lolos 100% (429 assertions)**.

---


## 4. Daftar Akun Uji Coba (Credentials)

Semua akun menggunakan password standar: `password`

| Peran (Role) | Nama Akun | Email | UKM Terkait |
|---|---|---|---|
| **Kemahasiswaan** | Biro Kemahasiswaan & Alumni | `kemahasiswaan@univ.ac.id` | Seluruh UKM (Admin Universitas) |
| **Pembina** | Dr. Ir. Hendra Setiawan, M.T. | `pembina.robotika@univ.ac.id` | UKM Robotika & Otomasi |
| **Pembina** | Rina Wijayanti, M.Pd. | `pembina.musik@univ.ac.id` | UKM Seni Musik Kampus |
| **Pembina** | Bambang Sudibyo, M.Or. | `pembina.basket@univ.ac.id` | UKM Bola Basket Patriot |
| **Mahasiswa** | Ahmad Fauzi | `mahasiswa.robotika@univ.ac.id` | UKM Robotika & Otomasi |
| **Mahasiswa** | Putri Ayu | `mahasiswa.musik@univ.ac.id` | UKM Seni Musik Kampus |
| **Mahasiswa** | Dimas Arya | `mahasiswa.basket@univ.ac.id` | UKM Bola Basket Patriot |

---

## 5. Panduan Menjalankan & Perawatan Aplikasi

### Menjalankan Server Development
```powershell
cd c:\Applications\ServBay\www\AjuanUKM
php artisan serve
```
Buka browser di: `http://127.0.0.1:8000`

### Menjalankan Asset Vite (Development Mode)
```powershell
npm run dev
```

### Build Ulang Asset (Production Mode)
```powershell
npm run build
```

### Reset & Seed Ulang Database
```powershell
php artisan migrate:fresh --seed
```

### Menjalankan Seluruh Automated Test
```powershell
php artisan test
```

---

## 6. Struktur Direktori Utama

```
AjuanUKM/
├── app/
│   ├── Exports/
│   │   └── LaporanRealisasiUkmExport.php      # Multi-sheet Excel export (.xlsx)
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── DashboardController.php        # Pengarah dashboard sesuai role
│   │   │   ├── MahasiswaController.php        # Flow usulan mahasiswa, revisi, & submit LPJ
│   │   │   ├── PembinaController.php          # Flow verifikasi & catatan pembina
│   │   │   ├── KemahasiswaanController.php    # Flow verifikasi, edit RAB, approve final, & LPJ
│   │   │   ├── MasterSatuanController.php     # CRUD Master Satuan kebutuhan
│   │   │   ├── MasterUkmController.php        # CRUD Master UKM
│   │   │   ├── MasterUserController.php       # CRUD Pengguna & Hak Akses
│   │   │   ├── LaporanKemahasiswaanController.php # Laporan rekapitulasi UKM & statistik
│   │   ├── Requests/
│   │   │   ├── StoreAjuanKegiatanRequest.php  # Validasi input usulan kegiatan baru
│   │   │   ├── UpdateAjuanKegiatanRequest.php # Validasi input perbaikan/revisi usulan
│   │   │   ├── ReviewAjuanRequest.php         # Validasi telaah/persetujuan Pembina
│   │   │   ├── StoreLpjRequest.php            # Validasi input LPJ mahasiswa
│   │   │   ├── UpdateRabDetailItemsRequest.php# Validasi perubahan detail item RAB
│   │   │   ├── ApproveAjuanRequest.php        # Validasi persetujuan final Kemahasiswaan
│   │   │   ├── VerifyLpjRequest.php           # Validasi telaah & revisi LPJ Kemahasiswaan
│   │   │   ├── StoreMasterUkmRequest.php      # Validasi tambah master UKM
│   │   │   ├── UpdateMasterUkmRequest.php     # Validasi edit master UKM
│   │   │   ├── StoreMasterSatuanRequest.php   # Validasi tambah master Satuan
│   │   │   ├── UpdateMasterSatuanRequest.php  # Validasi edit master Satuan
│   │   │   ├── StoreMasterUserRequest.php     # Validasi tambah akun Pengguna
│   │   │   └── UpdateMasterUserRequest.php    # Validasi edit akun Pengguna
│   │   └── Middleware/
│   │       └── EnsureUserHasRole.php          # Otorisasi hak akses role
│   ├── Services/
│   │   ├── AjuanKegiatanService.php           # Business logic & workflow usulan kegiatan
│   │   └── LpjKegiatanService.php             # Business logic LPJ, berkas, & SIMKATMAWA
│   └── Models/
│       ├── Ukm.php                            # Model master UKM
│       ├── User.php                           # Model pengguna & relasi role
│       ├── Satuan.php                         # Model master satuan kebutuhan RAB
│       ├── AjuanKegiatan.php                  # Model header usulan kegiatan
│       ├── DetailKegiatan.php                 # Model detail item anggaran (RAB)
│       ├── LpjKegiatan.php                    # Model laporan pertanggungjawaban
│       ├── LpjPeserta.php                     # Model pendataan peserta SIMKATMAWA
│       ├── LpjPrestasi.php                    # Model multi-prestasi/juara lomba
│       └── RiwayatKegiatan.php                # Model audit trail jejak aktivitas kegiatan
├── database/
│   ├── migrations/                            # Migrasi skema database lengkap
│   └── seeders/
│       ├── DatabaseSeeder.php
│       ├── SatuanSeeder.php                   # Seeder master satuan
│       └── AjuanUKMSeeder.php                 # Seeder master UKM, user, & sampel ajuan
├── resources/
│   └── views/
│       ├── components/
│       │   └── timeline-riwayat.blade.php     # Komponen Blade visual linimasa jejak usulan
│       ├── layouts/                           # Layouts app & navigasi berbasis role
│       ├── mahasiswa/                         # Views mahasiswa (index, create, edit, show, lpj)
│       ├── pembina/                           # Views pembina (index, show)
│       ├── kemahasiswaan/                     # Views kemahasiswaan (index, show, satuan/, ukm/, users/, laporan/)
│       └── pdf/                               # Template dokumen resmi siap cetak
│           └── lembar_pengesahan.blade.php    # Template PDF Lembar Pengesahan & Disposisi
├── tests/Feature/
│   ├── AjuanUKMWorkflowTest.php               # End-to-end automated workflow test (20 passed)
│   ├── LpjWorkflowTest.php                    # Pengujian alur LPJ & SIMKATMAWA (8 passed)
│   ├── MasterSatuanTest.php                   # Pengujian Master Satuan (8 passed)
│   └── RiwayatKegiatanTest.php                # Pengujian Audit Trail Riwayat Kegiatan (6 passed)
├── PROJECT_HISTORY.md                         # Dokumentasi riwayat projek ini
└── README.md                                  # Dokumentasi utama proyek
```

