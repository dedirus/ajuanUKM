<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Lembar Pengesahan - {{ $ajuan->nama_kegiatan }}</title>
    <style>
        @page {
            margin: 15mm 15mm 15mm 15mm;
            size: a4 portrait;
        }

        body {
            font-family: "DejaVu Sans", "Helvetica", Arial, sans-serif;
            font-size: 9.5pt;
            line-height: 1.35;
            color: #1a202c;
            margin: 0;
            padding: 0;
        }

        /* Kop Surat */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0px;
        }

        .kop-table td {
            vertical-align: middle;
            padding: 0;
        }

        .kop-logo-box {
            width: 65px;
            text-align: center;
        }

        .kop-logo-circle {
            width: 55px;
            height: 55px;
            background-color: #1e3a8a;
            color: #ffffff;
            border-radius: 50%;
            font-size: 20pt;
            font-weight: bold;
            line-height: 55px;
            text-align: center;
            margin: 0 auto;
        }

        .kop-text {
            text-align: center;
            padding-left: 10px;
        }

        .kop-instansi {
            font-size: 10.5pt;
            font-weight: bold;
            letter-spacing: 0.5px;
            color: #334155;
            text-transform: uppercase;
            margin: 0;
        }

        .kop-universitas {
            font-size: 13.5pt;
            font-weight: bold;
            letter-spacing: 0.8px;
            color: #0f172a;
            text-transform: uppercase;
            margin: 2px 0;
        }

        .kop-unit {
            font-size: 10.5pt;
            font-weight: bold;
            color: #1e40af;
            margin: 0;
            text-transform: uppercase;
        }

        .kop-alamat {
            font-size: 7.5pt;
            color: #64748b;
            margin-top: 3px;
        }

        .kop-line-thick {
            border: none;
            border-top: 2.5px solid #0f172a;
            margin-top: 8px;
            margin-bottom: 2px;
        }

        .kop-line-thin {
            border: none;
            border-top: 0.8px solid #0f172a;
            margin-top: 0;
            margin-bottom: 12px;
        }

        /* Header Dokumen */
        .doc-header {
            text-align: center;
            margin-bottom: 12px;
        }

        .doc-title {
            font-size: 11.5pt;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
            color: #0f172a;
        }

        .doc-number {
            font-size: 9pt;
            font-weight: bold;
            color: #475569;
            margin-top: 3px;
        }

        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 3px 12px;
            font-size: 8pt;
            font-weight: bold;
            border-radius: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 4px;
        }

        .status-disetujui {
            background-color: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
        }

        .status-revisi {
            background-color: #ffedd5;
            color: #9a3412;
            border: 1px solid #fdba74;
        }

        .status-ditolak {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .status-proses {
            background-color: #e0f2fe;
            color: #0369a1;
            border: 1px solid #7dd3fc;
        }

        /* Section Styling */
        .section-title {
            font-size: 9pt;
            font-weight: bold;
            color: #1e3a8a;
            text-transform: uppercase;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 2px;
            margin-top: 10px;
            margin-bottom: 6px;
        }

        /* Tabel Identitas */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }

        .info-table td {
            padding: 2.5px 4px;
            vertical-align: top;
            font-size: 8.5pt;
        }

        .info-label {
            width: 25%;
            color: #475569;
            font-weight: bold;
        }

        .info-separator {
            width: 2%;
            text-align: center;
        }

        .info-value {
            width: 73%;
            color: #0f172a;
        }

        /* Tabel RAB */
        .rab-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
            margin-bottom: 8px;
        }

        .rab-table th, .rab-table td {
            border: 0.8px solid #cbd5e1;
            padding: 4px 6px;
            font-size: 8pt;
        }

        .rab-table th {
            background-color: #f1f5f9;
            color: #1e293b;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .rab-table td.center {
            text-align: center;
        }

        .rab-table td.right {
            text-align: right;
        }

        .rab-total-row {
            background-color: #f8fafc;
            font-weight: bold;
        }

        .badge-item-ok {
            color: #166534;
            font-weight: bold;
        }

        .badge-item-no {
            color: #991b1b;
            font-weight: bold;
        }

        .badge-item-pending {
            color: #64748b;
            font-style: italic;
        }

        /* Box Catatan */
        .notes-box {
            background-color: #f8fafc;
            border: 0.8px solid #e2e8f0;
            border-radius: 4px;
            padding: 6px 8px;
            font-size: 8pt;
            margin-bottom: 10px;
        }

        .notes-title {
            font-weight: bold;
            color: #334155;
            margin-bottom: 2px;
        }

        /* Blok Tanda Tangan */
        .sign-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 14px;
            page-break-inside: avoid;
        }

        .sign-table td {
            width: 50%;
            vertical-align: top;
            padding: 0 10px;
            font-size: 8.5pt;
        }

        .sign-role {
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 2px;
        }

        .sign-unit {
            color: #475569;
            font-size: 8pt;
            margin-bottom: 6px;
        }

        .stamp-box {
            border: 1px dashed #0284c7;
            background-color: #f0f9ff;
            border-radius: 6px;
            padding: 6px;
            margin: 6px 0;
            text-align: center;
            color: #0369a1;
        }

        .stamp-title {
            font-weight: bold;
            font-size: 7pt;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .stamp-date {
            font-size: 7.5pt;
            color: #0c4a6e;
            margin-top: 2px;
        }

        .stamp-code {
            font-family: monospace;
            font-size: 6.5pt;
            color: #64748b;
            margin-top: 2px;
        }

        .sign-name {
            font-weight: bold;
            text-decoration: underline;
            margin-top: 4px;
            color: #0f172a;
        }

        .sign-nip {
            font-size: 7.5pt;
            color: #64748b;
        }

        /* Footer */
        .footer-note {
            margin-top: 16px;
            border-top: 0.5px solid #e2e8f0;
            padding-top: 5px;
            font-size: 6.8pt;
            color: #94a3b8;
            text-align: center;
            line-height: 1.25;
        }
    </style>
</head>
<body>

    <!-- KOP SURAT RESMI -->
    <table class="kop-table">
        <tr>
            <td class="kop-logo-box">
                <div class="kop-logo-circle">U</div>
            </td>
            <td class="kop-text">
                <div class="kop-instansi">Kementerian Pendidikan, Kebudayaan, Riset, dan Teknologi</div>
                <div class="kop-universitas">Universitas ServBay Indonesia</div>
                <div class="kop-unit">Biro Kemahasiswaan dan Pengembangan Karir Alumni</div>
                <div class="kop-alamat">
                    Gedung Pelayanan Mahasiswa Lt. 2 | Jalan Kampus Terpadu No. 1, Surabaya 60115<br>
                    Telepon: (031) 5928812 | Laman: https://univ.ac.id | Surel: kemahasiswaan@univ.ac.id
                </div>
            </td>
        </tr>
    </table>

    <hr class="kop-line-thick">
    <hr class="kop-line-thin">

    <!-- JUDUL DOKUMEN & NOMOR -->
    <div class="doc-header">
        <div class="doc-title">Lembar Pengesahan & Disposisi Kegiatan Mahasiswa</div>
        <div class="doc-number">Nomor Registrasi: {{ $nomorSurat }}</div>

        <div>
            @if($ajuan->status_akhir === 'disetujui')
                <span class="status-badge status-disetujui">✓ Telah Disetujui & Disahkan</span>
            @elseif(in_array($ajuan->status_akhir, ['revisi_pembina', 'revisi_kemahasiswaan']))
                <span class="status-badge status-revisi">⚠ Memerlukan Perbaikan / Revisi</span>
            @elseif($ajuan->status_akhir === 'ditolak')
                <span class="status-badge status-ditolak">✕ Usulan Tidak Disetujui</span>
            @else
                <span class="status-badge status-proses">⏳ Proses Verifikasi Bertingkat</span>
            @endif
        </div>
    </div>

    <!-- 1. IDENTITAS USULAN KEGIATAN -->
    <div class="section-title">I. Identitas Usulan Kegiatan</div>
    <table class="info-table">
        <tr>
            <td class="info-label">Nama UKM / Pengusul</td>
            <td class="info-separator">:</td>
            <td class="info-value"><strong>{{ $ajuan->ukm->nama_ukm ?? 'UKM' }}</strong> (Kode: {{ $ajuan->ukm->kode ?? '-' }})</td>
        </tr>
        <tr>
            <td class="info-label">Judul / Nama Kegiatan</td>
            <td class="info-separator">:</td>
            <td class="info-value"><strong>{{ $ajuan->nama_kegiatan }}</strong></td>
        </tr>
        <tr>
            <td class="info-label">Jenis Kegiatan</td>
            <td class="info-separator">:</td>
            <td class="info-value"><strong>{{ $ajuan->jenis_kegiatan ?? 'Lainnya' }}</strong></td>
        </tr>
        <tr>
            <td class="info-label">Tanggal Pelaksanaan</td>
            <td class="info-separator">:</td>
            <td class="info-value">
                {{ \Carbon\Carbon::parse($ajuan->tanggal_kegiatan)->translatedFormat('l, d F Y') }}
            </td>
        </tr>
        <tr>
            <td class="info-label">Ketua / Penanggung Jawab</td>
            <td class="info-separator">:</td>
            <td class="info-value">
                {{ $ajuan->user->name }} &nbsp;|&nbsp; NIM: {{ $ajuan->user->nim_nip ?? '-' }} &nbsp;|&nbsp; Telp: {{ $ajuan->user->no_hp ?? '-' }}
            </td>
        </tr>
        <tr>
            <td class="info-label">Dokumen Proposal Terlampir</td>
            <td class="info-separator">:</td>
            <td class="info-value">{{ $ajuan->dokumen_nama_asli ?? 'Proposal.pdf' }}</td>
        </tr>
        <tr>
            <td class="info-label">Tanggal Pengajuan</td>
            <td class="info-separator">:</td>
            <td class="info-value">
                {{ $ajuan->created_at ? $ajuan->created_at->translatedFormat('d F Y, H:i') : '-' }} WIB
                @if($ajuan->revisi_count > 0)
                    <span style="color: #ea580c; font-weight: bold;">(Telah direvisi {{ $ajuan->revisi_count }}x)</span>
                @endif
            </td>
        </tr>
    </table>

    <!-- 2. RINCIAN ANGGARAN BIAYA (RAB) -->
    <div class="section-title">II. Rincian Anggaran Biaya (RAB) Terverifikasi</div>
    <table class="rab-table">
        <thead>
            <tr>
                <th style="width: 3%;">No</th>
                <th style="width: 15%;">Akun Kebutuhan</th>
                <th style="width: 22%;">Uraian Barang / Jasa</th>
                <th style="width: 13%;">Harga Satuan</th>
                <th style="width: 5%;">Jml</th>
                <th style="width: 7%;">Satuan</th>
                <th style="width: 5%;">Durasi</th>
                <th style="width: 7%;">Sat. Durasi</th>
                <th style="width: 13%;">Total Biaya</th>
                <th style="width: 10%;">Verifikasi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ajuan->details as $index => $item)
                <tr>
                    <td class="center">{{ $item->no_urut ?? ($index + 1) }}</td>
                    <td>{{ $item->nama_akun }}</td>
                    <td>
                        {{ $item->nama_barang }}
                        @if($item->catatan)
                            <div style="font-size: 7pt; color: #64748b; font-style: italic;">Catatan: {{ $item->catatan }}</div>
                        @endif
                    </td>
                    <td class="right">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                    <td class="center">{{ $item->jumlah }}</td>
                    <td class="center">{{ $item->satuan ?? '-' }}</td>
                    <td class="center">{{ (float)$item->durasi == (int)$item->durasi ? (int)$item->durasi : $item->durasi }}</td>
                    <td class="center">{{ $item->satuan_durasi ?? 'Hari' }}</td>
                    <td class="right">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                    <td class="center">
                        @if($item->status_keterangan === 'disetujui')
                            <span class="badge-item-ok">✓ Disetujui</span>
                        @elseif($item->status_keterangan === 'tidak_disetujui')
                            <span class="badge-item-no">✕ Ditolak</span>
                        @else
                            <span class="badge-item-pending">Pending</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="center" style="font-style: italic; color: #94a3b8;">Tidak ada rincian kebutuhan kegiatan.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="rab-total-row">
                <td colspan="8" style="text-align: right; padding-right: 8px;">Total Usulan Anggaran:</td>
                <td class="right">Rp {{ number_format($totalDiajukan, 0, ',', '.') }}</td>
                <td class="center">-</td>
            </tr>
            @if($ajuan->status_akhir === 'disetujui')
                <tr class="rab-total-row" style="background-color: #ecfdf5; color: #065f46;">
                    <td colspan="8" style="text-align: right; padding-right: 8px;"><strong>Total Anggaran Disetujui Kemahasiswaan:</strong></td>
                    <td class="right" style="color: #047857;"><strong>Rp {{ number_format($totalDisetujui, 0, ',', '.') }}</strong></td>
                    <td class="center" style="color: #047857;"><strong>FINAL</strong></td>
                </tr>
            @endif
        </tfoot>
    </table>

    <!-- 3. CATATAN VERIFIKASI (JIKA ADA) -->
    @if($ajuan->catatan_pembina || $ajuan->catatan_kemahasiswaan)
        <div class="notes-box">
            <div class="notes-title">Catatan & Rekomendasi Verifikator:</div>
            @if($ajuan->catatan_pembina)
                <div><strong>Pembina UKM:</strong> "{{ $ajuan->catatan_pembina }}"</div>
            @endif
            @if($ajuan->catatan_kemahasiswaan)
                <div style="margin-top: 2px;"><strong>Kemahasiswaan:</strong> "{{ $ajuan->catatan_kemahasiswaan }}"</div>
            @endif
        </div>
    @endif

    <!-- 4. LEMBAR PENGESAHAN DUA PIHAK BERDAMPINGAN -->
    <div class="section-title">III. Lembar Pengesahan & Validasi Digital</div>
    <table class="sign-table">
        <tr>
            <!-- Kolom 1: Dosen Pembina UKM -->
            <td>
                <div class="sign-role">Mengetahui & Menyetujui,</div>
                <div class="sign-unit">Dosen Pembina {{ $ajuan->ukm->nama_ukm ?? 'UKM' }}</div>

                <div class="stamp-box">
                    <div class="stamp-title">Telah Diverifikasi Digital</div>
                    @if($ajuan->verified_pembina_at)
                        <div class="stamp-date">
                            Tanggal: {{ $ajuan->verified_pembina_at->translatedFormat('d F Y, H:i') }} WIB
                        </div>
                        <div class="stamp-code">Token: VERIF-PEMB-{{ strtoupper(substr(md5($ajuan->id . $ajuan->verified_pembina_at), 0, 10)) }}</div>
                    @else
                        <div class="stamp-date" style="color: #94a3b8; font-style: italic;">(Menunggu Verifikasi)</div>
                    @endif
                </div>

                <div class="sign-name">
                    {{ $ajuan->pembinaVerifier->name ?? $ajuan->ukm->pembina_nama ?? 'Dosen Pembina UKM' }}
                </div>
                <div class="sign-nip">
                    NIP / NIDN: {{ $ajuan->pembinaVerifier->nim_nip ?? '198503152010121002' }}
                </div>
            </td>

            <!-- Kolom 2: Bagian Kemahasiswaan -->
            <td>
                <div class="sign-role">Mengesahkan & Menyetujui,</div>
                <div class="sign-unit">Kepala Bagian Biro Kemahasiswaan & Alumni</div>

                <div class="stamp-box">
                    <div class="stamp-title">Telah Diverifikasi & Disahkan</div>
                    @if($ajuan->verified_kemahasiswaan_at)
                        <div class="stamp-date">
                            Tanggal: {{ $ajuan->verified_kemahasiswaan_at->translatedFormat('d F Y, H:i') }} WIB
                        </div>
                        <div class="stamp-code">Token: VERIF-KMHS-{{ strtoupper(substr(md5($ajuan->id . $ajuan->verified_kemahasiswaan_at), 0, 10)) }}</div>
                    @else
                        <div class="stamp-date" style="color: #94a3b8; font-style: italic;">(Menunggu Verifikasi Akhir)</div>
                    @endif
                </div>

                <div class="sign-name">
                    {{ $ajuan->kemahasiswaanVerifier->name ?? 'Dr. H. Kemahasiswaan, M.Pd.' }}
                </div>
                <div class="sign-nip">
                    NIP: {{ $ajuan->kemahasiswaanVerifier->nim_nip ?? '197908202005011003' }}
                </div>
            </td>
        </tr>
    </table>

    <!-- FOOTER RESMI KEABSAHAN SISTEM -->
    <div class="footer-note">
        Dokumen ini diterbitkan secara otomatis dan sah oleh <strong>Sistem Informasi Pengajuan Kegiatan Mahasiswa (AjuanUKM)</strong>.<br>
        Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i:s') }} WIB &nbsp;|&nbsp;
        Kode Dokumen: {{ $nomorSurat }} &nbsp;|&nbsp;
        Arsip Digital Biro Kemahasiswaan & Alumni Universitas ServBay Indonesia
    </div>

</body>
</html>
