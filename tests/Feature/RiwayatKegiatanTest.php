<?php

namespace Tests\Feature;

use App\Models\AjuanKegiatan;
use App\Models\DetailKegiatan;
use App\Models\LpjKegiatan;
use App\Models\RiwayatKegiatan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RiwayatKegiatanTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /**
     * Test 1: Log tercatat saat mahasiswa mengajukan proposal baru.
     */
    public function test_riwayat_recorded_on_proposal_creation(): void
    {
        Storage::fake('public');
        $mahasiswa = User::where('email', 'mahasiswa.robotika@univ.ac.id')->firstOrFail();

        $file = UploadedFile::fake()->create('Proposal_Timeline_Test.pdf', 300, 'application/pdf');

        $response = $this->actingAs($mahasiswa)->post(route('mahasiswa.store'), [
            'nama_kegiatan' => 'Workshop IoT & Robotika 2026',
            'tanggal_kegiatan' => now()->addDays(15)->toDateString(),
            'jenis_kegiatan' => 'Pelatihan',
            'dokumen_kegiatan' => $file,
            'items' => [
                [
                    'nama_akun' => 'Konsumsi',
                    'nama_barang' => 'Snack Box Peserta',
                    'harga_satuan' => 15000,
                    'jumlah' => 40,
                    'satuan' => 'Porsi',
                    'durasi' => 1,
                    'satuan_durasi' => 'Hari',
                    'catatan' => 'Sesi pagi',
                ],
            ],
        ]);

        $response->assertRedirect();

        $ajuan = AjuanKegiatan::where('nama_kegiatan', 'Workshop IoT & Robotika 2026')->firstOrFail();

        // Cek log riwayat
        $this->assertDatabaseHas('riwayat_kegiatans', [
            'ajuan_kegiatan_id' => $ajuan->id,
            'aksi' => 'Pengajuan Usulan Baru',
            'status_sesudah' => 'menunggu_pembina',
            'role' => 'mahasiswa',
            'user_id' => $mahasiswa->id,
        ]);

        $this->assertCount(1, $ajuan->riwayats);
        $this->assertEquals('Pengajuan Usulan Baru', $ajuan->riwayats->first()->aksi);
    }

    /**
     * Test 2: Log tercatat saat Pembina menyetujui, meminta revisi, atau menolak usulan.
     */
    public function test_riwayat_recorded_on_pembina_actions(): void
    {
        Storage::fake('public');
        $pembina = User::where('email', 'pembina.robotika@univ.ac.id')->firstOrFail();
        $mahasiswa = User::where('email', 'mahasiswa.robotika@univ.ac.id')->firstOrFail();

        $ajuan = AjuanKegiatan::create([
            'user_id' => $mahasiswa->id,
            'ukm_id' => $mahasiswa->ukm_id,
            'nama_kegiatan' => 'Lomba Robotika Nasional',
            'tanggal_kegiatan' => now()->addDays(30),
            'jenis_kegiatan' => 'Lomba',
            'dokumen_proposal' => 'test.pdf',
            'dokumen_nama_asli' => 'test.pdf',
            'total_anggaran' => 5000000,
            'status_pembina' => 'belum_disetujui',
            'status_kemahasiswaan' => 'belum_disetujui',
            'status_akhir' => 'menunggu_pembina',
        ]);

        // 1. Pembina minta revisi
        $response = $this->actingAs($pembina)->post(route('pembina.verify', $ajuan->id), [
            'status' => 'revisi',
            'catatan' => 'Tolong lengkapi susunan kepanitiaan pada proposal.',
        ]);
        $response->assertRedirect();

        $this->assertDatabaseHas('riwayat_kegiatans', [
            'ajuan_kegiatan_id' => $ajuan->id,
            'aksi' => 'Permintaan Revisi (Pembina)',
            'status_sesudah' => 'revisi_pembina',
            'role' => 'pembina',
            'catatan' => 'Tolong lengkapi susunan kepanitiaan pada proposal.',
        ]);

        // 2. Pembina kemudian menyetujui
        $response = $this->actingAs($pembina)->post(route('pembina.verify', $ajuan->id), [
            'status' => 'disetujui',
            'catatan' => 'Sudah bagus dan disetujui.',
        ]);
        $response->assertRedirect();

        $this->assertDatabaseHas('riwayat_kegiatans', [
            'ajuan_kegiatan_id' => $ajuan->id,
            'aksi' => 'Persetujuan Pembina',
            'status_sesudah' => 'menunggu_kemahasiswaan',
            'role' => 'pembina',
        ]);
    }

    /**
     * Test 3: Log tercatat saat mahasiswa mengirim revisi atau mengupdate usulan.
     */
    public function test_riwayat_recorded_on_student_resubmit(): void
    {
        Storage::fake('public');
        $mahasiswa = User::where('email', 'mahasiswa.robotika@univ.ac.id')->firstOrFail();

        $ajuan = AjuanKegiatan::create([
            'user_id' => $mahasiswa->id,
            'ukm_id' => $mahasiswa->ukm_id,
            'nama_kegiatan' => 'Pelatihan CAD Design',
            'tanggal_kegiatan' => now()->addDays(20),
            'jenis_kegiatan' => 'Pelatihan',
            'dokumen_proposal' => 'cad.pdf',
            'dokumen_nama_asli' => 'cad.pdf',
            'total_anggaran' => 1000000,
            'status_pembina' => 'revisi',
            'status_kemahasiswaan' => 'belum_disetujui',
            'status_akhir' => 'revisi_pembina',
        ]);

        DetailKegiatan::create([
            'ajuan_kegiatan_id' => $ajuan->id,
            'no_urut' => 1,
            'nama_akun' => 'Sertifikat',
            'nama_barang' => 'Cetak Sertifikat',
            'harga_satuan' => 5000,
            'jumlah' => 20,
            'satuan' => 'Lembar',
            'durasi' => 1,
            'satuan_durasi' => 'Hari',
            'total' => 100000,
            'status_keterangan' => 'belum_diverifikasi',
        ]);

        $response = $this->actingAs($mahasiswa)->put(route('mahasiswa.update', $ajuan->id), [
            'nama_kegiatan' => 'Pelatihan CAD Design 2026',
            'tanggal_kegiatan' => now()->addDays(25)->toDateString(),
            'jenis_kegiatan' => 'Pelatihan',
            'items' => [
                [
                    'nama_akun' => 'Sertifikat',
                    'nama_barang' => 'Cetak Sertifikat Peserta & Instruktur',
                    'harga_satuan' => 5000,
                    'jumlah' => 30,
                    'satuan' => 'Lembar',
                    'durasi' => 1,
                    'satuan_durasi' => 'Hari',
                    'catatan' => 'Sudah diperbaiki',
                ],
            ],
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('riwayat_kegiatans', [
            'ajuan_kegiatan_id' => $ajuan->id,
            'aksi' => 'Pengajuan Revisi Usulan',
            'status_sesudah' => 'menunggu_pembina',
            'role' => 'mahasiswa',
        ]);
    }

    /**
     * Test 4: Log tercatat saat Kemahasiswaan edit RAB dan menyetujui usulan secara final.
     */
    public function test_riwayat_recorded_on_kemahasiswaan_actions(): void
    {
        $kemahasiswaan = User::where('email', 'kemahasiswaan@univ.ac.id')->firstOrFail();
        $mahasiswa = User::where('email', 'mahasiswa.robotika@univ.ac.id')->firstOrFail();

        $ajuan = AjuanKegiatan::create([
            'user_id' => $mahasiswa->id,
            'ukm_id' => $mahasiswa->ukm_id,
            'nama_kegiatan' => 'Turnamen Robotika',
            'tanggal_kegiatan' => now()->addDays(10),
            'jenis_kegiatan' => 'Lomba',
            'dokumen_proposal' => 'doc.pdf',
            'dokumen_nama_asli' => 'doc.pdf',
            'total_anggaran' => 2000000,
            'status_pembina' => 'disetujui',
            'status_kemahasiswaan' => 'belum_disetujui',
            'status_akhir' => 'menunggu_kemahasiswaan',
        ]);

        $detail = DetailKegiatan::create([
            'ajuan_kegiatan_id' => $ajuan->id,
            'no_urut' => 1,
            'nama_akun' => 'Hadiah',
            'nama_barang' => 'Piala Juara',
            'harga_satuan' => 500000,
            'jumlah' => 3,
            'satuan' => 'Set',
            'durasi' => 1,
            'satuan_durasi' => 'Hari',
            'total' => 1500000,
            'status_keterangan' => 'belum_diverifikasi',
        ]);

        // 1. Kemahasiswaan edit RAB
        $response = $this->actingAs($kemahasiswaan)->post(route('kemahasiswaan.update-details', $ajuan->id), [
            'items' => [
                [
                    'id' => $detail->id,
                    'nama_akun' => 'Hadiah Juara',
                    'nama_barang' => 'Piala Juara 1, 2, 3',
                    'harga_satuan' => 450000,
                    'jumlah' => 3,
                    'satuan' => 'Set',
                    'durasi' => 1,
                    'satuan_durasi' => 'Hari',
                    'status_keterangan' => 'disetujui',
                    'catatan' => 'Disesuaikan dengan standar SBU',
                ],
            ],
        ]);
        $response->assertRedirect();

        $this->assertDatabaseHas('riwayat_kegiatans', [
            'ajuan_kegiatan_id' => $ajuan->id,
            'aksi' => 'Penyesuaian Detail RAB',
            'role' => 'kemahasiswaan',
        ]);

        // 2. Kemahasiswaan setujui keseluruhan
        $response = $this->actingAs($kemahasiswaan)->post(route('kemahasiswaan.approve', $ajuan->id), [
            'status' => 'disetujui',
            'catatan_kemahasiswaan' => 'Selamat, kegiatan disetujui penuh.',
        ]);
        $response->assertRedirect();

        $this->assertDatabaseHas('riwayat_kegiatans', [
            'ajuan_kegiatan_id' => $ajuan->id,
            'aksi' => 'Persetujuan Kemahasiswaan (Final)',
            'status_sesudah' => 'disetujui',
            'role' => 'kemahasiswaan',
            'catatan' => 'Selamat, kegiatan disetujui penuh.',
        ]);
    }

    /**
     * Test 5: Log tercatat saat penyerahan LPJ dan verifikasi LPJ oleh Kemahasiswaan.
     */
    public function test_riwayat_recorded_on_lpj_workflow(): void
    {
        $mahasiswa = User::where('email', 'mahasiswa.robotika@univ.ac.id')->firstOrFail();
        $kemahasiswaan = User::where('email', 'kemahasiswaan@univ.ac.id')->firstOrFail();

        $ajuan = AjuanKegiatan::create([
            'user_id' => $mahasiswa->id,
            'ukm_id' => $mahasiswa->ukm_id,
            'nama_kegiatan' => 'Kegiatan Selesai',
            'tanggal_kegiatan' => now()->subDays(2),
            'jenis_kegiatan' => 'Lomba',
            'dokumen_proposal' => 'proposal.pdf',
            'dokumen_nama_asli' => 'proposal.pdf',
            'total_anggaran' => 2000000,
            'status_pembina' => 'disetujui',
            'status_kemahasiswaan' => 'disetujui',
            'status_akhir' => 'disetujui',
        ]);

        // 1. Mahasiswa submit LPJ
        $response = $this->actingAs($mahasiswa)->post(route('mahasiswa.lpj.store', $ajuan->id), [
            'prestasi' => 'Juara 1 Nasional',
            'jumlah_peserta' => 5,
            'ringkasan_kegiatan' => 'Alhamdulillah kegiatan berjalan sangat baik dan lancar.',
            'biaya_terpakai' => 1950000,
            'pesertas' => [
                ['nama' => 'Budi Santoso', 'npm' => '210001', 'peran' => 'Ketua Tim'],
            ],
            'prestasies' => [
                ['kategori_lomba' => 'Robot Sumo', 'peringkat_juara' => 'Juara 1', 'tingkat' => 'Nasional', 'nama_peraih' => 'Budi Santoso'],
            ],
        ]);
        $response->assertRedirect();

        $this->assertDatabaseHas('riwayat_kegiatans', [
            'ajuan_kegiatan_id' => $ajuan->id,
            'aksi' => 'Penyerahan Laporan LPJ',
            'status_sesudah' => 'menunggu_verifikasi_lpj',
            'role' => 'mahasiswa',
        ]);

        // 2. Kemahasiswaan setujui LPJ
        $response = $this->actingAs($kemahasiswaan)->post(route('kemahasiswaan.verify-lpj', $ajuan->id), [
            'status' => 'disetujui',
            'catatan' => 'LPJ dan dokumentasi lengkap. Selamat atas prestasinya!',
        ]);
        $response->assertRedirect();

        $this->assertDatabaseHas('riwayat_kegiatans', [
            'ajuan_kegiatan_id' => $ajuan->id,
            'aksi' => 'Verifikasi LPJ: Disetujui',
            'status_sesudah' => 'sudah_dilaksanakan',
            'role' => 'kemahasiswaan',
            'catatan' => 'LPJ dan dokumentasi lengkap. Selamat atas prestasinya!',
        ]);
    }

    /**
     * Test 6: Timeline view rendered pada halaman detail Mahasiswa, Pembina, dan Kemahasiswaan.
     */
    public function test_timeline_view_renders_on_all_detail_pages(): void
    {
        $mahasiswa = User::where('email', 'mahasiswa.robotika@univ.ac.id')->firstOrFail();
        $pembina = User::where('email', 'pembina.robotika@univ.ac.id')->firstOrFail();
        $kemahasiswaan = User::where('email', 'kemahasiswaan@univ.ac.id')->firstOrFail();

        $ajuan = AjuanKegiatan::create([
            'user_id' => $mahasiswa->id,
            'ukm_id' => $mahasiswa->ukm_id,
            'nama_kegiatan' => 'Kegiatan Multi-Role View Test',
            'tanggal_kegiatan' => now()->addDays(5),
            'jenis_kegiatan' => 'Lomba',
            'dokumen_proposal' => 'doc.pdf',
            'dokumen_nama_asli' => 'doc.pdf',
            'total_anggaran' => 1000000,
            'status_pembina' => 'disetujui',
            'status_kemahasiswaan' => 'disetujui',
            'status_akhir' => 'disetujui',
        ]);

        $ajuan->catatRiwayat('Pengajuan Usulan Baru', 'menunggu_pembina', 'Usulan awal diajukan.', null, $mahasiswa->id, 'mahasiswa');
        $ajuan->catatRiwayat('Persetujuan Pembina', 'menunggu_kemahasiswaan', 'Disetujui Pembina.', 'menunggu_pembina', $pembina->id, 'pembina');
        $ajuan->catatRiwayat('Persetujuan Kemahasiswaan (Final)', 'disetujui', 'Disetujui Kemahasiswaan.', 'menunggu_kemahasiswaan', $kemahasiswaan->id, 'kemahasiswaan');

        // Check Mahasiswa Show View
        $responseMhs = $this->actingAs($mahasiswa)->get(route('mahasiswa.show', $ajuan->id));
        $responseMhs->assertStatus(200);
        $responseMhs->assertSee('Riwayat Jejak & Aktivitas Usulan');
        $responseMhs->assertSee('Pengajuan Usulan Baru');
        $responseMhs->assertSee('Persetujuan Pembina');
        $responseMhs->assertSee('Persetujuan Kemahasiswaan (Final)');

        // Check Pembina Show View
        $responsePembina = $this->actingAs($pembina)->get(route('pembina.show', $ajuan->id));
        $responsePembina->assertStatus(200);
        $responsePembina->assertSee('Riwayat Jejak & Aktivitas Usulan');
        $responsePembina->assertSee('Persetujuan Pembina');

        // Check Kemahasiswaan Show View
        $responseKemahasiswaan = $this->actingAs($kemahasiswaan)->get(route('kemahasiswaan.show', $ajuan->id));
        $responseKemahasiswaan->assertStatus(200);
        $responseKemahasiswaan->assertSee('Riwayat Jejak & Aktivitas Usulan');
        $responseKemahasiswaan->assertSee('Persetujuan Kemahasiswaan (Final)');
    }
}
