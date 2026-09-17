<?php

namespace App\Http\Controllers;

use App\Models\AjuanKegiatan;
use App\Models\DetailKegiatan;
use App\Models\LpjKegiatan;
use App\Models\LpjPeserta;
use App\Models\LpjPrestasi;
use App\Models\Satuan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MahasiswaController extends Controller
{
    /**
     * Tampilkan daftar ajuan kegiatan milik UKM mahasiswa yang sedang login.
     */
    public function index(): View
    {
        $user = Auth::user();
        $ukm = $user->ukm;

        $ajuans = AjuanKegiatan::where('ukm_id', $user->ukm_id)
            ->with(['details', 'pembinaVerifier', 'kemahasiswaanVerifier'])
            ->latest()
            ->paginate(10);

        return view('mahasiswa.index', compact('user', 'ukm', 'ajuans'));
    }

    /**
     * Form pengajuan kegiatan mahasiswa baru.
     */
    public function create(): View
    {
        $user = Auth::user();
        $ukm = $user->ukm;
        $satuans = Satuan::where('is_aktif', true)->orderBy('nama_satuan')->get();

        return view('mahasiswa.create', compact('user', 'ukm', 'satuans'));
    }

    /**
     * Simpan data pengajuan kegiatan beserta detail tabel kebutuhan.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if (! $user->ukm_id) {
            return back()->with('error', 'Akun Anda belum ditautkan ke UKM manapun.');
        }

        $validated = $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'tanggal_kegiatan' => 'required|date',
            'jenis_kegiatan' => 'required|in:Lomba,Pelatihan,Lainnya',
            'dokumen_kegiatan' => 'required|file|mimes:pdf,doc,docx|max:10240',
            'items' => 'required|array|min:1',
            'items.*.nama_akun' => 'required|string|max:255',
            'items.*.nama_barang' => 'required|string|max:255',
            'items.*.harga_satuan' => 'required|numeric|min:0',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.satuan' => 'required|string|max:50',
            'items.*.durasi' => 'required|numeric|min:1',
            'items.*.satuan_durasi' => 'required|string|max:50',
            'items.*.catatan' => 'nullable|string|max:500',
        ], [
            'nama_kegiatan.required' => 'Nama kegiatan wajib diisi.',
            'tanggal_kegiatan.required' => 'Tanggal kegiatan wajib diisi.',
            'jenis_kegiatan.required' => 'Jenis kegiatan wajib dipilih.',
            'jenis_kegiatan.in' => 'Pilihan jenis kegiatan harus salah satu dari: Lomba, Pelatihan, atau Lainnya.',
            'dokumen_kegiatan.required' => 'Dokumen proposal/ToR wajib diunggah.',
            'dokumen_kegiatan.mimes' => 'Format dokumen harus berformat PDF, DOC, atau DOCX.',
            'dokumen_kegiatan.max' => 'Ukuran dokumen maksimal 10MB.',
            'items.required' => 'Tabel detail kebutuhan kegiatan wajib diisi minimal 1 baris.',
            'items.min' => 'Tabel detail kebutuhan kegiatan wajib diisi minimal 1 baris.',
            'items.*.nama_akun.required' => 'Nama akun pada baris detail wajib diisi.',
            'items.*.nama_barang.required' => 'Nama barang pada baris detail wajib diisi.',
            'items.*.harga_satuan.required' => 'Harga satuan wajib diisi.',
            'items.*.jumlah.required' => 'Jumlah barang wajib diisi.',
            'items.*.satuan.required' => 'Satuan barang wajib dipilih.',
            'items.*.durasi.required' => 'Durasi wajib diisi.',
            'items.*.durasi.min' => 'Durasi minimal 1.',
            'items.*.satuan_durasi.required' => 'Satuan durasi wajib dipilih.',
        ]);

        DB::beginTransaction();
        try {
            // Upload Dokumen
            $file = $request->file('dokumen_kegiatan');
            $originalName = $file->getClientOriginalName();
            $path = $file->store('dokumen_proposal', 'public');

            // Hitung total anggaran: Jumlah * Harga Satuan * Durasi
            $totalAnggaran = 0;
            foreach ($validated['items'] as $item) {
                $durasi = isset($item['durasi']) && (float) $item['durasi'] > 0 ? (float) $item['durasi'] : 1;
                $subtotal = (float) $item['harga_satuan'] * (int) $item['jumlah'] * $durasi;
                $totalAnggaran += $subtotal;
            }

            // Simpan Header Ajuan Kegiatan
            $ajuan = AjuanKegiatan::create([
                'user_id' => $user->id,
                'ukm_id' => $user->ukm_id,
                'nama_kegiatan' => $validated['nama_kegiatan'],
                'tanggal_kegiatan' => $validated['tanggal_kegiatan'],
                'jenis_kegiatan' => $validated['jenis_kegiatan'],
                'dokumen_proposal' => $path,
                'dokumen_nama_asli' => $originalName,
                'total_anggaran' => $totalAnggaran,
                'status_pembina' => 'belum_disetujui',
                'status_kemahasiswaan' => 'belum_disetujui',
                'status_akhir' => 'menunggu_pembina',
            ]);

            // Simpan Baris Detail Kebutuhan Kegiatan
            $noUrut = 1;
            foreach ($validated['items'] as $item) {
                $durasi = isset($item['durasi']) && (float) $item['durasi'] > 0 ? (float) $item['durasi'] : 1;
                $subtotal = (float) $item['harga_satuan'] * (int) $item['jumlah'] * $durasi;

                DetailKegiatan::create([
                    'ajuan_kegiatan_id' => $ajuan->id,
                    'no_urut' => $noUrut++,
                    'nama_akun' => trim($item['nama_akun']),
                    'nama_barang' => trim($item['nama_barang']),
                    'harga_satuan' => (float) $item['harga_satuan'],
                    'jumlah' => (int) $item['jumlah'],
                    'satuan' => trim($item['satuan'] ?? 'Unit'),
                    'durasi' => $durasi,
                    'satuan_durasi' => trim($item['satuan_durasi'] ?? 'Hari'),
                    'total' => $subtotal,
                    // Status default non-editable untuk mahasiswa, hanya diedit oleh Kemahasiswaan
                    'status_keterangan' => 'belum_diverifikasi',
                    'catatan' => $item['catatan'] ?? null,
                ]);
            }

            // Catat log riwayat pengajuan awal
            $ajuan->catatRiwayat(
                aksi: 'Pengajuan Usulan Baru',
                statusSesudah: 'menunggu_pembina',
                catatan: 'Usulan kegiatan berhasil diajukan untuk pertama kali.',
                statusSebelumnya: null,
                userId: $user->id,
                role: 'mahasiswa'
            );

            DB::commit();

            return redirect()->route('mahasiswa.show', $ajuan->id)
                ->with('success', 'Pengajuan kegiatan dan detail kebutuhan berhasil disimpan dan dikirim ke Pembina!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan pengajuan: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan detail ajuan kegiatan beserta tabel rincian kebutuhan.
     */
    public function show(int $id): View
    {
        $user = Auth::user();

        $ajuan = AjuanKegiatan::where('id', $id)
            ->where('ukm_id', $user->ukm_id)
            ->with(['ukm', 'user', 'details', 'pembinaVerifier', 'kemahasiswaanVerifier', 'lpj.pesertas', 'lpj.prestasies', 'riwayats.user'])
            ->firstOrFail();

        return view('mahasiswa.show', compact('ajuan', 'user'));
    }

    /**
     * Unduh file dokumen proposal.
     */
    public function downloadDokumen(int $id): StreamedResponse
    {
        $user = Auth::user();
        $ajuan = AjuanKegiatan::where('id', $id)
            ->where('ukm_id', $user->ukm_id)
            ->firstOrFail();

        if (! Storage::disk('public')->exists($ajuan->dokumen_proposal)) {
            abort(404, 'File dokumen proposal tidak ditemukan di server.');
        }

        return Storage::disk('public')->download($ajuan->dokumen_proposal, $ajuan->dokumen_nama_asli);
    }

    /**
     * Form edit untuk memperbaiki usulan kegiatan yang memerlukan revisi.
     */
    public function edit(int $id): View|RedirectResponse
    {
        $user = Auth::user();

        $ajuan = AjuanKegiatan::where('id', $id)
            ->where('ukm_id', $user->ukm_id)
            ->with(['ukm', 'details'])
            ->firstOrFail();

        if (! $ajuan->isBisaDieditMahasiswa()) {
            return redirect()->route('mahasiswa.show', $ajuan->id)
                ->with('error', 'Usulan kegiatan yang telah disetujui tidak dapat diedit kembali.');
        }

        $ukm = $ajuan->ukm;
        $satuans = Satuan::where('is_aktif', true)->orderBy('nama_satuan')->get();

        return view('mahasiswa.edit', compact('ajuan', 'user', 'ukm', 'satuans'));
    }

    /**
     * Simpan perbaikan usulan dan ajukan kembali (resubmit).
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $user = Auth::user();

        $ajuan = AjuanKegiatan::where('id', $id)
            ->where('ukm_id', $user->ukm_id)
            ->with('details')
            ->firstOrFail();

        if (! $ajuan->isBisaDieditMahasiswa()) {
            return redirect()->route('mahasiswa.show', $ajuan->id)
                ->with('error', 'Usulan kegiatan yang telah disetujui tidak dapat diedit kembali.');
        }

        $validated = $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'tanggal_kegiatan' => 'required|date',
            'jenis_kegiatan' => 'required|in:Lomba,Pelatihan,Lainnya',
            'dokumen_kegiatan' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'items' => 'required|array|min:1',
            'items.*.nama_akun' => 'required|string|max:255',
            'items.*.nama_barang' => 'required|string|max:255',
            'items.*.harga_satuan' => 'required|numeric|min:0',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.satuan' => 'required|string|max:50',
            'items.*.durasi' => 'required|numeric|min:1',
            'items.*.satuan_durasi' => 'required|string|max:50',
            'items.*.catatan' => 'nullable|string|max:500',
        ], [
            'nama_kegiatan.required' => 'Nama kegiatan wajib diisi.',
            'tanggal_kegiatan.required' => 'Tanggal kegiatan wajib diisi.',
            'jenis_kegiatan.required' => 'Jenis kegiatan wajib dipilih.',
            'jenis_kegiatan.in' => 'Pilihan jenis kegiatan harus salah satu dari: Lomba, Pelatihan, atau Lainnya.',
            'dokumen_kegiatan.mimes' => 'Format dokumen harus berformat PDF, DOC, atau DOCX.',
            'dokumen_kegiatan.max' => 'Ukuran dokumen maksimal 10MB.',
            'items.required' => 'Tabel detail kebutuhan kegiatan wajib diisi minimal 1 baris.',
            'items.min' => 'Tabel detail kebutuhan kegiatan wajib diisi minimal 1 baris.',
            'items.*.nama_akun.required' => 'Nama akun pada baris detail wajib diisi.',
            'items.*.nama_barang.required' => 'Nama barang pada baris detail wajib diisi.',
            'items.*.harga_satuan.required' => 'Harga satuan wajib diisi.',
            'items.*.jumlah.required' => 'Jumlah barang wajib diisi.',
            'items.*.satuan.required' => 'Satuan barang wajib dipilih.',
            'items.*.durasi.required' => 'Durasi wajib diisi.',
            'items.*.durasi.min' => 'Durasi minimal 1.',
            'items.*.satuan_durasi.required' => 'Satuan durasi wajib dipilih.',
        ]);

        DB::beginTransaction();
        try {
            // Cek jika ada unggahan dokumen revisi baru
            if ($request->hasFile('dokumen_kegiatan')) {
                $file = $request->file('dokumen_kegiatan');
                $originalName = $file->getClientOriginalName();
                $path = $file->store('dokumen_proposal', 'public');

                $ajuan->dokumen_proposal = $path;
                $ajuan->dokumen_nama_asli = $originalName;
            }

            // Hitung ulang total anggaran: Jumlah * Harga Satuan * Durasi
            $totalAnggaran = 0;
            foreach ($validated['items'] as $item) {
                $durasi = isset($item['durasi']) && (float) $item['durasi'] > 0 ? (float) $item['durasi'] : 1;
                $subtotal = (float) $item['harga_satuan'] * (int) $item['jumlah'] * $durasi;
                $totalAnggaran += $subtotal;
            }

            $ajuan->nama_kegiatan = $validated['nama_kegiatan'];
            $ajuan->tanggal_kegiatan = $validated['tanggal_kegiatan'];
            $ajuan->jenis_kegiatan = $validated['jenis_kegiatan'];
            $ajuan->total_anggaran = $totalAnggaran;

            $statusSebelumnya = $ajuan->status_akhir;

            // Transisi status pengajuan kembali (resubmit) atau update sebelum disetujui
            if ($ajuan->isRevisiPembina()) {
                $ajuan->status_pembina = 'belum_disetujui';
                $ajuan->status_akhir = 'menunggu_pembina';
                $ajuan->revisi_count += 1;
                $pesan = 'Revisi usulan kegiatan berhasil disimpan dan diajukan kembali ke Dosen Pembina!';
                $riwayatAksi = 'Pengajuan Revisi Usulan';
                $riwayatCatatan = 'Perbaikan usulan (Revisi ke-' . $ajuan->revisi_count . ') diajukan kembali ke Dosen Pembina.';
            } elseif ($ajuan->isRevisiKemahasiswaan()) {
                // Revisi dari Kemahasiswaan
                $ajuan->status_kemahasiswaan = 'belum_disetujui';
                $ajuan->status_akhir = 'menunggu_kemahasiswaan';
                $ajuan->revisi_count += 1;
                $pesan = 'Revisi usulan kegiatan berhasil disimpan dan diajukan kembali ke Biro Kemahasiswaan!';
                $riwayatAksi = 'Pengajuan Revisi Usulan';
                $riwayatCatatan = 'Perbaikan usulan (Revisi ke-' . $ajuan->revisi_count . ') diajukan kembali ke Biro Kemahasiswaan.';
            } else {
                // Usulan sedang menunggu verifikasi awal (belum disetujui)
                $pesan = 'Perubahan data usulan kegiatan dan rincian RAB berhasil disimpan!';
                $riwayatAksi = 'Pembaruan Usulan Kegiatan';
                $riwayatCatatan = 'Perubahan informasi usulan kegiatan dan/atau rincian item RAB disimpan oleh mahasiswa.';
            }

            $ajuan->save();

            // Catat log riwayat perubahan/revisi usulan
            $ajuan->catatRiwayat(
                aksi: $riwayatAksi,
                statusSesudah: $ajuan->status_akhir,
                catatan: $riwayatCatatan,
                statusSebelumnya: $statusSebelumnya,
                userId: $user->id,
                role: 'mahasiswa'
            );

            // Refresh baris detail kebutuhan kegiatan
            $ajuan->details()->delete();

            $noUrut = 1;
            foreach ($validated['items'] as $item) {
                $durasi = isset($item['durasi']) && (float) $item['durasi'] > 0 ? (float) $item['durasi'] : 1;
                $subtotal = (float) $item['harga_satuan'] * (int) $item['jumlah'] * $durasi;

                DetailKegiatan::create([
                    'ajuan_kegiatan_id' => $ajuan->id,
                    'no_urut' => $noUrut++,
                    'nama_akun' => trim($item['nama_akun']),
                    'nama_barang' => trim($item['nama_barang']),
                    'harga_satuan' => (float) $item['harga_satuan'],
                    'jumlah' => (int) $item['jumlah'],
                    'satuan' => trim($item['satuan'] ?? 'Unit'),
                    'durasi' => $durasi,
                    'satuan_durasi' => trim($item['satuan_durasi'] ?? 'Hari'),
                    'total' => $subtotal,
                    'status_keterangan' => 'belum_diverifikasi',
                    'catatan' => $item['catatan'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()->route('mahasiswa.show', $ajuan->id)
                ->with('success', $pesan);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan revisi: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan formulir pengisian Laporan Pertanggungjawaban (LPJ).
     */
    public function lpjForm(int $id): View|RedirectResponse
    {
        $user = Auth::user();

        $ajuan = AjuanKegiatan::where('id', $id)
            ->where('ukm_id', $user->ukm_id)
            ->with(['ukm', 'details', 'lpj.pesertas', 'lpj.prestasies'])
            ->firstOrFail();

        // Validasi apakah kegiatan berhak mengisi/mengedit LPJ
        if (! $ajuan->isSiapLpj() && ! $ajuan->isRevisiLpj() && ! $ajuan->isSudahDilaksanakan() && ! $ajuan->isMenungguVerifikasiLpj()) {
            return redirect()->route('mahasiswa.show', $ajuan->id)
                ->with('error', 'Kegiatan ini belum memenuhi syarat untuk pengisian LPJ atau tanggal pelaksanaan belum terlewati.');
        }

        $paguDisetujui = (float) ($ajuan->details->where('status_keterangan', 'disetujui')->sum('total') ?: $ajuan->total_anggaran);
        
        $pesertasInitial = old('pesertas');
        if ($pesertasInitial === null) {
            $pesertasInitial = ($ajuan->lpj && $ajuan->lpj->pesertas->count() > 0)
                ? $ajuan->lpj->pesertas->map(function ($p) {
                    return [
                        'nama' => $p->nama,
                        'npm' => $p->npm,
                        'peran' => $p->peran,
                    ];
                })->values()->all()
                : [];
        }

        $prestasiesInitial = old('prestasies');
        if ($prestasiesInitial === null) {
            $prestasiesInitial = ($ajuan->lpj && $ajuan->lpj->prestasies->count() > 0)
                ? $ajuan->lpj->prestasies->map(function ($pr) {
                    return [
                        'kategori_lomba' => $pr->kategori_lomba,
                        'peringkat_juara' => $pr->peringkat_juara,
                        'tingkat' => $pr->tingkat,
                        'nama_peraih' => $pr->nama_peraih,
                    ];
                })->values()->all()
                : [];
        }

        return view('mahasiswa.lpj', compact('ajuan', 'paguDisetujui', 'pesertasInitial', 'prestasiesInitial'));
    }

    /**
     * Simpan atau perbarui Laporan Pertanggungjawaban (LPJ).
     */
    public function storeLpj(Request $request, int $id): RedirectResponse
    {
        $user = Auth::user();

        $ajuan = AjuanKegiatan::where('id', $id)
            ->where('ukm_id', $user->ukm_id)
            ->with(['lpj.prestasies', 'lpj.pesertas'])
            ->firstOrFail();

        $validated = $request->validate([
            'prestasi' => 'nullable|string|max:100',
            'prestasies' => 'nullable|array',
            'prestasies.*.kategori_lomba' => 'nullable|string|max:150',
            'prestasies.*.peringkat_juara' => 'nullable|string|max:100',
            'prestasies.*.tingkat' => 'nullable|string|max:100',
            'prestasies.*.nama_peraih' => 'nullable|string|max:255',
            'jumlah_peserta' => 'nullable|integer|min:0',
            'ringkasan_kegiatan' => 'nullable|string',
            'realisasi_anggaran' => 'nullable|numeric|min:0',
            'pesertas' => 'nullable|array',
            'pesertas.*.nama' => 'nullable|string|max:150',
            'pesertas.*.npm' => 'nullable|string|max:50',
            'pesertas.*.peran' => 'nullable|string|max:50',
            'file_sertifikat' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'foto_kegiatan' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'foto_penyerahan' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'surat_undangan' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
        ]);

        DB::beginTransaction();
        try {
            $lpj = $ajuan->lpj ?: new LpjKegiatan(['ajuan_kegiatan_id' => $ajuan->id]);

            $lpj->jumlah_peserta = isset($validated['jumlah_peserta']) ? (int) $validated['jumlah_peserta'] : 0;
            $lpj->ringkasan_kegiatan = $validated['ringkasan_kegiatan'] ?? null;
            $lpj->realisasi_anggaran = isset($validated['realisasi_anggaran']) ? (float) $validated['realisasi_anggaran'] : 0;

            // Upload 4 Berkas Opsional
            if ($request->hasFile('file_sertifikat')) {
                $file = $request->file('file_sertifikat');
                $lpj->file_sertifikat_nama = $file->getClientOriginalName();
                $lpj->file_sertifikat = $file->store('dokumen_lpj/sertifikat', 'public');
            }

            if ($request->hasFile('foto_kegiatan')) {
                $file = $request->file('foto_kegiatan');
                $lpj->foto_kegiatan_nama = $file->getClientOriginalName();
                $lpj->foto_kegiatan = $file->store('dokumen_lpj/foto_kegiatan', 'public');
            }

            if ($request->hasFile('foto_penyerahan')) {
                $file = $request->file('foto_penyerahan');
                $lpj->foto_penyerahan_nama = $file->getClientOriginalName();
                $lpj->foto_penyerahan = $file->store('dokumen_lpj/foto_penyerahan', 'public');
            }

            if ($request->hasFile('surat_undangan')) {
                $file = $request->file('surat_undangan');
                $lpj->surat_undangan_nama = $file->getClientOriginalName();
                $lpj->surat_undangan = $file->store('dokumen_lpj/surat_undangan', 'public');
            }

            $lpj->submitted_at = now();
            $lpj->save();

            // Simpan / Sinkronisasi Data Multi-Prestasi Lomba
            $lpj->prestasies()->delete();
            $validPrestasies = [];
            if (! empty($validated['prestasies']) && is_array($validated['prestasies'])) {
                foreach ($validated['prestasies'] as $pr) {
                    if (! empty($pr['kategori_lomba']) || ! empty($pr['peringkat_juara'])) {
                        $newPrestasi = LpjPrestasi::create([
                            'lpj_kegiatan_id' => $lpj->id,
                            'kategori_lomba' => trim($pr['kategori_lomba'] ?? 'Umum'),
                            'peringkat_juara' => trim($pr['peringkat_juara'] ?? 'Juara'),
                            'tingkat' => trim($pr['tingkat'] ?? 'Nasional'),
                            'nama_peraih' => trim($pr['nama_peraih'] ?? ''),
                        ]);
                        $validPrestasies[] = $newPrestasi;
                    }
                }
            }

            // Sintesiskan kolom prestasi lama agar tetap terisi ringkasan juara
            if (count($validPrestasies) > 0) {
                if (count($validPrestasies) === 1) {
                    $lpj->prestasi = $validPrestasies[0]->peringkat_juara . ' - ' . $validPrestasies[0]->kategori_lomba;
                } else {
                    $lpj->prestasi = count($validPrestasies) . ' Juara (' . collect($validPrestasies)->map(function ($p) {
                        return $p->peringkat_juara;
                    })->take(2)->implode(', ') . (count($validPrestasies) > 2 ? ', dll' : '') . ')';
                }
            } elseif (! empty($validated['prestasi'])) {
                $lpj->prestasi = $validated['prestasi'];
            } else {
                $lpj->prestasi = 'Kegiatan Non-Lomba (Terlaksana Sukses)';
            }
            $lpj->save();

            // Simpan / Sinkronisasi Data Peserta SIMKATMAWA
            $lpj->pesertas()->delete();
            if (! empty($validated['pesertas']) && is_array($validated['pesertas'])) {
                foreach ($validated['pesertas'] as $p) {
                    if (! empty($p['nama']) || ! empty($p['npm'])) {
                        LpjPeserta::create([
                            'lpj_kegiatan_id' => $lpj->id,
                            'nama' => trim($p['nama'] ?? '-'),
                            'npm' => trim($p['npm'] ?? '-'),
                            'peran' => trim($p['peran'] ?? 'Peserta'),
                        ]);
                    }
                }
            }

            $statusSebelumnya = $ajuan->status_akhir;
            $isLpjPerbaikan = ($statusSebelumnya === 'revisi_lpj');

            // Update status akhir ajuan menjadi menunggu verifikasi LPJ
            $ajuan->status_akhir = 'menunggu_verifikasi_lpj';
            $ajuan->save();

            // Catat log riwayat penyerahan / perbaikan LPJ
            $ajuan->catatRiwayat(
                aksi: $isLpjPerbaikan ? 'Perbaikan Laporan LPJ' : 'Penyerahan Laporan LPJ',
                statusSesudah: 'menunggu_verifikasi_lpj',
                catatan: $isLpjPerbaikan
                    ? 'Laporan LPJ yang telah diperbaiki diserahkan kembali untuk verifikasi.'
                    : 'Laporan Pertanggungjawaban (LPJ) kegiatan dan data peserta SIMKATMAWA diserahkan.',
                statusSebelumnya: $statusSebelumnya,
                userId: $user->id,
                role: 'mahasiswa'
            );

            DB::commit();

            return redirect()->route('mahasiswa.show', $ajuan->id)
                ->with('success', 'Laporan Pertanggungjawaban (LPJ) berhasil disimpan dan dikirim ke Kemahasiswaan untuk verifikasi!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan LPJ: ' . $e->getMessage());
        }
    }

    /**
     * Unduh berkas dokumen LPJ.
     */
    public function downloadLpjFile(int $id, string $type): StreamedResponse
    {
        $user = Auth::user();

        $ajuan = AjuanKegiatan::where('id', $id)
            ->where('ukm_id', $user->ukm_id)
            ->with('lpj')
            ->firstOrFail();

        $lpj = $ajuan->lpj;
        if (! $lpj) {
            abort(404, 'Data LPJ belum tersedia.');
        }

        $fieldMap = [
            'sertifikat' => ['path' => $lpj->file_sertifikat, 'name' => $lpj->file_sertifikat_nama],
            'foto_kegiatan' => ['path' => $lpj->foto_kegiatan, 'name' => $lpj->foto_kegiatan_nama],
            'foto_penyerahan' => ['path' => $lpj->foto_penyerahan, 'name' => $lpj->foto_penyerahan_nama],
            'surat_undangan' => ['path' => $lpj->surat_undangan, 'name' => $lpj->surat_undangan_nama],
        ];

        if (! isset($fieldMap[$type]) || ! $fieldMap[$type]['path'] || ! Storage::disk('public')->exists($fieldMap[$type]['path'])) {
            abort(404, 'Berkas LPJ tidak ditemukan di server.');
        }

        return Storage::disk('public')->download($fieldMap[$type]['path'], $fieldMap[$type]['name'] ?? 'dokumen_lpj');
    }
}
