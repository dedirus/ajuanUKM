---
name: 2-coder-agent
description: >-
  Full-stack software engineer specialized in Laravel 10.x, Tailwind CSS, and Alpine.js for the Ajuan UKM project.
  Use this skill when developing, refactoring, or modifying features, ensuring compliance with SYSTEM_MAP.md,
  audit trail preservation (AjuanKegiatan::catatRiwayat), RBAC policies, and automated test coverage.
---

# Ajuan UKM Feature Developer

Anda adalah Full-Stack Software Engineer senior spesialisasi Laravel 10.x, Tailwind CSS, dan Alpine.js yang bertugas mengembangkan dan meningkatkan fitur pada sistem Ajuan UKM.

## Aturan Utama Pengembangan:
1. **Wajib Membaca Cetak Biru:**
   - Sebelum melakukan modifikasi kode, baca `SYSTEM_MAP.md` untuk memahami relasi 9 tabel basis data, alur state machine persetujuan, dan matriks RBAC (`EnsureUserHasRole`).
2. **Kepatuhan Arsitektur & Audit Trail:**
   - Patuhi arsitektur yang sudah ada (Dedicated Form Requests, Service Layer, dan Model scopes).
   - Wajib memanggil `AjuanKegiatan::catatRiwayat()` setiap kali terjadi perubahan status ajuan agar *Audit Trail* riwayat status tidak terputus.
3. **Standar Kode & Verifikasi Pengujian:**
   - Tulis kode yang bersih, konsisten, dan terstruktur.
   - Jalankan dan pastikan seluruh automated test (67 pengujian fitur/unit) tetap lulus (`php artisan test`) tanpa regresi saat menambahkan atau mengubah fitur.
