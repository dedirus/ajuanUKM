<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaporanRealisasiUkmExport implements WithMultipleSheets
{
    protected $ukmStats;
    protected $allAjuans;
    protected $summary;

    public function __construct($ukmStats, $allAjuans, $summary)
    {
        $this->ukmStats = $ukmStats;
        $this->allAjuans = $allAjuans;
        $this->summary = $summary;
    }

    public function sheets(): array
    {
        return [
            new RekapitulasiSheet($this->ukmStats, $this->summary),
            new RincianKegiatanSheet($this->allAjuans),
        ];
    }
}

class RekapitulasiSheet implements FromArray, WithHeadings, WithTitle, ShouldAutoSize, WithStyles
{
    protected $ukmStats;
    protected $summary;

    public function __construct($ukmStats, $summary)
    {
        $this->ukmStats = $ukmStats;
        $this->summary = $summary;
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode UKM',
            'Nama Organisasi / UKM',
            'Kategori',
            'Dosen Pembina',
            'Total Usulan',
            'Disetujui (Terealisasi)',
            'Dalam Proses',
            'Ditolak',
            '% Realisasi',
            'Total Anggaran Diajukan (Rp)',
            'Total Realisasi Disetujui (Rp)',
        ];
    }

    public function array(): array
    {
        $rows = [];
        $sumUsulan = 0;
        $sumDisetujui = 0;
        $sumProses = 0;
        $sumDitolak = 0;
        $sumDiajukan = 0;
        $sumDisetujuiRp = 0;

        foreach ($this->ukmStats as $index => $row) {
            $sumUsulan += $row['total_usulan'];
            $sumDisetujui += $row['disetujui_count'];
            $sumProses += $row['proses_count'];
            $sumDitolak += $row['ditolak_count'];
            $sumDiajukan += $row['total_anggaran_diajukan'];
            $sumDisetujuiRp += $row['total_anggaran_disetujui'];

            $rows[] = [
                $index + 1,
                $row['kode'],
                $row['nama_ukm'],
                $row['kategori'],
                $row['pembina_nama'],
                $row['total_usulan'],
                $row['disetujui_count'],
                $row['proses_count'],
                $row['ditolak_count'],
                $row['persentase_realisasi'] . '%',
                $row['total_anggaran_diajukan'],
                $row['total_anggaran_disetujui'],
            ];
        }

        // Summary Total Row
        $persenTotal = $sumUsulan > 0 ? round(($sumDisetujui / $sumUsulan) * 100, 1) : 0;
        $rows[] = [
            'TOTAL',
            '',
            'TOTAL KESELURUHAN UNIVERSITAS',
            '',
            '',
            $sumUsulan,
            $sumDisetujui,
            $sumProses,
            $sumDitolak,
            $persenTotal . '%',
            $sumDiajukan,
            $sumDisetujuiRp,
        ];

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:L1')->getFont()->setBold(true);
        $sheet->getStyle('A1:L1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF1E3A8A');
        $sheet->getStyle('A1:L1')->getFont()->getColor()->setARGB('FFFFFFFF');

        $lastRow = count($this->ukmStats) + 2;
        $sheet->getStyle("A{$lastRow}:L{$lastRow}")->getFont()->setBold(true);
        $sheet->getStyle("A{$lastRow}:L{$lastRow}")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFF1F5F9');

        return [];
    }

    public function title(): string
    {
        return 'Rekapitulasi per UKM';
    }
}

class RincianKegiatanSheet implements FromArray, WithHeadings, WithTitle, ShouldAutoSize, WithStyles
{
    protected $allAjuans;

    public function __construct($allAjuans)
    {
        $this->allAjuans = $allAjuans;
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode UKM',
            'Nama Organisasi / UKM',
            'Nama Usulan Kegiatan',
            'Tanggal Pelaksanaan',
            'Mahasiswa Pengusul',
            'Status Pembina',
            'Status Kemahasiswaan',
            'Status Akhir',
            'Revisi Ke',
            'Total Anggaran (Rp)',
        ];
    }

    public function array(): array
    {
        $rows = [];
        $totalRp = 0;

        foreach ($this->allAjuans as $index => $item) {
            $totalRp += (float) $item->total_anggaran;

            $statusPembina = match($item->status_pembina) {
                'disetujui' => 'Disetujui',
                'revisi' => 'Minta Revisi',
                'ditolak' => 'Ditolak',
                default => 'Belum Disetujui',
            };

            $statusKemahasiswaan = match($item->status_kemahasiswaan) {
                'disetujui' => 'Disetujui',
                'revisi' => 'Minta Revisi',
                'ditolak' => 'Ditolak',
                default => 'Belum Disetujui',
            };

            $statusAkhir = match($item->status_akhir) {
                'disetujui' => 'Disetujui',
                'ditolak' => 'Ditolak',
                'revisi_pembina' => 'Revisi Pembina',
                'revisi_kemahasiswaan' => 'Revisi Kemahasiswaan',
                'menunggu_kemahasiswaan' => 'Menunggu Kemahasiswaan',
                default => 'Menunggu Pembina',
            };

            $rows[] = [
                $index + 1,
                $item->ukm->kode ?? '-',
                $item->ukm->nama_ukm ?? '-',
                $item->nama_kegiatan,
                $item->tanggal_kegiatan ? \Carbon\Carbon::parse($item->tanggal_kegiatan)->format('d/m/Y') : '-',
                ($item->user->name ?? '-') . ' (' . ($item->user->nim_nip ?? '-') . ')',
                $statusPembina,
                $statusKemahasiswaan,
                $statusAkhir,
                $item->revisi_count . 'x',
                (float) $item->total_anggaran,
            ];
        }

        $rows[] = [
            'TOTAL',
            '',
            '',
            'TOTAL KESELURUHAN ANGGARAN USULAN',
            '',
            '',
            '',
            '',
            '',
            '',
            $totalRp,
        ];

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:K1')->getFont()->setBold(true);
        $sheet->getStyle('A1:K1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF334155');
        $sheet->getStyle('A1:K1')->getFont()->getColor()->setARGB('FFFFFFFF');

        $lastRow = count($this->allAjuans) + 2;
        $sheet->getStyle("A{$lastRow}:K{$lastRow}")->getFont()->setBold(true);
        $sheet->getStyle("A{$lastRow}:K{$lastRow}")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFF1F5F9');

        return [];
    }

    public function title(): string
    {
        return 'Rincian Seluruh Usulan';
    }
}
