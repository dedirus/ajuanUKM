# Workspace Safety & Scope Guidelines: Ajuan_UKM_Agent

Dokumen aturan ini aktif otomatis untuk membatasi ruang lingkup kerja dan menjaga keamanan eksekusi pada proyek **Ajuan_UKM_Agent**.

## 1. Batasan Lingkup Proyek (Workspace Boundary)
- **Nama Proyek:** AjuanUKM (Sistem Informasi Pengajuan Kegiatan Mahasiswa & LPJ UKM Unpad)
- **Direktori Utama:** `c:\Applications\ServBay\www\Ajuan_UKM_Agent`
- **Remote Git Resmi:**
  - GitHub: `https://github.com/dedirus/ajuanUKM.git`
  - GitLab Unpad: `https://git.unpad.dev/unpad/units/dirkema/ajuanukm.git`

---

## 2. Perlindungan Lintas Proyek (Cross-Project Guard & Notification)
Agen **WAJIB** menjalankan verifikasi batas proyek sebelum melakukan eksekusi perintah:

1. **Verifikasi Keberadaan Berkas di Proyek Ini:**
   - Seluruh berkas, fitur, route, controller, view, dan model yang diminta pengguna **harus dipastikan berada di dalam** direktori `c:\Applications\ServBay\www\Ajuan_UKM_Agent`.
   - Agen **DILARANG** secara otomatis mencari, memodifikasi, meng-commit, atau me-push ke folder proyek lain di luar repositori ini (seperti `SpjOrmawa`, `AjuanKema`, `kemaweb_agent`, dll.).

2. **Notifikasi Peringatan Sebelum Eksekusi:**
   - Jika pengguna memberikan perintah yang merujuk pada berkas, fitur, tabel, atau screenshot yang **tidak ditemukan** di proyek `Ajuan_UKM_Agent`:
     - **HENTIKAN EKSEKUSI SEKETIKA.**
     - Berikan pesan peringatan konfirmasi kepada pengguna secara eksplisit:
       > ⚠️ **Peringatan Batasan Folder Proyek**: Berkas atau fitur yang Anda sebutkan tidak ditemukan di proyek **Ajuan_UKM_Agent**. Apakah perintah ini ditujukan untuk proyek lain (misalnya *SpjOrmawa*)? Mohon konfirmasikan terlebih dahulu sebelum tindakan dijalankan.
     - Jangan mengeksekusi perubahan apa pun sampai pengguna memberikan konfirmasi yang jelas.

3. **Batasan Operasi Git:**
   - Operasi `git add`, `git commit`, dan `git push` hanya boleh dijalankan di dalam repositori `Ajuan_UKM_Agent` menuju remote `origin` resmi.
