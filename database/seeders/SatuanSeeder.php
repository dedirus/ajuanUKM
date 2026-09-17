<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SatuanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $satuans = [
            'Buah',
            'Dus',
            'Bulan',
            'Hari',
            'Jam',
            'Kali',
            'Sesi',
            'Org',
            'Unit',
            'Paket',
            'Keg',
        ];

        foreach ($satuans as $satuan) {
            \App\Models\Satuan::firstOrCreate(
                ['nama_satuan' => $satuan],
                ['is_aktif' => true, 'keterangan' => 'Satuan bawaan sistem']
            );
        }
    }
}
