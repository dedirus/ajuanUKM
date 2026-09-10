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
            'dokumen_kegiatan' => $file,
            'items' => [
                [
                    'nama_akun' => 'Perlengkapan',
                    'nama_barang' => 'Arena Lintasan Line Follower',
                    'harga_satuan' => 1200000,
                    'jumlah' => 2,
                    'catatan' => 'Sesuai regulasi nasional',
                ],
                [
                    'nama_akun' => 'Konsumsi',
                    'nama_barang' => 'Makan Siang Peserta',
                    'harga_satuan' => 30000,
                    'jumlah' => 40,
                    'catatan' => '40 pax',
                ],
            ],
        ];

        $submitResponse = $this->actingAs($mahasiswa)->post(route('mahasiswa.store'), $postData);
        $submitResponse->assertRedirect();

        // 3. Verify in database
        $this->assertDatabaseHas('ajuan_kegiatans', [
            'nama_kegiatan' => 'RoboFest Championship 2026',
            'ukm_id' => $mahasiswa->ukm_id,
            'status_pembina' => 'belum_disetujui',
            'status_kemahasiswaan' => 'belum_disetujui',
            'status_akhir' => 'menunggu_pembina',
            'total_anggaran' => 3600000, // (1200000*2) + (30000*40) = 2400000 + 1200000 = 3600000
        ]);

        $ajuan = AjuanKegiatan::where('nama_kegiatan', 'RoboFest Championship 2026')->first();
        $this->assertCount(2, $ajuan->details);
        $this->assertEquals('Arena Lintasan Line Follower', $ajuan->details[0]->nama_barang);
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
            'items' => [
                [
                    'nama_akun' => 'Perlengkapan',
                    'nama_barang' => 'Modul ESP32 & Sensor Kit IoT',
                    'harga_satuan' => 150000,
                    'jumlah' => 8, // dikurangi dari 10 ke 8
                    'catatan' => 'Disesuaikan dengan pagu',
                ],
                [
                    'nama_akun' => 'Konsumsi',
                    'nama_barang' => 'Snack & Makan Siang Peserta',
                    'harga_satuan' => 30000,
                    'jumlah' => 35, // dikurangi dari 50 ke 35
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
        $this->assertEquals(2250000, $ajuan->total_anggaran); // (150000*8) + (30000*35) = 1200000 + 1050000 = 2250000
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
            'items' => [
                [
                    'nama_akun' => 'Sewa Peralatan',
                    'nama_barang' => 'Sewa Sound System Paket B (Hemat)',
                    'harga_satuan' => 2000000,
                    'jumlah' => 1,
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
            'items' => [
                [
                    'nama_akun' => 'Sewa Peralatan',
                    'nama_barang' => 'Sewa Sound System Hemat Diskon',
                    'harga_satuan' => 1500000,
                    'jumlah' => 1,
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
}
