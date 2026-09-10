<?php

namespace Database\Seeders;

use App\Models\AjuanKegiatan;
use App\Models\DetailKegiatan;
use App\Models\Ukm;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AjuanUKMSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Buat UKM
        $ukmRobotika = Ukm::firstOrCreate(
            ['kode' => 'ROBOTIK'],
            [
                'nama_ukm' => 'UKM Robotika & Otomasi',
                'kategori' => 'Penalaran & Keilmuan',
                'deskripsi' => 'Unit kegiatan mahasiswa riset robotika, IoT, dan kecerdasan buatan.',
                'pembina_nama' => 'Dr. Ir. Hendra Setiawan, M.T.',
            ]
        );

        $ukmMusik = Ukm::firstOrCreate(
            ['kode' => 'MUSIK'],
            [
                'nama_ukm' => 'UKM Seni Musik Kampus',
                'kategori' => 'Seni & Budaya',
                'deskripsi' => 'Pengembangan minat bakat musik modern, akustik, dan produksi audio.',
                'pembina_nama' => 'Rina Wijayanti, S.Sn., M.Pd.',
            ]
        );

        $ukmBasket = Ukm::firstOrCreate(
            ['kode' => 'BASKET'],
            [
                'nama_ukm' => 'UKM Bola Basket Patriot',
                'kategori' => 'Olahraga',
                'deskripsi' => 'Wadah latihan dan kompetisi olahraga bola basket mahasiswa.',
                'pembina_nama' => 'Bambang Sudibyo, S.Pd., M.Or.',
            ]
        );

        $ukmMapala = Ukm::firstOrCreate(
            ['kode' => 'MAPALA'],
            [
                'nama_ukm' => 'UKM Mahasiswa Pecinta Alam (MAPALA)',
                'kategori' => 'Kepetualangan & Lingkungan',
                'deskripsi' => 'Kegiatan kepedulian lingkungan hidup dan eksplorasi alam terbuka.',
                'pembina_nama' => 'Agus Priyono, M.Sc.',
            ]
        );

        // 2. Buat User Mahasiswa
        $mhsRobotika = User::firstOrCreate(
            ['email' => 'mahasiswa.robotika@univ.ac.id'],
            [
                'name' => 'Ahmad Fauzi (Mhs Robotika)',
                'password' => Hash::make('password'),
                'role' => 'mahasiswa',
                'ukm_id' => $ukmRobotika->id,
                'nim_nip' => '2105101001',
                'no_hp' => '081234567890',
            ]
        );

        $mhsMusik = User::firstOrCreate(
            ['email' => 'mahasiswa.musik@univ.ac.id'],
            [
                'name' => 'Putri Ayu (Mhs Musik)',
                'password' => Hash::make('password'),
                'role' => 'mahasiswa',
                'ukm_id' => $ukmMusik->id,
                'nim_nip' => '2105101002',
                'no_hp' => '081234567891',
            ]
        );

        $mhsBasket = User::firstOrCreate(
            ['email' => 'mahasiswa.basket@univ.ac.id'],
            [
                'name' => 'Dimas Arya (Mhs Basket)',
                'password' => Hash::make('password'),
                'role' => 'mahasiswa',
                'ukm_id' => $ukmBasket->id,
                'nim_nip' => '2105101003',
                'no_hp' => '081234567892',
            ]
        );

        // 3. Buat User Pembina
        $pembinaRobotika = User::firstOrCreate(
            ['email' => 'pembina.robotika@univ.ac.id'],
            [
                'name' => 'Dr. Ir. Hendra Setiawan, M.T. (Pembina Robotika)',
                'password' => Hash::make('password'),
                'role' => 'pembina',
                'ukm_id' => $ukmRobotika->id,
                'nim_nip' => '198005122005011002',
                'no_hp' => '081398765432',
            ]
        );

        $pembinaMusik = User::firstOrCreate(
            ['email' => 'pembina.musik@univ.ac.id'],
            [
                'name' => 'Rina Wijayanti, M.Pd. (Pembina Musik)',
                'password' => Hash::make('password'),
                'role' => 'pembina',
                'ukm_id' => $ukmMusik->id,
                'nim_nip' => '198403252008122001',
                'no_hp' => '081398765433',
            ]
        );

        $pembinaBasket = User::firstOrCreate(
            ['email' => 'pembina.basket@univ.ac.id'],
            [
                'name' => 'Bambang Sudibyo, M.Or. (Pembina Basket)',
                'password' => Hash::make('password'),
                'role' => 'pembina',
                'ukm_id' => $ukmBasket->id,
                'nim_nip' => '197809142002121004',
                'no_hp' => '081398765434',
            ]
        );

        // 4. Buat User Kemahasiswaan
        $kemahasiswaan = User::firstOrCreate(
            ['email' => 'kemahasiswaan@univ.ac.id'],
            [
                'name' => 'Biro Kemahasiswaan & Alumni',
                'password' => Hash::make('password'),
                'role' => 'kemahasiswaan',
                'ukm_id' => null,
                'nim_nip' => '197501012000031001',
                'no_hp' => '081122334455',
            ]
        );

        // Siapkan dummy file proposal di storage
        Storage::disk('public')->makeDirectory('dokumen_proposal');
        $dummyPdfPath = 'dokumen_proposal/sample_proposal.pdf';
        if (!Storage::disk('public')->exists($dummyPdfPath)) {
            // PDF header dummy minimal
            $pdfContent = "%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\n2 0 obj<</Type/Pages/Count 1/Kids[3 0 R]>>endobj\n3 0 obj<</Type/Page/MediaBox[0 0 595 842]/Parent 2 0 R/Resources<<>>>>endobj\nxref\n0 4\n0000000000 65535 f\n0000000009 00000 n\n0000000052 00000 n\n0000000101 00000 n\ntrailer<</Size 4/Root 1 0 R>>\nstartxref\n178\n%%EOF";
            Storage::disk('public')->put($dummyPdfPath, $pdfContent);
        }

        // 5. Buat Sampel Ajuan 1: Robotika (Status: Menunggu Verifikasi Pembina)
        $ajuan1 = AjuanKegiatan::create([
            'user_id' => $mhsRobotika->id,
            'ukm_id' => $ukmRobotika->id,
            'nama_kegiatan' => 'Workshop IoT dan Robotika Kampus 2026',
            'tanggal_kegiatan' => now()->addDays(14)->toDateString(),
            'dokumen_proposal' => $dummyPdfPath,
            'dokumen_nama_asli' => 'Proposal_Workshop_IoT_Robotika.pdf',
            'total_anggaran' => 3850000,
            'status_pembina' => 'belum_disetujui',
            'catatan_pembina' => null,
            'status_kemahasiswaan' => 'belum_disetujui',
            'status_akhir' => 'menunggu_pembina',
        ]);

        DetailKegiatan::create([
            'ajuan_kegiatan_id' => $ajuan1->id,
            'no_urut' => 1,
            'nama_akun' => 'Perlengkapan & Modul',
            'nama_barang' => 'Modul ESP32 & Sensor Kit IoT',
            'harga_satuan' => 150000,
            'jumlah' => 10,
            'total' => 1500000,
            'status_keterangan' => 'belum_diverifikasi',
            'catatan' => 'Untuk bahan praktik peserta workshop',
        ]);

        DetailKegiatan::create([
            'ajuan_kegiatan_id' => $ajuan1->id,
            'no_urut' => 2,
            'nama_akun' => 'Konsumsi',
            'nama_barang' => 'Snack & Makan Siang Peserta & Panitia',
            'harga_satuan' => 35000,
            'jumlah' => 50,
            'total' => 1750000,
            'status_keterangan' => 'belum_diverifikasi',
            'catatan' => '50 pax x 1 hari pelaksanaan',
        ]);

        DetailKegiatan::create([
            'ajuan_kegiatan_id' => $ajuan1->id,
            'no_urut' => 3,
            'nama_akun' => 'Kesekretariatan',
            'nama_barang' => 'Sertifikat, Nametag & Blocknote',
            'harga_satuan' => 12000,
            'jumlah' => 50,
            'total' => 600000,
            'status_keterangan' => 'belum_diverifikasi',
            'catatan' => 'Cetak bahan kit seminar',
        ]);

        // 6. Buat Sampel Ajuan 2: Musik (Status: Sudah Disetujui Pembina, Menunggu Kemahasiswaan)
        $ajuan2 = AjuanKegiatan::create([
            'user_id' => $mhsMusik->id,
            'ukm_id' => $ukmMusik->id,
            'nama_kegiatan' => 'Festival Musik Akustik dan Parade Seni Mahasiswa',
            'tanggal_kegiatan' => now()->addDays(21)->toDateString(),
            'dokumen_proposal' => $dummyPdfPath,
            'dokumen_nama_asli' => 'TOR_Festival_Akustik_2026.pdf',
            'total_anggaran' => 5500000,
            'status_pembina' => 'disetujui',
            'catatan_pembina' => 'Kegiatan sangat positif, mohon koordinasi sound system dijaga agar tidak mengganggu perkuliahan.',
            'verified_pembina_at' => now()->subDay(),
            'verified_pembina_by' => $pembinaMusik->id,
            'status_kemahasiswaan' => 'belum_disetujui',
            'status_akhir' => 'menunggu_kemahasiswaan',
        ]);

        DetailKegiatan::create([
            'ajuan_kegiatan_id' => $ajuan2->id,
            'no_urut' => 1,
            'nama_akun' => 'Sewa Peralatan',
            'nama_barang' => 'Sewa Sound System & Lighting Panggung',
            'harga_satuan' => 3000000,
            'jumlah' => 1,
            'total' => 3000000,
            'status_keterangan' => 'belum_diverifikasi',
            'catatan' => 'Sewa 1 set outdoor panggung kecil',
        ]);

        DetailKegiatan::create([
            'ajuan_kegiatan_id' => $ajuan2->id,
            'no_urut' => 2,
            'nama_akun' => 'Hadiah & Apresiasi',
            'nama_barang' => 'Piala & Plakat Juara Akustik',
            'harga_satuan' => 250000,
            'jumlah' => 4,
            'total' => 1000000,
            'status_keterangan' => 'belum_diverifikasi',
            'catatan' => 'Juara 1, 2, 3 dan Juara Favorit',
        ]);

        DetailKegiatan::create([
            'ajuan_kegiatan_id' => $ajuan2->id,
            'no_urut' => 3,
            'nama_akun' => 'Konsumsi',
            'nama_barang' => 'Konsumsi Panitia & Juri Festival',
            'harga_satuan' => 30000,
            'jumlah' => 50,
            'total' => 1500000,
            'status_keterangan' => 'belum_diverifikasi',
            'catatan' => 'Konsumsi 50 orang',
        ]);

        // 7. Buat Sampel Ajuan 3: Basket (Status: Sudah Disetujui Keseluruhan)
        $ajuan3 = AjuanKegiatan::create([
            'user_id' => $mhsBasket->id,
            'ukm_id' => $ukmBasket->id,
            'nama_kegiatan' => 'Piala Rektor Turnamen Bola Basket 3x3 Antar Fakultas',
            'tanggal_kegiatan' => now()->addDays(30)->toDateString(),
            'dokumen_proposal' => $dummyPdfPath,
            'dokumen_nama_asli' => 'Proposal_Basket_3x3_2026.pdf',
            'total_anggaran' => 4200000,
            'status_pembina' => 'disetujui',
            'catatan_pembina' => 'Jadwal pertandingan sudah sesuai kalender akademik.',
            'verified_pembina_at' => now()->subDays(3),
            'verified_pembina_by' => $pembinaBasket->id,
            'status_kemahasiswaan' => 'disetujui',
            'catatan_kemahasiswaan' => 'Disetujui untuk dilaksanakan dengan mematuhi protokol keamanan kampus.',
            'verified_kemahasiswaan_at' => now()->subDays(2),
            'verified_kemahasiswaan_by' => $kemahasiswaan->id,
            'status_akhir' => 'disetujui',
        ]);

        DetailKegiatan::create([
            'ajuan_kegiatan_id' => $ajuan3->id,
            'no_urut' => 1,
            'nama_akun' => 'Honorarium Wasit',
            'nama_barang' => 'Jasa Wasit Berlisensi Perbasi',
            'harga_satuan' => 400000,
            'jumlah' => 4,
            'total' => 1600000,
            'status_keterangan' => 'disetujui',
            'catatan' => '2 wasit x 2 hari pertandingan',
        ]);

        DetailKegiatan::create([
            'ajuan_kegiatan_id' => $ajuan3->id,
            'no_urut' => 2,
            'nama_akun' => 'Medis & P3K',
            'nama_barang' => 'Obat-obatan, Ice Pack, dan Perban Olahraga',
            'harga_satuan' => 600000,
            'jumlah' => 1,
            'total' => 600000,
            'status_keterangan' => 'disetujui',
            'catatan' => 'Kelengkapan tim medis lapangan',
        ]);

        DetailKegiatan::create([
            'ajuan_kegiatan_id' => $ajuan3->id,
            'no_urut' => 3,
            'nama_akun' => 'Konsumsi',
            'nama_barang' => 'Air Mineral & Snack Panitia/Wasit',
            'harga_satuan' => 2000000,
            'jumlah' => 1,
            'total' => 2000000,
            'status_keterangan' => 'disetujui',
            'catatan' => 'Paket konsumsi 2 hari',
        ]);
    }
}
