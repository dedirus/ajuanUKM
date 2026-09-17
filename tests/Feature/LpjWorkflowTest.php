<?php

namespace Tests\Feature;

use App\Models\AjuanKegiatan;
use App\Models\DetailKegiatan;
use App\Models\LpjKegiatan;
use App\Models\LpjPeserta;
use App\Models\Ukm;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LpjWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        Storage::fake('public');
    }

    /**
     * Test 1: Tombol LPJ hanya muncul ketika kegiatan sudah disetujui Pembina & Kemahasiswaan
     * serta tanggal pelaksanaan sudah lewat / hari ini.
     */
    public function test_lpj_button_appears_only_when_approved_and_date_passed(): void
    {
        $mahasiswa = User::where('email', 'mahasiswa.robotika@univ.ac.id')->firstOrFail();
        $ukm = $mahasiswa->ukm;

        // Buat ajuan yang SUDAH disetujui tapi tanggal masih di masa depan (+7 hari)
        $ajuanMasaDepan = AjuanKegiatan::create([
            'user_id' => $mahasiswa->id,
            'ukm_id' => $ukm->id,
            'nama_kegiatan' => 'Lomba Robotika Masa Depan',
            'tanggal_kegiatan' => now()->addDays(7)->toDateString(),
            'dokumen_proposal' => 'dummy.pdf',
            'dokumen_nama_asli' => 'dummy.pdf',
            'total_anggaran' => 1000000,
            'status_pembina' => 'disetujui',
            'status_kemahasiswaan' => 'disetujui',
            'status_akhir' => 'disetujui',
        ]);

        // Buat ajuan yang SUDAH disetujui dan tanggal SUDAH LEWAT (-2 hari)
        $ajuanLampau = AjuanKegiatan::create([
            'user_id' => $mahasiswa->id,
            'ukm_id' => $ukm->id,
            'nama_kegiatan' => 'Lomba Robotika Nasional 2026',
            'tanggal_kegiatan' => now()->subDays(2)->toDateString(),
            'dokumen_proposal' => 'dummy.pdf',
            'dokumen_nama_asli' => 'dummy.pdf',
            'total_anggaran' => 2000000,
            'status_pembina' => 'disetujui',
            'status_kemahasiswaan' => 'disetujui',
            'status_akhir' => 'disetujui',
        ]);

        // 1. Cek ajuan masa depan: tombol LPJ TIDAK boleh muncul
        $respFuture = $this->actingAs($mahasiswa)->get(route('mahasiswa.show', $ajuanMasaDepan->id));
        $respFuture->assertStatus(200);
        $respFuture->assertDontSee('Isi Laporan LPJ');

        // 2. Cek ajuan lampau: tombol LPJ & banner harus muncul
        $respPast = $this->actingAs($mahasiswa)->get(route('mahasiswa.show', $ajuanLampau->id));
        $respPast->assertStatus(200);
        $respPast->assertSee('Isi Laporan LPJ');
        $respPast->assertSee('Waktunya Isi LPJ');

        // 3. Cek di daftar index/dashboard
        $respIndex = $this->actingAs($mahasiswa)->get(route('mahasiswa.dashboard'));
        $respIndex->assertStatus(200);
        $respIndex->assertSee('Lapor LPJ');
        $respIndex->assertSee('Menunggu LPJ');
    }

    /**
     * Test 2: Mahasiswa dapat mengakses form LPJ.
     */
    public function test_student_can_access_lpj_form(): void
    {
        $mahasiswa = User::where('email', 'mahasiswa.robotika@univ.ac.id')->firstOrFail();

        $ajuan = AjuanKegiatan::create([
            'user_id' => $mahasiswa->id,
            'ukm_id' => $mahasiswa->ukm_id,
            'nama_kegiatan' => 'Kontes Robot Indonesia',
            'tanggal_kegiatan' => now()->subDays(1)->toDateString(),
            'dokumen_proposal' => 'dummy.pdf',
            'dokumen_nama_asli' => 'dummy.pdf',
            'total_anggaran' => 2500000,
            'status_pembina' => 'disetujui',
            'status_kemahasiswaan' => 'disetujui',
            'status_akhir' => 'disetujui',
        ]);

        $response = $this->actingAs($mahasiswa)->get(route('mahasiswa.lpj', $ajuan->id));
        $response->assertStatus(200);
        $response->assertSee('x-data="lpjForm()"', false);
        $response->assertSee('function lpjForm()', false);
        $response->assertSee('Laporan Pertanggungjawaban (LPJ)');
        $response->assertSee('Capaian, Hasil', false);
        $response->assertSee('Data Mahasiswa Peserta / Kontingen (SIMKATMAWA)');
        $response->assertSee('Realisasi Anggaran', false);
        $response->assertSee('Unggah Dokumen Bukti', false);
    }

    /**
     * Test 3: Mahasiswa dapat mengirim LPJ tanpa upload berkas (semua opsional)
     * dan data peserta SIMKATMAWA tersimpan dengan benar.
     */
    public function test_student_can_submit_lpj_without_files_and_saves_simkatmawa_participants(): void
    {
        $mahasiswa = User::where('email', 'mahasiswa.robotika@univ.ac.id')->firstOrFail();

        $ajuan = AjuanKegiatan::create([
            'user_id' => $mahasiswa->id,
            'ukm_id' => $mahasiswa->ukm_id,
            'nama_kegiatan' => 'Turnamen Robot Cerdas',
            'tanggal_kegiatan' => now()->subDays(3)->toDateString(),
            'dokumen_proposal' => 'dummy.pdf',
            'dokumen_nama_asli' => 'dummy.pdf',
            'total_anggaran' => 3000000,
            'status_pembina' => 'disetujui',
            'status_kemahasiswaan' => 'disetujui',
            'status_akhir' => 'disetujui',
        ]);

        $lpjData = [
            'prestasi' => 'Juara 1 (Emas / Gold Medal)',
            'jumlah_peserta' => 3,
            'ringkasan_kegiatan' => 'Kegiatan berjalan lancar dan UKM meraih juara pertama.',
            'realisasi_anggaran' => 2800000,
            'pesertas' => [
                ['nama' => 'Ahmad Fauzi', 'npm' => '2105101001', 'peran' => 'Ketua Tim'],
                ['nama' => 'Rina Salsabila', 'npm' => '2105101002', 'peran' => 'Programmer'],
                ['nama' => 'Dimas Anggoro', 'npm' => '2105101003', 'peran' => 'Mekanik'],
            ],
        ];

        $response = $this->actingAs($mahasiswa)->post(route('mahasiswa.lpj.store', $ajuan->id), $lpjData);
        $response->assertRedirect(route('mahasiswa.show', $ajuan->id));

        $ajuan->refresh();
        $this->assertEquals('menunggu_verifikasi_lpj', $ajuan->status_akhir);
        $this->assertNotNull($ajuan->lpj);
        $this->assertEquals('Juara 1 (Emas / Gold Medal)', $ajuan->lpj->prestasi);
        $this->assertEquals(3, $ajuan->lpj->jumlah_peserta);
        $this->assertEquals(2800000, $ajuan->lpj->realisasi_anggaran);

        // Verifikasi tabel peserta SIMKATMAWA
        $this->assertCount(3, $ajuan->lpj->pesertas);
        $this->assertDatabaseHas('lpj_pesertas', [
            'lpj_kegiatan_id' => $ajuan->lpj->id,
            'nama' => 'Ahmad Fauzi',
            'npm' => '2105101001',
        ]);
        $this->assertDatabaseHas('lpj_pesertas', [
            'lpj_kegiatan_id' => $ajuan->lpj->id,
            'nama' => 'Rina Salsabila',
            'npm' => '2105101002',
        ]);
    }

    /**
     * Test 4: Mahasiswa dapat mengunggah 4 berkas dokumen LPJ opsional.
     */
    public function test_student_can_upload_all_four_optional_files(): void
    {
        $mahasiswa = User::where('email', 'mahasiswa.robotika@univ.ac.id')->firstOrFail();

        $ajuan = AjuanKegiatan::create([
            'user_id' => $mahasiswa->id,
            'ukm_id' => $mahasiswa->ukm_id,
            'nama_kegiatan' => 'Olimpiade Robotika Nasional',
            'tanggal_kegiatan' => now()->subDays(5)->toDateString(),
            'dokumen_proposal' => 'dummy.pdf',
            'dokumen_nama_asli' => 'dummy.pdf',
            'total_anggaran' => 4000000,
            'status_pembina' => 'disetujui',
            'status_kemahasiswaan' => 'disetujui',
            'status_akhir' => 'disetujui',
        ]);

        $fileSertifikat = UploadedFile::fake()->create('Sertifikat_Juara1.pdf', 500, 'application/pdf');
        $fileFoto = UploadedFile::fake()->image('Foto_Suasana_Lomba.jpg');
        $filePenyerahan = UploadedFile::fake()->image('Foto_Podium_Piala.jpg');
        $fileUndangan = UploadedFile::fake()->create('Surat_Undangan_Resmi.pdf', 300, 'application/pdf');

        $lpjData = [
            'prestasi' => 'Juara 1 (Emas / Gold Medal)',
            'jumlah_peserta' => 2,
            'ringkasan_kegiatan' => 'Tim berhasil menyabet gelar juara umum.',
            'realisasi_anggaran' => 3750000,
            'pesertas' => [
                ['nama' => 'Budi Robotika', 'npm' => '2105101099', 'peran' => 'Ketua Tim'],
                ['nama' => 'Citra Lestari', 'npm' => '2105101098', 'peran' => 'Anggota'],
            ],
            'file_sertifikat' => $fileSertifikat,
            'foto_kegiatan' => $fileFoto,
            'foto_penyerahan' => $filePenyerahan,
            'surat_undangan' => $fileUndangan,
        ];

        $response = $this->actingAs($mahasiswa)->post(route('mahasiswa.lpj.store', $ajuan->id), $lpjData);
        $response->assertRedirect(route('mahasiswa.show', $ajuan->id));

        $ajuan->refresh();
        $lpj = $ajuan->lpj;

        $this->assertNotNull($lpj->file_sertifikat);
        $this->assertEquals('Sertifikat_Juara1.pdf', $lpj->file_sertifikat_nama);
        Storage::disk('public')->assertExists($lpj->file_sertifikat);

        $this->assertNotNull($lpj->foto_kegiatan);
        $this->assertEquals('Foto_Suasana_Lomba.jpg', $lpj->foto_kegiatan_nama);
        Storage::disk('public')->assertExists($lpj->foto_kegiatan);

        $this->assertNotNull($lpj->foto_penyerahan);
        $this->assertEquals('Foto_Podium_Piala.jpg', $lpj->foto_penyerahan_nama);
        Storage::disk('public')->assertExists($lpj->foto_penyerahan);

        $this->assertNotNull($lpj->surat_undangan);
        $this->assertEquals('Surat_Undangan_Resmi.pdf', $lpj->surat_undangan_nama);
        Storage::disk('public')->assertExists($lpj->surat_undangan);
    }

    /**
     * Test 5: Kemahasiswaan dapat meminta revisi LPJ, dan Mahasiswa dapat mengirim ulang.
     */
    public function test_kemahasiswaan_can_request_lpj_revision_and_student_resubmits(): void
    {
        $kemahasiswaan = User::where('role', 'kemahasiswaan')->firstOrFail();
        $mahasiswa = User::where('email', 'mahasiswa.robotika@univ.ac.id')->firstOrFail();

        $ajuan = AjuanKegiatan::create([
            'user_id' => $mahasiswa->id,
            'ukm_id' => $mahasiswa->ukm_id,
            'nama_kegiatan' => 'Workshop IoT Lanjutan',
            'tanggal_kegiatan' => now()->subDays(4)->toDateString(),
            'dokumen_proposal' => 'dummy.pdf',
            'dokumen_nama_asli' => 'dummy.pdf',
            'total_anggaran' => 2000000,
            'status_pembina' => 'disetujui',
            'status_kemahasiswaan' => 'disetujui',
            'status_akhir' => 'menunggu_verifikasi_lpj',
        ]);

        $lpj = LpjKegiatan::create([
            'ajuan_kegiatan_id' => $ajuan->id,
            'prestasi' => 'Peserta',
            'jumlah_peserta' => 10,
            'ringkasan_kegiatan' => 'Laporan awal',
            'realisasi_anggaran' => 2000000,
            'submitted_at' => now(),
        ]);

        // 1. Kemahasiswaan minta revisi LPJ
        $verifyResponse = $this->actingAs($kemahasiswaan)->post(route('kemahasiswaan.verify-lpj', $ajuan->id), [
            'status' => 'revisi',
            'catatan' => 'Mohon lampirkan foto dokumentasi kegiatan bersama pembina.',
        ]);
        $verifyResponse->assertRedirect(route('kemahasiswaan.show', $ajuan->id));

        $ajuan->refresh();
        $this->assertEquals('revisi_lpj', $ajuan->status_akhir);
        $this->assertEquals('Mohon lampirkan foto dokumentasi kegiatan bersama pembina.', $ajuan->lpj->catatan_verifikasi_lpj);

        // 2. Mahasiswa melihat pesan revisi LPJ
        $mhsResponse = $this->actingAs($mahasiswa)->get(route('mahasiswa.show', $ajuan->id));
        $mhsResponse->assertStatus(200);
        $mhsResponse->assertSee('Laporan Pertanggungjawaban (LPJ) Memerlukan Perbaikan');
        $mhsResponse->assertSee('Mohon lampirkan foto dokumentasi kegiatan bersama pembina.');

        // 3. Mahasiswa mengirim ulang perbaikan LPJ
        $resubmitResponse = $this->actingAs($mahasiswa)->post(route('mahasiswa.lpj.store', $ajuan->id), [
            'prestasi' => 'Peserta',
            'jumlah_peserta' => 10,
            'ringkasan_kegiatan' => 'Laporan telah diperbaiki sesuai arahan.',
            'realisasi_anggaran' => 1950000,
        ]);
        $resubmitResponse->assertRedirect(route('mahasiswa.show', $ajuan->id));

        $ajuan->refresh();
        $this->assertEquals('menunggu_verifikasi_lpj', $ajuan->status_akhir);
    }

    /**
     * Test 6: Kemahasiswaan menyetujui LPJ dan status berubah menjadi 'sudah_dilaksanakan'.
     */
    public function test_kemahasiswaan_can_approve_lpj_and_status_becomes_sudah_dilaksanakan(): void
    {
        $kemahasiswaan = User::where('role', 'kemahasiswaan')->firstOrFail();
        $mahasiswa = User::where('email', 'mahasiswa.robotika@univ.ac.id')->firstOrFail();

        $ajuan = AjuanKegiatan::create([
            'user_id' => $mahasiswa->id,
            'ukm_id' => $mahasiswa->ukm_id,
            'nama_kegiatan' => 'Pameran Karya Robotika 2026',
            'tanggal_kegiatan' => now()->subDays(2)->toDateString(),
            'dokumen_proposal' => 'dummy.pdf',
            'dokumen_nama_asli' => 'dummy.pdf',
            'total_anggaran' => 5000000,
            'status_pembina' => 'disetujui',
            'status_kemahasiswaan' => 'disetujui',
            'status_akhir' => 'menunggu_verifikasi_lpj',
        ]);

        $lpj = LpjKegiatan::create([
            'ajuan_kegiatan_id' => $ajuan->id,
            'prestasi' => 'Kegiatan Non-Lomba (Terlaksana Sukses)',
            'jumlah_peserta' => 25,
            'ringkasan_kegiatan' => 'Pameran dikunjungi 300 orang mahasiswa.',
            'realisasi_anggaran' => 4800000,
            'submitted_at' => now(),
        ]);

        $response = $this->actingAs($kemahasiswaan)->post(route('kemahasiswaan.verify-lpj', $ajuan->id), [
            'status' => 'disetujui',
            'catatan' => 'LPJ telah diperiksa dan dinyatakan lengkap. Selamat atas suksesnya kegiatan.',
        ]);
        $response->assertRedirect(route('kemahasiswaan.show', $ajuan->id));

        $ajuan->refresh();
        $this->assertEquals('sudah_dilaksanakan', $ajuan->status_akhir);
        $this->assertEquals($kemahasiswaan->id, $ajuan->lpj->verified_lpj_by);
        $this->assertNotNull($ajuan->lpj->verified_lpj_at);

        // Verifikasi tampilan mahasiswa menampilkan badge Selesai Dilaksanakan
        $mhsView = $this->actingAs($mahasiswa)->get(route('mahasiswa.show', $ajuan->id));
        $mhsView->assertStatus(200);
        $mhsView->assertSee('Kegiatan Selesai Dilaksanakan', false);
        $mhsView->assertSee('LPJ Telah Disetujui', false);
    }

    /**
     * Test 7: Otorisasi unduh berkas LPJ untuk Mahasiswa, Pembina, dan Kemahasiswaan.
     */
    public function test_authorized_users_can_download_lpj_files(): void
    {
        $kemahasiswaan = User::where('role', 'kemahasiswaan')->firstOrFail();
        $pembina = User::where('email', 'pembina.robotika@univ.ac.id')->firstOrFail();
        $mahasiswa = User::where('email', 'mahasiswa.robotika@univ.ac.id')->firstOrFail();
        $mahasiswaLain = User::where('email', 'mahasiswa.musik@univ.ac.id')->firstOrFail();

        $ajuan = AjuanKegiatan::create([
            'user_id' => $mahasiswa->id,
            'ukm_id' => $mahasiswa->ukm_id,
            'nama_kegiatan' => 'Seminar Teknologi AI',
            'tanggal_kegiatan' => now()->subDays(3)->toDateString(),
            'dokumen_proposal' => 'dummy.pdf',
            'dokumen_nama_asli' => 'dummy.pdf',
            'total_anggaran' => 1500000,
            'status_pembina' => 'disetujui',
            'status_kemahasiswaan' => 'disetujui',
            'status_akhir' => 'sudah_dilaksanakan',
        ]);

        $filePath = 'dokumen_lpj/sertifikat/sample_cert.pdf';
        Storage::disk('public')->put($filePath, 'PDF CERTIFICATE CONTENT');

        $lpj = LpjKegiatan::create([
            'ajuan_kegiatan_id' => $ajuan->id,
            'file_sertifikat' => $filePath,
            'file_sertifikat_nama' => 'Sertifikat_Seminar.pdf',
            'submitted_at' => now(),
        ]);

        // 1. Mahasiswa pemilik UKM dapat mengunduh
        $respMhs = $this->actingAs($mahasiswa)->get(route('mahasiswa.lpj.download', ['id' => $ajuan->id, 'type' => 'sertifikat']));
        $respMhs->assertStatus(200);

        // 2. Pembina UKM terkait dapat mengunduh
        $respPembina = $this->actingAs($pembina)->get(route('pembina.lpj.download', ['id' => $ajuan->id, 'type' => 'sertifikat']));
        $respPembina->assertStatus(200);

        // 3. Kemahasiswaan dapat mengunduh
        $respKmhs = $this->actingAs($kemahasiswaan)->get(route('kemahasiswaan.lpj.download', ['id' => $ajuan->id, 'type' => 'sertifikat']));
        $respKmhs->assertStatus(200);

        // 4. Mahasiswa UKM lain TIDAK dapat mengunduh (404 / firstOrFail scoping)
        $respLain = $this->actingAs($mahasiswaLain)->get(route('mahasiswa.lpj.download', ['id' => $ajuan->id, 'type' => 'sertifikat']));
        $respLain->assertStatus(404);
    }

    /**
     * Test 8: Mahasiswa dapat mencatat multi-juara/prestasi dalam 1 kegiatan dan tampil di semua role.
     */
    public function test_student_can_submit_multiple_prestasies_and_views_display_awards(): void
    {
        $mahasiswa = User::where('email', 'mahasiswa.robotika@univ.ac.id')->firstOrFail();
        $pembina = User::where('email', 'pembina.robotika@univ.ac.id')->firstOrFail();
        $kemahasiswaan = User::where('email', 'kemahasiswaan@univ.ac.id')->firstOrFail();

        $ajuan = AjuanKegiatan::create([
            'user_id' => $mahasiswa->id,
            'ukm_id' => $mahasiswa->ukm_id,
            'nama_kegiatan' => 'Kejuaraan Renang & Aquatik Antar Mahasiswa',
            'tanggal_kegiatan' => now()->subDays(2)->toDateString(),
            'dokumen_proposal' => 'dummy.pdf',
            'dokumen_nama_asli' => 'dummy.pdf',
            'total_anggaran' => 5000000,
            'status_pembina' => 'disetujui',
            'status_kemahasiswaan' => 'disetujui',
            'status_akhir' => 'disetujui',
        ]);

        $lpjData = [
            'jumlah_peserta' => 4,
            'realisasi_anggaran' => 4800000,
            'ringkasan_kegiatan' => 'Kontingen meraih total 3 medali pada nomor perlombaan renang.',
            'prestasies' => [
                [
                    'kategori_lomba' => 'Gaya Dada 100m Putra',
                    'peringkat_juara' => 'Juara 1 (Emas / Gold Medal)',
                    'tingkat' => 'Nasional',
                    'nama_peraih' => 'Budi Santoso',
                ],
                [
                    'kategori_lomba' => 'Gaya Bebas 50m Putri',
                    'peringkat_juara' => 'Juara 2 (Perak / Silver Medal)',
                    'tingkat' => 'Nasional',
                    'nama_peraih' => 'Siti Rahma',
                ],
                [
                    'kategori_lomba' => 'Estafet Campuran 4x50m',
                    'peringkat_juara' => 'Juara 3 (Perunggu / Bronze Medal)',
                    'tingkat' => 'Wilayah / LLDIKTI',
                    'nama_peraih' => 'Tim Estafet UKM',
                ],
            ],
            'pesertas' => [
                ['nama' => 'Budi Santoso', 'npm' => '2105101011', 'peran' => 'Perenang Putra'],
                ['nama' => 'Siti Rahma', 'npm' => '2105101012', 'peran' => 'Perenang Putri'],
            ],
        ];

        // 1. Mahasiswa submit LPJ multi-prestasi
        $submitResp = $this->actingAs($mahasiswa)->post(route('mahasiswa.lpj.store', $ajuan->id), $lpjData);
        $submitResp->assertRedirect(route('mahasiswa.show', $ajuan->id));

        $ajuan->refresh();
        $this->assertEquals('menunggu_verifikasi_lpj', $ajuan->status_akhir);
        $this->assertNotNull($ajuan->lpj);
        $this->assertCount(3, $ajuan->lpj->prestasies);

        // Verifikasi database record per medali
        $this->assertDatabaseHas('lpj_prestasies', [
            'lpj_kegiatan_id' => $ajuan->lpj->id,
            'kategori_lomba' => 'Gaya Dada 100m Putra',
            'peringkat_juara' => 'Juara 1 (Emas / Gold Medal)',
            'tingkat' => 'Nasional',
            'nama_peraih' => 'Budi Santoso',
        ]);

        $this->assertDatabaseHas('lpj_prestasies', [
            'lpj_kegiatan_id' => $ajuan->lpj->id,
            'kategori_lomba' => 'Gaya Bebas 50m Putri',
            'peringkat_juara' => 'Juara 2 (Perak / Silver Medal)',
            'tingkat' => 'Nasional',
            'nama_peraih' => 'Siti Rahma',
        ]);

        $this->assertDatabaseHas('lpj_prestasies', [
            'lpj_kegiatan_id' => $ajuan->lpj->id,
            'kategori_lomba' => 'Estafet Campuran 4x50m',
            'peringkat_juara' => 'Juara 3 (Perunggu / Bronze Medal)',
            'tingkat' => 'Wilayah / LLDIKTI',
            'nama_peraih' => 'Tim Estafet UKM',
        ]);

        // 2. Mahasiswa view menampilkan rincian multi-juara
        $mhsView = $this->actingAs($mahasiswa)->get(route('mahasiswa.show', $ajuan->id));
        $mhsView->assertStatus(200);
        $mhsView->assertSee('Daftar Perolehan Juara &amp; Capaian Prestasi Lomba', false);
        $mhsView->assertSee('Gaya Dada 100m Putra');
        $mhsView->assertSee('Budi Santoso');
        $mhsView->assertSee('Gaya Bebas 50m Putri');
        $mhsView->assertSee('Siti Rahma');
        $mhsView->assertSee('Estafet Campuran 4x50m');
        $mhsView->assertSee('Tim Estafet UKM');
        $mhsView->assertSee('3 Kejuaraan / Medali');

        // 3. Pembina view menampilkan rincian multi-juara
        $pembinaView = $this->actingAs($pembina)->get(route('pembina.show', $ajuan->id));
        $pembinaView->assertStatus(200);
        $pembinaView->assertSee('Daftar Perolehan Juara &amp; Capaian Prestasi Lomba', false);
        $pembinaView->assertSee('Gaya Dada 100m Putra');
        $pembinaView->assertSee('Budi Santoso');

        // 4. Kemahasiswaan view menampilkan rincian multi-juara
        $kmhsView = $this->actingAs($kemahasiswaan)->get(route('kemahasiswaan.show', $ajuan->id));
        $kmhsView->assertStatus(200);
        $kmhsView->assertSee('Daftar Perolehan Juara &amp; Capaian Prestasi Lomba', false);
        $kmhsView->assertSee('Gaya Dada 100m Putra');
        $kmhsView->assertSee('3 Kejuaraan / Medali');
    }
}
