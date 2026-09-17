<?php

namespace Tests\Feature;

use App\Models\DetailKegiatan;
use App\Models\Satuan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterSatuanTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /**
     * Pastikan 11 satuan bawaan terisi di database sesuai permintaan user.
     */
    public function test_default_satuans_are_seeded(): void
    {
        $expected = ['Buah', 'Dus', 'Bulan', 'Hari', 'Jam', 'Kali', 'Sesi', 'Org', 'Unit', 'Paket', 'Keg'];

        foreach ($expected as $item) {
            $this->assertDatabaseHas('satuans', [
                'nama_satuan' => $item,
                'is_aktif' => true,
            ]);
        }
    }

    /**
     * Pastikan form mahasiswa pengajuan create menampilkan opsi satuan dinamis dari database.
     */
    public function test_mahasiswa_create_view_displays_satuans_dropdown(): void
    {
        $mahasiswa = User::where('email', 'mahasiswa.robotika@univ.ac.id')->firstOrFail();

        $response = $this->actingAs($mahasiswa)->get(route('mahasiswa.create'));
        $response->assertStatus(200);

        $expected = ['Buah', 'Dus', 'Bulan', 'Hari', 'Jam', 'Kali', 'Sesi', 'Org', 'Unit', 'Paket', 'Keg'];
        foreach ($expected as $item) {
            $response->assertSee($item);
        }
    }

    /**
     * Pastikan tabel RAB memiliki kolom Satuan (untuk Jumlah) dan Sat. Durasi (untuk Durasi).
     */
    public function test_mahasiswa_create_view_has_both_satuan_jumlah_and_satuan_durasi_columns(): void
    {
        $mahasiswa = User::where('email', 'mahasiswa.robotika@univ.ac.id')->firstOrFail();

        $response = $this->actingAs($mahasiswa)->get(route('mahasiswa.create'));
        $response->assertStatus(200);

        $response->assertSee('Satuan');
        $response->assertSee('Sat. Durasi');
        $response->assertSee('item.satuan');
        $response->assertSee('item.satuan_durasi');
    }

    /**
     * Biro Kemahasiswaan dapat mengakses halaman master data satuan.
     */
    public function test_kemahasiswaan_can_view_master_satuan(): void
    {
        $kemahasiswaan = User::where('role', 'kemahasiswaan')->firstOrFail();

        $response = $this->actingAs($kemahasiswaan)->get(route('kemahasiswaan.satuan.index'));
        $response->assertStatus(200);
        $response->assertSee('Master Data Satuan Barang');
        $response->assertSee('Buah');
        $response->assertSee('Paket');
    }

    /**
     * Biro Kemahasiswaan dapat menambahkan satuan baru secara dinamis.
     */
    public function test_kemahasiswaan_can_create_new_satuan_and_it_shows_in_create_form(): void
    {
        $kemahasiswaan = User::where('role', 'kemahasiswaan')->firstOrFail();
        $mahasiswa = User::where('email', 'mahasiswa.robotika@univ.ac.id')->firstOrFail();

        // Tambah satuan baru
        $response = $this->actingAs($kemahasiswaan)->post(route('kemahasiswaan.satuan.store'), [
            'nama_satuan' => 'Rim Kertas',
            'keterangan' => 'Satuan kertas cetak HVS',
            'is_aktif' => 1,
        ]);

        $response->assertRedirect(route('kemahasiswaan.satuan.index'));
        $this->assertDatabaseHas('satuans', [
            'nama_satuan' => 'Rim Kertas',
            'is_aktif' => true,
        ]);

        // Cek apakah langsung muncul di form mahasiswa create
        $mhsResponse = $this->actingAs($mahasiswa)->get(route('mahasiswa.create'));
        $mhsResponse->assertStatus(200);
        $mhsResponse->assertSee('Rim Kertas');
    }

    /**
     * Biro Kemahasiswaan dapat memperbarui master satuan.
     */
    public function test_kemahasiswaan_can_update_satuan(): void
    {
        $kemahasiswaan = User::where('role', 'kemahasiswaan')->firstOrFail();
        $satuan = Satuan::where('nama_satuan', 'Dus')->firstOrFail();

        $response = $this->actingAs($kemahasiswaan)->put(route('kemahasiswaan.satuan.update', $satuan->id), [
            'nama_satuan' => 'Karton (Dus)',
            'keterangan' => 'Kemasan dus/karton karton tebal',
            'is_aktif' => 1,
        ]);

        $response->assertRedirect(route('kemahasiswaan.satuan.index'));
        $this->assertDatabaseHas('satuans', [
            'id' => $satuan->id,
            'nama_satuan' => 'Karton (Dus)',
        ]);
    }

    /**
     * Satuan yang belum terpakai dapat dihapus.
     */
    public function test_kemahasiswaan_can_delete_unused_satuan(): void
    {
        $kemahasiswaan = User::where('role', 'kemahasiswaan')->firstOrFail();

        $satuanBaru = Satuan::create([
            'nama_satuan' => 'Galon',
            'keterangan' => 'Air minum galon',
            'is_aktif' => true,
        ]);

        $response = $this->actingAs($kemahasiswaan)->delete(route('kemahasiswaan.satuan.destroy', $satuanBaru->id));
        $response->assertRedirect(route('kemahasiswaan.satuan.index'));
        $this->assertDatabaseMissing('satuans', [
            'nama_satuan' => 'Galon',
        ]);
    }

    /**
     * Mahasiswa dan Pembina tidak boleh mengakses halaman kelola master satuan.
     */
    public function test_unauthorized_users_cannot_access_master_satuan(): void
    {
        $mahasiswa = User::where('role', 'mahasiswa')->firstOrFail();
        $pembina = User::where('role', 'pembina')->firstOrFail();

        $responseMhs = $this->actingAs($mahasiswa)->get(route('kemahasiswaan.satuan.index'));
        $responseMhs->assertStatus(403);

        $responsePembina = $this->actingAs($pembina)->get(route('kemahasiswaan.satuan.index'));
        $responsePembina->assertStatus(403);
    }
}
