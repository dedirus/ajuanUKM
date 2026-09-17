<?php

namespace Tests\Feature;

use App\Models\AjuanKegiatan;
use App\Models\DetailKegiatan;
use App\Models\Ukm;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AjuanUKMWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /**
     * Test 1: Mahasiswa Login, View Form, and Submit Proposal with Detail Items.
     */
    public function test_mahasiswa_can_submit_proposal_with_detail_items(): void
    {
        Storage::fake('public');

        $mahasiswa = User::where('email', 'mahasiswa.robotika@univ.ac.id')->firstOrFail();

        // 1. Check access to dashboard & create form
        $response = $this->actingAs($mahasiswa)->get(route('mahasiswa.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('UKM Robotika & Otomasi');

        $response = $this->actingAs($mahasiswa)->get(route('mahasiswa.create'));
        $response->assertStatus(200);

        // 2. Submit new proposal
        $file = UploadedFile::fake()->create('Proposal_RoboFest_2026.pdf', 500, 'application/pdf');

        $postData = [
            'nama_kegiatan' => 'RoboFest Championship 2026',
            'tanggal_kegiatan' => now()->addDays(20)->toDateString(),
            'jenis_kegiatan' => 'Lomba',
            'dokumen_kegiatan' => $file,
            'items' => [
                [
                    'nama_akun' => 'Perlengkapan',
                    'nama_barang' => 'Arena Lintasan Line Follower',
                    'harga_satuan' => 1200000,
                    'jumlah' => 2,
                    'satuan' => 'Unit',
                    'durasi' => 1,
                    'satuan_durasi' => 'Hari',
                    'catatan' => 'Sesuai regulasi nasional',
                ],
                [
                    'nama_akun' => 'Konsumsi',
                    'nama_barang' => 'Makan Siang Peserta',
                    'harga_satuan' => 30000,
                    'jumlah' => 40,
                    'satuan' => 'Dus',
                    'durasi' => 2, // 2 hari pelaksanaan
                    'satuan_durasi' => 'Hari',
                    'catatan' => '40 pax x 2 hari',
                ],
            ],
        ];

        $submitResponse = $this->actingAs($mahasiswa)->post(route('mahasiswa.store'), $postData);
        $submitResponse->assertRedirect();

        // 3. Verify in database
        $this->assertDatabaseHas('ajuan_kegiatans', [
            'nama_kegiatan' => 'RoboFest Championship 2026',
            'ukm_id' => $mahasiswa->ukm_id,
            'jenis_kegiatan' => 'Lomba',
            'status_pembina' => 'belum_disetujui',
            'status_kemahasiswaan' => 'belum_disetujui',
            'status_akhir' => 'menunggu_pembina',
            'total_anggaran' => 4800000, // (1200000*2*1) + (30000*40*2) = 2400000 + 2400000 = 4800000
        ]);

        $ajuan = AjuanKegiatan::where('nama_kegiatan', 'RoboFest Championship 2026')->first();
        $this->assertCount(2, $ajuan->details);
        $this->assertEquals('Arena Lintasan Line Follower', $ajuan->details[0]->nama_barang);
        $this->assertEquals(1, $ajuan->details[0]->durasi);
        $this->assertEquals('Unit', $ajuan->details[0]->satuan);
        $this->assertEquals('Hari', $ajuan->details[0]->satuan_durasi);
        $this->assertEquals(2, $ajuan->details[1]->durasi);
        $this->assertEquals('Dus', $ajuan->details[1]->satuan);
        $this->assertEquals('Hari', $ajuan->details[1]->satuan_durasi);
        $this->assertEquals(2400000, $ajuan->details[0]->total);
        $this->assertEquals(2400000, $ajuan->details[1]->total);
        $this->assertEquals('belum_diverifikasi', $ajuan->details[0]->status_keterangan);
    }

    /**
     * Test 2: Pembina UKM sees submissions of their UKM and can approve.
     */
    public function test_pembina_can_view_and_approve_ukm_proposal(): void
    {
        $pembina = User::where('email', 'pembina.robotika@univ.ac.id')->firstOrFail();
        $ajuan = AjuanKegiatan::where('ukm_id', $pembina->ukm_id)
            ->where('status_pembina', 'belum_disetujui')
            ->firstOrFail();

        // 1. Pembina views dashboard and detail
        $response = $this->actingAs($pembina)->get(route('pembina.dashboard'));
        $response->assertStatus(200);
        $response->assertSee($ajuan->nama_kegiatan);

        $response = $this->actingAs($pembina)->get(route('pembina.show', $ajuan->id));
        $response->assertStatus(200);
        $response->assertSee('Tabel Detail Ajuan Kegiatan');

        // 2. Pembina approves proposal
        $approveResponse = $this->actingAs($pembina)->post(route('pembina.verify', $ajuan->id), [
            'status' => 'disetujui',
            'catatan' => 'Disetujui oleh Pembina untuk dilanjutkan ke Kemahasiswaan.',
        ]);

        $approveResponse->assertRedirect(route('pembina.show', $ajuan->id));

        $ajuan->refresh();
        $this->assertEquals('disetujui', $ajuan->status_pembina);
        $this->assertEquals('menunggu_kemahasiswaan', $ajuan->status_akhir);
        $this->assertEquals('Disetujui oleh Pembina untuk dilanjutkan ke Kemahasiswaan.', $ajuan->catatan_pembina);
    }

    /**
     * Test 3: Kemahasiswaan filters UKM, verifies detail item checklist, and approves overall.
     */
    public function test_kemahasiswaan_filter_verify_details_and_approve_overall(): void
    {
        $kemahasiswaan = User::where('email', 'kemahasiswaan@univ.ac.id')->firstOrFail();
        $ukmMusik = Ukm::where('kode', 'MUSIK')->firstOrFail();

        // 1. Dashboard with filter
        $response = $this->actingAs($kemahasiswaan)->get(route('kemahasiswaan.dashboard', [
            'ukm_id' => $ukmMusik->id,
        ]));
        $response->assertStatus(200);
        $response->assertSee('Festival Musik Akustik dan Parade Seni Mahasiswa');

        $ajuan = AjuanKegiatan::where('ukm_id', $ukmMusik->id)->firstOrFail();

        // 2. View detail
        $response = $this->actingAs($kemahasiswaan)->get(route('kemahasiswaan.show', $ajuan->id));
        $response->assertStatus(200);

        // 3. Update checklist per detail item
        $itemsData = [];
        foreach ($ajuan->details as $index => $detail) {
            $itemsData[] = [
                'id' => $detail->id,
                'status_keterangan' => 'disetujui',
                'catatan' => 'Sesuai dengan pagu anggaran',
            ];
        }

        $detailResponse = $this->actingAs($kemahasiswaan)->post(route('kemahasiswaan.update-details', $ajuan->id), [
            'items' => $itemsData,
        ]);
        $detailResponse->assertRedirect(route('kemahasiswaan.show', $ajuan->id));

        foreach ($ajuan->details as $detail) {
            $detail->refresh();
            $this->assertEquals('disetujui', $detail->status_keterangan);
        }

        // 4. Final approval overall
        $finalResponse = $this->actingAs($kemahasiswaan)->post(route('kemahasiswaan.approve', $ajuan->id), [
            'status' => 'disetujui',
            'catatan_kemahasiswaan' => 'Proposal resmi disetujui, dana dapat dicairkan melalui bagian keuangan.',
        ]);

        $finalResponse->assertRedirect(route('kemahasiswaan.show', $ajuan->id));

        $ajuan->refresh();
        $this->assertEquals('disetujui', $ajuan->status_kemahasiswaan);
        $this->assertEquals('disetujui', $ajuan->status_akhir);
    }

    /**
     * Test 4: Pembina can request revision and Mahasiswa can edit and resubmit.
     */
    public function test_pembina_can_request_revision_and_student_can_resubmit(): void
    {
        $pembina = User::where('email', 'pembina.robotika@univ.ac.id')->firstOrFail();
        $mahasiswa = User::where('email', 'mahasiswa.robotika@univ.ac.id')->firstOrFail();
        $ajuan = AjuanKegiatan::where('ukm_id', $pembina->ukm_id)->firstOrFail();

        // 1. Pembina requests revision
        $response = $this->actingAs($pembina)->post(route('pembina.verify', $ajuan->id), [
            'status' => 'revisi',
            'catatan' => 'Mohon sesuaikan jumlah peserta dan kurangi pos konsumsi.',
        ]);
        $response->assertRedirect(route('pembina.show', $ajuan->id));

        $ajuan->refresh();
        $this->assertEquals('revisi', $ajuan->status_pembina);
        $this->assertEquals('revisi_pembina', $ajuan->status_akhir);
        $this->assertEquals('Mohon sesuaikan jumlah peserta dan kurangi pos konsumsi.', $ajuan->catatan_revisi_terakhir);

        // 2. Mahasiswa views edit form
        $editResponse = $this->actingAs($mahasiswa)->get(route('mahasiswa.edit', $ajuan->id));
        $editResponse->assertStatus(200);
        $editResponse->assertSee('Mohon sesuaikan jumlah peserta dan kurangi pos konsumsi.');

        // 3. Mahasiswa updates and resubmits
        $updateResponse = $this->actingAs($mahasiswa)->put(route('mahasiswa.update', $ajuan->id), [
            'nama_kegiatan' => 'Workshop IoT dan Robotika Kampus 2026 (Revisi)',
            'tanggal_kegiatan' => now()->addDays(15)->toDateString(),
            'jenis_kegiatan' => 'Pelatihan',
            'items' => [
                [
                    'nama_akun' => 'Perlengkapan',
                    'nama_barang' => 'Modul ESP32 & Sensor Kit IoT',
                    'harga_satuan' => 150000,
                    'jumlah' => 8, // dikurangi dari 10 ke 8
                    'satuan' => 'Paket',
                    'durasi' => 1,
                    'satuan_durasi' => 'Keg',
                    'catatan' => 'Disesuaikan dengan pagu',
                ],
                [
                    'nama_akun' => 'Konsumsi',
                    'nama_barang' => 'Snack & Makan Siang Peserta',
                    'harga_satuan' => 30000,
                    'jumlah' => 35, // dikurangi dari 50 ke 35
                    'satuan' => 'Dus',
                    'durasi' => 1,
                    'satuan_durasi' => 'Hari',
                    'catatan' => '35 pax',
                ],
            ],
        ]);
        $updateResponse->assertRedirect(route('mahasiswa.show', $ajuan->id));

        $ajuan->refresh();
        $this->assertEquals('Workshop IoT dan Robotika Kampus 2026 (Revisi)', $ajuan->nama_kegiatan);
        $this->assertEquals('belum_disetujui', $ajuan->status_pembina);
        $this->assertEquals('menunggu_pembina', $ajuan->status_akhir);
        $this->assertEquals(1, $ajuan->revisi_count);
        $this->assertEquals(2250000, $ajuan->total_anggaran); // (150000*8*1) + (30000*35*1) = 1200000 + 1050000 = 2250000
    }

    /**
     * Test 5: Kemahasiswaan can request revision and Mahasiswa can edit and resubmit.
     */
    public function test_kemahasiswaan_can_request_revision_and_student_can_resubmit(): void
    {
        $kemahasiswaan = User::where('email', 'kemahasiswaan@univ.ac.id')->firstOrFail();
        $mahasiswaMusik = User::where('email', 'mahasiswa.musik@univ.ac.id')->firstOrFail();
        $ajuan = AjuanKegiatan::where('ukm_id', $mahasiswaMusik->ukm_id)->firstOrFail();

        // 1. Kemahasiswaan requests revision
        $response = $this->actingAs($kemahasiswaan)->post(route('kemahasiswaan.approve', $ajuan->id), [
            'status' => 'revisi',
            'catatan_kemahasiswaan' => 'Biaya sewa sound system harap dinegosiasi ulang atau lampirkan 3 pembanding vendor.',
        ]);
        $response->assertRedirect(route('kemahasiswaan.show', $ajuan->id));

        $ajuan->refresh();
        $this->assertEquals('revisi', $ajuan->status_kemahasiswaan);
        $this->assertEquals('revisi_kemahasiswaan', $ajuan->status_akhir);

        // 2. Mahasiswa views edit form & resubmits
        $updateResponse = $this->actingAs($mahasiswaMusik)->put(route('mahasiswa.update', $ajuan->id), [
            'nama_kegiatan' => $ajuan->nama_kegiatan,
            'tanggal_kegiatan' => $ajuan->tanggal_kegiatan->toDateString(),
            'jenis_kegiatan' => 'Lomba',
            'items' => [
                [
                    'nama_akun' => 'Sewa Peralatan',
                    'nama_barang' => 'Sewa Sound System Paket B (Hemat)',
                    'harga_satuan' => 2000000,
                    'jumlah' => 1,
                    'satuan' => 'Paket',
                    'durasi' => 1,
                    'satuan_durasi' => 'Hari',
                    'catatan' => 'Sudah dinegosiasi ulang',
                ],
            ],
        ]);
        $updateResponse->assertRedirect(route('mahasiswa.show', $ajuan->id));

        $ajuan->refresh();
        $this->assertEquals('belum_disetujui', $ajuan->status_kemahasiswaan);
        $this->assertEquals('menunggu_kemahasiswaan', $ajuan->status_akhir);
        $this->assertEquals(1, $ajuan->revisi_count);
    }

    /**
     * Test 6: Authorized roles (Mahasiswa, Pembina, Kemahasiswaan) can generate Lembar Pengesahan PDF.
     */
    public function test_authorized_users_can_generate_lembar_pengesahan_pdf(): void
    {
        $kemahasiswaan = User::where('email', 'kemahasiswaan@univ.ac.id')->firstOrFail();
        $pembinaRobotika = User::where('email', 'pembina.robotika@univ.ac.id')->firstOrFail();
        $mahasiswaRobotika = User::where('email', 'mahasiswa.robotika@univ.ac.id')->firstOrFail();
        $ajuanRobotika = AjuanKegiatan::where('ukm_id', $mahasiswaRobotika->ukm_id)->firstOrFail();

        // 1. Mahasiswa own UKM can generate PDF
        $responseMhs = $this->actingAs($mahasiswaRobotika)->get(route('mahasiswa.cetak-pengesahan', $ajuanRobotika->id));
        $responseMhs->assertStatus(200);
        $this->assertStringContainsString('application/pdf', $responseMhs->headers->get('content-type'));

        // 2. Pembina own UKM can generate PDF
        $responsePembina = $this->actingAs($pembinaRobotika)->get(route('pembina.cetak-pengesahan', $ajuanRobotika->id));
        $responsePembina->assertStatus(200);
        $this->assertStringContainsString('application/pdf', $responsePembina->headers->get('content-type'));

        // 3. Kemahasiswaan can generate PDF for any UKM
        $responseKmhs = $this->actingAs($kemahasiswaan)->get(route('kemahasiswaan.cetak-pengesahan', $ajuanRobotika->id));
        $responseKmhs->assertStatus(200);
        $this->assertStringContainsString('application/pdf', $responseKmhs->headers->get('content-type'));

        // 4. Test download query parameter (?download=1)
        $responseDownload = $this->actingAs($kemahasiswaan)->get(route('kemahasiswaan.cetak-pengesahan', ['id' => $ajuanRobotika->id, 'download' => 1]));
        $responseDownload->assertStatus(200);
        $this->assertStringContainsString('attachment', $responseDownload->headers->get('content-disposition'));
        $this->assertStringContainsString('.pdf', $responseDownload->headers->get('content-disposition'));
    }

    /**
     * Test 7: Unauthorized users cannot access PDF of another UKM.
     */
    public function test_unauthorized_user_cannot_access_other_ukm_pdf(): void
    {
        $mahasiswaMusik = User::where('email', 'mahasiswa.musik@univ.ac.id')->firstOrFail();
        $pembinaMusik = User::where('email', 'pembina.musik@univ.ac.id')->firstOrFail();
        $ajuanRobotika = AjuanKegiatan::whereHas('ukm', function ($q) {
            $q->where('kode', 'ROBOTIK');
        })->firstOrFail();

        // 1. Mahasiswa Musik cannot access PDF of UKM Robotika
        $responseMhs = $this->actingAs($mahasiswaMusik)->get(route('mahasiswa.cetak-pengesahan', $ajuanRobotika->id));
        $responseMhs->assertStatus(403);

        // 2. Pembina Musik cannot access PDF of UKM Robotika
        $responsePembina = $this->actingAs($pembinaMusik)->get(route('pembina.cetak-pengesahan', $ajuanRobotika->id));
        $responsePembina->assertStatus(403);
    }

    /**
     * Test 8: Rejected ajuan (ditolak) can still be revised and resubmitted by Mahasiswa.
     */
    public function test_rejected_ajuan_can_be_revised_and_resubmitted_by_student(): void
    {
        $kemahasiswaan = User::where('email', 'kemahasiswaan@univ.ac.id')->firstOrFail();
        $mahasiswaMusik = User::where('email', 'mahasiswa.musik@univ.ac.id')->firstOrFail();
        $ajuan = AjuanKegiatan::where('ukm_id', $mahasiswaMusik->ukm_id)->firstOrFail();

        // 1. Kemahasiswaan rejects the proposal with notes
        $response = $this->actingAs($kemahasiswaan)->post(route('kemahasiswaan.approve', $ajuan->id), [
            'status' => 'ditolak',
            'catatan_kemahasiswaan' => 'Usulan ditolak sementara karena sewa sound system terlalu tinggi. Silakan revisi harga.',
        ]);
        $response->assertRedirect(route('kemahasiswaan.show', $ajuan->id));

        $ajuan->refresh();
        $this->assertEquals('ditolak', $ajuan->status_kemahasiswaan);
        $this->assertEquals('ditolak', $ajuan->status_akhir);
        $this->assertTrue($ajuan->isPerluRevisi());
        $this->assertEquals('Usulan ditolak sementara karena sewa sound system terlalu tinggi. Silakan revisi harga.', $ajuan->catatan_revisi_terakhir);

        // 2. Mahasiswa views the show page and sees the rejection notice & edit link
        $showResponse = $this->actingAs($mahasiswaMusik)->get(route('mahasiswa.show', $ajuan->id));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Usulan Ditolak & Memerlukan Perbaikan');
        $showResponse->assertSee('Perbaiki Usulan Sekarang');

        // 3. Mahasiswa opens edit form
        $editResponse = $this->actingAs($mahasiswaMusik)->get(route('mahasiswa.edit', $ajuan->id));
        $editResponse->assertStatus(200);
        $editResponse->assertSee('Mode Perbaikan Usulan (Ditolak)');

        // 4. Mahasiswa resubmits with lower price
        $resubmitResponse = $this->actingAs($mahasiswaMusik)->put(route('mahasiswa.update', $ajuan->id), [
            'nama_kegiatan' => $ajuan->nama_kegiatan,
            'tanggal_kegiatan' => $ajuan->tanggal_kegiatan->toDateString(),
            'jenis_kegiatan' => 'Lomba',
            'items' => [
                [
                    'nama_akun' => 'Sewa Peralatan',
                    'nama_barang' => 'Sewa Sound System Hemat Diskon',
                    'harga_satuan' => 1500000,
                    'jumlah' => 1,
                    'satuan' => 'Paket',
                    'durasi' => 1,
                    'satuan_durasi' => 'Hari',
                    'catatan' => 'Harga telah diturunkan',
                ],
            ],
        ]);
        $resubmitResponse->assertRedirect(route('mahasiswa.show', $ajuan->id));

        $ajuan->refresh();
        $this->assertEquals('belum_disetujui', $ajuan->status_kemahasiswaan);
        $this->assertEquals('menunggu_kemahasiswaan', $ajuan->status_akhir);
        $this->assertEquals(1, $ajuan->revisi_count);
        $this->assertEquals(1500000, $ajuan->total_anggaran);
    }

    /**
     * Test 9: Kemahasiswaan can access Laporan dashboard and UKM drill-down detail.
     */
    public function test_kemahasiswaan_can_access_laporan_dashboard_and_detail(): void
    {
        $kemahasiswaan = User::where('email', 'kemahasiswaan@univ.ac.id')->firstOrFail();
        $ukmRobotika = Ukm::where('kode', 'ROBOTIK')->firstOrFail();

        // 1. Kemahasiswaan accesses Laporan index
        $response = $this->actingAs($kemahasiswaan)->get(route('kemahasiswaan.laporan'));
        $response->assertStatus(200);
        $response->assertSee('Laporan Realisasi Usulan Kegiatan per UKM');
        $response->assertSee('Tabel Rekapitulasi Realisasi per UKM');
        $response->assertSee('UKM Robotika');

        // 2. Kemahasiswaan accesses drill-down detail for a specific UKM
        $detailResponse = $this->actingAs($kemahasiswaan)->get(route('kemahasiswaan.laporan.detail', $ukmRobotika->id));
        $detailResponse->assertStatus(200);
        $detailResponse->assertSee('Laporan Detail UKM');
        $detailResponse->assertSee('UKM Robotika');
        $detailResponse->assertSee('Daftar Seluruh Usulan Kegiatan');
    }

    /**
     * Test 10: Kemahasiswaan can export Laporan to Excel (.xlsx).
     */
    public function test_kemahasiswaan_can_export_laporan_excel(): void
    {
        $kemahasiswaan = User::where('email', 'kemahasiswaan@univ.ac.id')->firstOrFail();

        $response = $this->actingAs($kemahasiswaan)->get(route('kemahasiswaan.laporan.export'));
        $response->assertStatus(200);
        $this->assertStringContainsString('attachment', $response->headers->get('content-disposition'));
        $this->assertStringContainsString('.xlsx', $response->headers->get('content-disposition'));
    }

    /**
     * Test 11: Non-kemahasiswaan roles cannot access Laporan.
     */
    public function test_unauthorized_users_cannot_access_laporan(): void
    {
        $mahasiswa = User::where('email', 'mahasiswa.robotika@univ.ac.id')->firstOrFail();
        $pembina = User::where('email', 'pembina.robotika@univ.ac.id')->firstOrFail();

        // 1. Mahasiswa cannot access Laporan
        $responseMhs = $this->actingAs($mahasiswa)->get(route('kemahasiswaan.laporan'));
        $responseMhs->assertStatus(403);

        // 2. Pembina cannot access Laporan
        $responsePembina = $this->actingAs($pembina)->get(route('kemahasiswaan.laporan'));
        $responsePembina->assertStatus(403);
    }

    /**
     * Test 12: Kemahasiswaan can CRUD Master UKM with safety deletion protection.
     */
    public function test_kemahasiswaan_can_crud_master_ukm(): void
    {
        $kemahasiswaan = User::where('email', 'kemahasiswaan@univ.ac.id')->firstOrFail();
        $ukmRobotika = Ukm::where('kode', 'ROBOTIK')->firstOrFail();

        // 1. Kemahasiswaan accesses master UKM index & create page
        $indexResponse = $this->actingAs($kemahasiswaan)->get(route('kemahasiswaan.ukm.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Master Data Unit Kegiatan Mahasiswa');

        $createResponse = $this->actingAs($kemahasiswaan)->get(route('kemahasiswaan.ukm.create'));
        $createResponse->assertStatus(200);

        // 2. Kemahasiswaan stores new UKM
        $storeResponse = $this->actingAs($kemahasiswaan)->post(route('kemahasiswaan.ukm.store'), [
            'kode' => 'KSR',
            'nama_ukm' => 'UKM Korp Sukarela PMI',
            'kategori' => 'Kesejahteraan & Sosial',
            'deskripsi' => 'Unit kemanusiaan dan kepalangmerahan kampus.',
            'pembina_nama_manual' => 'Dr. dr. Siti Rahma, Sp.A.',
        ]);
        $storeResponse->assertRedirect(route('kemahasiswaan.ukm.index'));
        $storeResponse->assertSessionHas('success');

        $this->assertDatabaseHas('ukms', [
            'kode' => 'KSR',
            'nama_ukm' => 'UKM Korp Sukarela PMI',
        ]);

        $newUkm = Ukm::where('kode', 'KSR')->firstOrFail();

        // 3. Kemahasiswaan views show page and edit page
        $showResponse = $this->actingAs($kemahasiswaan)->get(route('kemahasiswaan.ukm.show', $newUkm->id));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('UKM Korp Sukarela PMI');

        $editResponse = $this->actingAs($kemahasiswaan)->get(route('kemahasiswaan.ukm.edit', $newUkm->id));
        $editResponse->assertStatus(200);

        // 4. Kemahasiswaan updates UKM
        $updateResponse = $this->actingAs($kemahasiswaan)->put(route('kemahasiswaan.ukm.update', $newUkm->id), [
            'kode' => 'KSR',
            'nama_ukm' => 'UKM Korp Sukarela PMI Universitas',
            'kategori' => 'Kesejahteraan & Sosial',
            'deskripsi' => 'Profil telah diperbarui.',
            'pembina_nama_manual' => 'Dr. dr. Siti Rahma, Sp.A.',
        ]);
        $updateResponse->assertRedirect(route('kemahasiswaan.ukm.index'));
        $this->assertDatabaseHas('ukms', [
            'id' => $newUkm->id,
            'nama_ukm' => 'UKM Korp Sukarela PMI Universitas',
        ]);

        // 5. Safety deletion protection: Cannot delete UKM with existing ajuan
        $deleteProtectedResponse = $this->actingAs($kemahasiswaan)->delete(route('kemahasiswaan.ukm.destroy', $ukmRobotika->id));
        $deleteProtectedResponse->assertSessionHas('error');
        $this->assertDatabaseHas('ukms', ['id' => $ukmRobotika->id]);

        // 6. Delete new UKM without ajuan
        $deleteResponse = $this->actingAs($kemahasiswaan)->delete(route('kemahasiswaan.ukm.destroy', $newUkm->id));
        $deleteResponse->assertRedirect(route('kemahasiswaan.ukm.index'));
        $deleteResponse->assertSessionHas('success');
        $this->assertDatabaseMissing('ukms', ['id' => $newUkm->id]);
    }

    /**
     * Test 13: Kemahasiswaan can CRUD Users with safety deletion checks.
     */
    public function test_kemahasiswaan_can_crud_users_and_safety_checks(): void
    {
        $kemahasiswaan = User::where('email', 'kemahasiswaan@univ.ac.id')->firstOrFail();
        $ukmRobotika = Ukm::where('kode', 'ROBOTIK')->firstOrFail();
        $mahasiswaRobotika = User::where('email', 'mahasiswa.robotika@univ.ac.id')->firstOrFail();

        // 1. Access user index & create form
        $indexResponse = $this->actingAs($kemahasiswaan)->get(route('kemahasiswaan.users.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Manajemen Akun Pengguna Sistem');

        $createResponse = $this->actingAs($kemahasiswaan)->get(route('kemahasiswaan.users.create'));
        $createResponse->assertStatus(200);

        // 2. Create new student user
        $storeResponse = $this->actingAs($kemahasiswaan)->post(route('kemahasiswaan.users.store'), [
            'name' => 'Budi Pratama',
            'email' => 'budi.pratama@univ.ac.id',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'mahasiswa',
            'ukm_id' => $ukmRobotika->id,
            'nim_nip' => '2105101099',
            'no_hp' => '081299998888',
        ]);
        $storeResponse->assertRedirect(route('kemahasiswaan.users.index'));
        $storeResponse->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'email' => 'budi.pratama@univ.ac.id',
            'role' => 'mahasiswa',
        ]);

        $newUser = User::where('email', 'budi.pratama@univ.ac.id')->firstOrFail();

        // 3. Edit user
        $editResponse = $this->actingAs($kemahasiswaan)->get(route('kemahasiswaan.users.edit', $newUser->id));
        $editResponse->assertStatus(200);

        $updateResponse = $this->actingAs($kemahasiswaan)->put(route('kemahasiswaan.users.update', $newUser->id), [
            'name' => 'Budi Pratama Putra',
            'email' => 'budi.pratama@univ.ac.id',
            'role' => 'mahasiswa',
            'ukm_id' => $ukmRobotika->id,
            'nim_nip' => '2105101099',
            'no_hp' => '081299997777',
        ]);
        $updateResponse->assertRedirect(route('kemahasiswaan.users.index'));
        $this->assertDatabaseHas('users', [
            'id' => $newUser->id,
            'name' => 'Budi Pratama Putra',
        ]);

        // 4. Safety check 1: Cannot delete self
        $deleteSelfResponse = $this->actingAs($kemahasiswaan)->delete(route('kemahasiswaan.users.destroy', $kemahasiswaan->id));
        $deleteSelfResponse->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $kemahasiswaan->id]);

        // 5. Safety check 2: Cannot delete user with proposals
        $deleteUserWithAjuan = $this->actingAs($kemahasiswaan)->delete(route('kemahasiswaan.users.destroy', $mahasiswaRobotika->id));
        $deleteUserWithAjuan->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $mahasiswaRobotika->id]);

        // 6. Delete new user without proposals
        $deleteResponse = $this->actingAs($kemahasiswaan)->delete(route('kemahasiswaan.users.destroy', $newUser->id));
        $deleteResponse->assertRedirect(route('kemahasiswaan.users.index'));
        $deleteResponse->assertSessionHas('success');
        $this->assertDatabaseMissing('users', ['id' => $newUser->id]);
    }

    /**
     * Test 14: Non-kemahasiswaan users cannot access Master UKM and Users management.
     */
    public function test_unauthorized_users_cannot_access_master_data(): void
    {
        $mahasiswa = User::where('email', 'mahasiswa.robotika@univ.ac.id')->firstOrFail();
        $pembina = User::where('email', 'pembina.robotika@univ.ac.id')->firstOrFail();

        // 1. Mahasiswa
        $this->actingAs($mahasiswa)->get(route('kemahasiswaan.ukm.index'))->assertStatus(403);
        $this->actingAs($mahasiswa)->get(route('kemahasiswaan.users.index'))->assertStatus(403);

        // 2. Pembina
        $this->actingAs($pembina)->get(route('kemahasiswaan.ukm.index'))->assertStatus(403);
        $this->actingAs($pembina)->get(route('kemahasiswaan.users.index'))->assertStatus(403);
    }

    /**
     * Test 15: Kemahasiswaan navigation renders Setting dropdown with Master and User Management submenus.
     */
    public function test_kemahasiswaan_navigation_has_setting_dropdown_with_submenus(): void
    {
        $kemahasiswaan = User::where('email', 'kemahasiswaan@univ.ac.id')->firstOrFail();
        $mahasiswa = User::where('email', 'mahasiswa.robotika@univ.ac.id')->firstOrFail();

        // 1. Kemahasiswaan can see Setting dropdown and its submenus
        $response = $this->actingAs($kemahasiswaan)->get(route('kemahasiswaan.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Setting');
        $response->assertSee('Master Data');
        $response->assertSee('Master UKM');
        $response->assertSee('Master Satuan (RAB)');
        $response->assertSee('User Management');

        // 2. Mahasiswa does not have the Setting dropdown for master management
        $mhsResponse = $this->actingAs($mahasiswa)->get(route('mahasiswa.dashboard'));
        $mhsResponse->assertStatus(200);
        $mhsResponse->assertDontSee('Master Data UKM');
    }

    /**
     * Test 16: Mahasiswa can edit proposal details and RAB items before approval (menunggu_pembina or menunggu_kemahasiswaan).
     */
    public function test_mahasiswa_can_edit_proposal_and_details_before_approval(): void
    {
        Storage::fake('public');
        $mahasiswa = User::where('email', 'mahasiswa.robotika@univ.ac.id')->firstOrFail();
        $file = UploadedFile::fake()->create('proposal_awal.pdf', 200, 'application/pdf');

        // Create initial proposal in 'menunggu_pembina' status
        $this->actingAs($mahasiswa)->post(route('mahasiswa.store'), [
            'nama_kegiatan' => 'Workshop IoT Awal',
            'tanggal_kegiatan' => now()->addDays(15)->toDateString(),
            'jenis_kegiatan' => 'Pelatihan',
            'dokumen_kegiatan' => $file,
            'items' => [
                [
                    'nama_akun' => 'Perlengkapan',
                    'nama_barang' => 'ESP32 Development Board',
                    'harga_satuan' => 75000,
                    'jumlah' => 10,
                    'satuan' => 'Unit',
                    'durasi' => 1,
                    'satuan_durasi' => 'Hari',
                    'catatan' => 'Item awal',
                ],
            ],
        ]);

        $ajuan = AjuanKegiatan::where('nama_kegiatan', 'Workshop IoT Awal')->firstOrFail();
        $this->assertEquals('menunggu_pembina', $ajuan->status_akhir);
        $this->assertTrue($ajuan->isBisaDieditMahasiswa());

        // 1. Edit page accessible while menunggu_pembina
        $editPage = $this->actingAs($mahasiswa)->get(route('mahasiswa.edit', $ajuan->id));
        $editPage->assertStatus(200);
        $editPage->assertSee('Mode Edit Usulan Kegiatan');
        $editPage->assertSee('Simpan Perubahan Usulan');

        // 2. Update proposal and detail items
        $updateResponse = $this->actingAs($mahasiswa)->put(route('mahasiswa.update', $ajuan->id), [
            'nama_kegiatan' => 'Workshop IoT Lanjutan 2026',
            'tanggal_kegiatan' => now()->addDays(18)->toDateString(),
            'jenis_kegiatan' => 'Pelatihan',
            'items' => [
                [
                    'nama_akun' => 'Perlengkapan',
                    'nama_barang' => 'ESP32 NodeMCU V3',
                    'harga_satuan' => 85000,
                    'jumlah' => 15,
                    'satuan' => 'Unit',
                    'durasi' => 2,
                    'satuan_durasi' => 'Hari',
                    'catatan' => 'Revisi spesifikasi board',
                ],
                [
                    'nama_akun' => 'Konsumsi',
                    'nama_barang' => 'Snack Box',
                    'harga_satuan' => 15000,
                    'jumlah' => 20,
                    'satuan' => 'Porsi',
                    'durasi' => 2,
                    'satuan_durasi' => 'Hari',
                    'catatan' => 'Snack 2 hari',
                ],
            ],
        ]);

        $updateResponse->assertRedirect(route('mahasiswa.show', $ajuan->id));
        $updateResponse->assertSessionHas('success');

        $ajuan->refresh();
        $this->assertEquals('Workshop IoT Lanjutan 2026', $ajuan->nama_kegiatan);
        $this->assertEquals('menunggu_pembina', $ajuan->status_akhir);
        // Total anggaran: (85000 * 15 * 2) + (15000 * 20 * 2) = 2550000 + 600000 = 3150000
        $this->assertEquals(3150000, $ajuan->total_anggaran);
        $this->assertCount(2, $ajuan->details);
        $this->assertEquals('ESP32 NodeMCU V3', $ajuan->details[0]->nama_barang);
        $this->assertEquals(2550000, $ajuan->details[0]->total);
        $this->assertEquals('Snack Box', $ajuan->details[1]->nama_barang);
        $this->assertEquals(600000, $ajuan->details[1]->total);

        // 3. Move to 'menunggu_kemahasiswaan' (Pembina approves)
        $pembina = User::where('email', 'pembina.robotika@univ.ac.id')->firstOrFail();
        $this->actingAs($pembina)->post(route('pembina.verify', $ajuan->id), [
            'status' => 'disetujui',
            'catatan' => 'Disetujui pembina',
        ]);

        $ajuan->refresh();
        $this->assertEquals('menunggu_kemahasiswaan', $ajuan->status_akhir);
        $this->assertTrue($ajuan->isBisaDieditMahasiswa());

        // 4. Mahasiswa can still edit while waiting for Kemahasiswaan
        $editPage2 = $this->actingAs($mahasiswa)->get(route('mahasiswa.edit', $ajuan->id));
        $editPage2->assertStatus(200);

        $updateResponse2 = $this->actingAs($mahasiswa)->put(route('mahasiswa.update', $ajuan->id), [
            'nama_kegiatan' => 'Workshop IoT Lanjutan 2026 Final Update',
            'tanggal_kegiatan' => now()->addDays(19)->toDateString(),
            'jenis_kegiatan' => 'Pelatihan',
            'items' => [
                [
                    'nama_akun' => 'Perlengkapan',
                    'nama_barang' => 'ESP32 NodeMCU V3 Pro',
                    'harga_satuan' => 90000,
                    'jumlah' => 15,
                    'satuan' => 'Unit',
                    'durasi' => 1,
                    'satuan_durasi' => 'Hari',
                    'catatan' => 'Penyesuaian 1 hari',
                ],
            ],
        ]);
        $updateResponse2->assertRedirect(route('mahasiswa.show', $ajuan->id));

        $ajuan->refresh();
        $this->assertEquals('Workshop IoT Lanjutan 2026 Final Update', $ajuan->nama_kegiatan);
        $this->assertEquals('menunggu_kemahasiswaan', $ajuan->status_akhir);
        $this->assertEquals(1350000, $ajuan->total_anggaran);
        $this->assertCount(1, $ajuan->details);
    }

    /**
     * Test 17: Mahasiswa cannot edit once ajuan is officially approved by Kemahasiswaan.
     */
    public function test_mahasiswa_cannot_edit_once_approved(): void
    {
        $mahasiswa = User::where('email', 'mahasiswa.robotika@univ.ac.id')->firstOrFail();
        $kemahasiswaan = User::where('email', 'kemahasiswaan@univ.ac.id')->firstOrFail();

        $ajuan = AjuanKegiatan::create([
            'user_id' => $mahasiswa->id,
            'ukm_id' => $mahasiswa->ukm_id,
            'nama_kegiatan' => 'Kegiatan Sudah Disetujui',
            'tanggal_kegiatan' => now()->addDays(10)->toDateString(),
            'jenis_kegiatan' => 'Lomba',
            'dokumen_proposal' => 'test.pdf',
            'dokumen_nama_asli' => 'test.pdf',
            'total_anggaran' => 100000,
            'status_pembina' => 'disetujui',
            'status_kemahasiswaan' => 'disetujui',
            'status_akhir' => 'disetujui',
        ]);

        $this->assertFalse($ajuan->isBisaDieditMahasiswa());

        // Attempting to visit edit page should redirect with error
        $response = $this->actingAs($mahasiswa)->get(route('mahasiswa.edit', $ajuan->id));
        $response->assertRedirect(route('mahasiswa.show', $ajuan->id));
        $response->assertSessionHas('error');

        // Attempting to PUT update should redirect with error
        $putResponse = $this->actingAs($mahasiswa)->put(route('mahasiswa.update', $ajuan->id), [
            'nama_kegiatan' => 'Coba Ubah Yang Sudah Disetujui',
            'tanggal_kegiatan' => now()->addDays(10)->toDateString(),
            'jenis_kegiatan' => 'Lomba',
            'items' => [
                [
                    'nama_akun' => 'Test',
                    'nama_barang' => 'Test',
                    'harga_satuan' => 1000,
                    'jumlah' => 1,
                    'satuan' => 'Unit',
                    'durasi' => 1,
                    'satuan_durasi' => 'Hari',
                ],
            ],
        ]);
        $putResponse->assertRedirect(route('mahasiswa.show', $ajuan->id));
        $putResponse->assertSessionHas('error');
    }

    /**
     * Test 18: Mahasiswa create and edit views have Jenis Kegiatan dropdown with Lomba, Pelatihan, Lainnya.
     */
    public function test_mahasiswa_create_and_edit_views_have_jenis_kegiatan_dropdown(): void
    {
        $mahasiswa = User::where('email', 'mahasiswa.robotika@univ.ac.id')->firstOrFail();

        // 1. Check create view
        $createResponse = $this->actingAs($mahasiswa)->get(route('mahasiswa.create'));
        $createResponse->assertStatus(200);
        $createResponse->assertSee('Jenis Kegiatan');
        $createResponse->assertSee('Lomba');
        $createResponse->assertSee('Pelatihan');
        $createResponse->assertSee('Lainnya');

        // 2. Check edit view
        $ajuan = AjuanKegiatan::where('ukm_id', $mahasiswa->ukm_id)->firstOrFail();
        $editResponse = $this->actingAs($mahasiswa)->get(route('mahasiswa.edit', $ajuan->id));
        $editResponse->assertStatus(200);
        $editResponse->assertSee('Jenis Kegiatan');
        $editResponse->assertSee('Lomba');
        $editResponse->assertSee('Pelatihan');
        $editResponse->assertSee('Lainnya');
    }

    /**
     * Test 19: Jenis Kegiatan validation requires valid option (Lomba, Pelatihan, Lainnya).
     */
    public function test_jenis_kegiatan_validation(): void
    {
        Storage::fake('public');
        $mahasiswa = User::where('email', 'mahasiswa.robotika@univ.ac.id')->firstOrFail();
        $file = UploadedFile::fake()->create('test.pdf', 100, 'application/pdf');

        // Missing jenis_kegiatan
        $res1 = $this->actingAs($mahasiswa)->post(route('mahasiswa.store'), [
            'nama_kegiatan' => 'Kegiatan Tanpa Jenis',
            'tanggal_kegiatan' => now()->addDays(5)->toDateString(),
            'dokumen_kegiatan' => $file,
            'items' => [
                [
                    'nama_akun' => 'Test',
                    'nama_barang' => 'Test',
                    'harga_satuan' => 1000,
                    'jumlah' => 1,
                    'satuan' => 'Unit',
                    'durasi' => 1,
                    'satuan_durasi' => 'Hari',
                ],
            ],
        ]);
        $res1->assertSessionHasErrors('jenis_kegiatan');

        // Invalid jenis_kegiatan
        $res2 = $this->actingAs($mahasiswa)->post(route('mahasiswa.store'), [
            'nama_kegiatan' => 'Kegiatan Jenis Salah',
            'tanggal_kegiatan' => now()->addDays(5)->toDateString(),
            'jenis_kegiatan' => 'KonserMusik', // Invalid
            'dokumen_kegiatan' => $file,
            'items' => [
                [
                    'nama_akun' => 'Test',
                    'nama_barang' => 'Test',
                    'harga_satuan' => 1000,
                    'jumlah' => 1,
                    'satuan' => 'Unit',
                    'durasi' => 1,
                    'satuan_durasi' => 'Hari',
                ],
            ],
        ]);
        $res2->assertSessionHasErrors('jenis_kegiatan');
    }

    /**
     * Test 20: Kemahasiswaan can edit RAB detail items, add new items, and recalculate total_anggaran.
     */
    public function test_kemahasiswaan_can_edit_rab_detail_items_and_recalculate_total(): void
    {
        $kemahasiswaan = User::where('email', 'kemahasiswaan@univ.ac.id')->firstOrFail();
        $ajuan = AjuanKegiatan::with('details')->firstOrFail();

        // 1. Visit Kemahasiswaan show page (e.g. https://ukmkeg.com/kemahasiswaan/pengajuan/{id})
        $response = $this->actingAs($kemahasiswaan)->get(route('kemahasiswaan.show', $ajuan->id));
        $response->assertStatus(200);
        $response->assertSee('Aktifkan Edit Tabel RAB');

        $firstDetail = $ajuan->details->first();

        // 2. Submit edited RAB items: update the first item, add a brand new item, and leave out others (if any) to test deletion
        $postData = [
            'items' => [
                [
                    'id' => $firstDetail->id,
                    'nama_akun' => 'Honorarium Diperbarui',
                    'nama_barang' => 'Honorarium Juri Nasional',
                    'harga_satuan' => 1500000,
                    'jumlah' => 2,
                    'satuan' => 'Orang',
                    'durasi' => 2,
                    'satuan_durasi' => 'Hari',
                    'status_keterangan' => 'disetujui',
                    'catatan' => 'Disesuaikan dengan SBM kemahasiswaan terbaru',
                ],
                [
                    'id' => null, // new row
                    'nama_akun' => 'Perlengkapan',
                    'nama_barang' => 'Sewa Sound System & Lighting Tambahan',
                    'harga_satuan' => 2000000,
                    'jumlah' => 1,
                    'satuan' => 'Paket',
                    'durasi' => 1,
                    'satuan_durasi' => 'Hari',
                    'status_keterangan' => 'disetujui',
                    'catatan' => 'Disetujui untuk mendukung panggung utama',
                ],
            ],
        ];

        $updateResponse = $this->actingAs($kemahasiswaan)->post(
            route('kemahasiswaan.update-details', $ajuan->id),
            $postData
        );

        $updateResponse->assertRedirect(route('kemahasiswaan.show', $ajuan->id));
        $updateResponse->assertSessionHas('success');

        // 3. Verify DB changes
        $ajuan->refresh();
        // Item 1: 1.500.000 * 2 * 2 = 6.000.000
        // Item 2: 2.000.000 * 1 * 1 = 2.000.000
        // Total = 8.000.000
        $this->assertEquals(8000000, (float)$ajuan->total_anggaran);
        $this->assertCount(2, $ajuan->details);

        $this->assertDatabaseHas('detail_kegiatans', [
            'id' => $firstDetail->id,
            'ajuan_kegiatan_id' => $ajuan->id,
            'nama_akun' => 'Honorarium Diperbarui',
            'nama_barang' => 'Honorarium Juri Nasional',
            'harga_satuan' => 1500000,
            'jumlah' => 2,
            'durasi' => 2,
            'total' => 6000000,
            'status_keterangan' => 'disetujui',
            'catatan' => 'Disesuaikan dengan SBM kemahasiswaan terbaru',
        ]);

        $this->assertDatabaseHas('detail_kegiatans', [
            'ajuan_kegiatan_id' => $ajuan->id,
            'nama_akun' => 'Perlengkapan',
            'nama_barang' => 'Sewa Sound System & Lighting Tambahan',
            'harga_satuan' => 2000000,
            'jumlah' => 1,
            'durasi' => 1,
            'total' => 2000000,
            'status_keterangan' => 'disetujui',
        ]);
    }
}


