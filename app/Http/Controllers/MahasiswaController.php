<?php

namespace App\Http\Controllers;

use App\Models\AjuanKegiatan;
use App\Models\DetailKegiatan;
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

        return view('mahasiswa.create', compact('user', 'ukm'));
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
            'dokumen_kegiatan' => 'required|file|mimes:pdf,doc,docx|max:10240',
            'items' => 'required|array|min:1',
            'items.*.nama_akun' => 'required|string|max:255',
            'items.*.nama_barang' => 'required|string|max:255',
            'items.*.harga_satuan' => 'required|numeric|min:0',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.catatan' => 'nullable|string|max:500',
        ], [
            'nama_kegiatan.required' => 'Nama kegiatan wajib diisi.',
            'tanggal_kegiatan.required' => 'Tanggal kegiatan wajib diisi.',
            'dokumen_kegiatan.required' => 'Dokumen proposal/ToR wajib diunggah.',
            'dokumen_kegiatan.mimes' => 'Format dokumen harus berformat PDF, DOC, atau DOCX.',
            'dokumen_kegiatan.max' => 'Ukuran dokumen maksimal 10MB.',
            'items.required' => 'Tabel detail kebutuhan kegiatan wajib diisi minimal 1 baris.',
            'items.min' => 'Tabel detail kebutuhan kegiatan wajib diisi minimal 1 baris.',
            'items.*.nama_akun.required' => 'Nama akun pada baris detail wajib diisi.',
            'items.*.nama_barang.required' => 'Nama barang pada baris detail wajib diisi.',
            'items.*.harga_satuan.required' => 'Harga satuan wajib diisi.',
            'items.*.jumlah.required' => 'Jumlah barang wajib diisi.',
        ]);

        DB::beginTransaction();
        try {
            // Upload Dokumen
            $file = $request->file('dokumen_kegiatan');
            $originalName = $file->getClientOriginalName();
            $path = $file->store('dokumen_proposal', 'public');

            // Hitung total anggaran
            $totalAnggaran = 0;
            foreach ($validated['items'] as $item) {
                $subtotal = (float) $item['harga_satuan'] * (int) $item['jumlah'];
                $totalAnggaran += $subtotal;
            }

            // Simpan Header Ajuan Kegiatan
            $ajuan = AjuanKegiatan::create([
                'user_id' => $user->id,
                'ukm_id' => $user->ukm_id,
                'nama_kegiatan' => $validated['nama_kegiatan'],
                'tanggal_kegiatan' => $validated['tanggal_kegiatan'],
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
                $subtotal = (float) $item['harga_satuan'] * (int) $item['jumlah'];

                DetailKegiatan::create([
                    'ajuan_kegiatan_id' => $ajuan->id,
                    'no_urut' => $noUrut++,
                    'nama_akun' => trim($item['nama_akun']),
                    'nama_barang' => trim($item['nama_barang']),
                    'harga_satuan' => (float) $item['harga_satuan'],
                    'jumlah' => (int) $item['jumlah'],
                    'total' => $subtotal,
                    // Status default non-editable untuk mahasiswa, hanya diedit oleh Kemahasiswaan
                    'status_keterangan' => 'belum_diverifikasi',
                    'catatan' => $item['catatan'] ?? null,
                ]);
            }

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
            ->with(['ukm', 'user', 'details', 'pembinaVerifier', 'kemahasiswaanVerifier'])
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

        if (! $ajuan->isPerluRevisi()) {
            return redirect()->route('mahasiswa.show', $ajuan->id)
                ->with('error', 'Usulan kegiatan ini tidak dalam status revisi.');
        }

        $ukm = $ajuan->ukm;

        return view('mahasiswa.edit', compact('ajuan', 'user', 'ukm'));
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

        if (! $ajuan->isPerluRevisi()) {
            return redirect()->route('mahasiswa.show', $ajuan->id)
                ->with('error', 'Usulan kegiatan ini tidak dalam status revisi.');
        }

        $validated = $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'tanggal_kegiatan' => 'required|date',
            'dokumen_kegiatan' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'items' => 'required|array|min:1',
            'items.*.nama_akun' => 'required|string|max:255',
            'items.*.nama_barang' => 'required|string|max:255',
            'items.*.harga_satuan' => 'required|numeric|min:0',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.catatan' => 'nullable|string|max:500',
        ], [
            'nama_kegiatan.required' => 'Nama kegiatan wajib diisi.',
            'tanggal_kegiatan.required' => 'Tanggal kegiatan wajib diisi.',
            'dokumen_kegiatan.mimes' => 'Format dokumen harus berformat PDF, DOC, atau DOCX.',
            'dokumen_kegiatan.max' => 'Ukuran dokumen maksimal 10MB.',
            'items.required' => 'Tabel detail kebutuhan kegiatan wajib diisi minimal 1 baris.',
            'items.min' => 'Tabel detail kebutuhan kegiatan wajib diisi minimal 1 baris.',
            'items.*.nama_akun.required' => 'Nama akun pada baris detail wajib diisi.',
            'items.*.nama_barang.required' => 'Nama barang pada baris detail wajib diisi.',
            'items.*.harga_satuan.required' => 'Harga satuan wajib diisi.',
            'items.*.jumlah.required' => 'Jumlah barang wajib diisi.',
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

            // Hitung ulang total anggaran
            $totalAnggaran = 0;
            foreach ($validated['items'] as $item) {
                $subtotal = (float) $item['harga_satuan'] * (int) $item['jumlah'];
                $totalAnggaran += $subtotal;
            }

            $ajuan->nama_kegiatan = $validated['nama_kegiatan'];
            $ajuan->tanggal_kegiatan = $validated['tanggal_kegiatan'];
            $ajuan->total_anggaran = $totalAnggaran;
            $ajuan->revisi_count += 1;

            // Transisi status pengajuan kembali (resubmit)
            if ($ajuan->isRevisiPembina()) {
                $ajuan->status_pembina = 'belum_disetujui';
                $ajuan->status_akhir = 'menunggu_pembina';
                $pesanTarget = 'Dosen Pembina';
            } else {
                // Revisi dari Kemahasiswaan
                $ajuan->status_kemahasiswaan = 'belum_disetujui';
                $ajuan->status_akhir = 'menunggu_kemahasiswaan';
                $pesanTarget = 'Biro Kemahasiswaan';
            }

            $ajuan->save();

            // Refresh baris detail kebutuhan kegiatan
            $ajuan->details()->delete();

            $noUrut = 1;
            foreach ($validated['items'] as $item) {
                $subtotal = (float) $item['harga_satuan'] * (int) $item['jumlah'];

                DetailKegiatan::create([
                    'ajuan_kegiatan_id' => $ajuan->id,
                    'no_urut' => $noUrut++,
                    'nama_akun' => trim($item['nama_akun']),
                    'nama_barang' => trim($item['nama_barang']),
                    'harga_satuan' => (float) $item['harga_satuan'],
                    'jumlah' => (int) $item['jumlah'],
                    'total' => $subtotal,
                    'status_keterangan' => 'belum_diverifikasi',
                    'catatan' => $item['catatan'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()->route('mahasiswa.show', $ajuan->id)
                ->with('success', "Revisi usulan kegiatan berhasil disimpan dan diajukan kembali ke {$pesanTarget}!");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan revisi: ' . $e->getMessage());
        }
    }
}
