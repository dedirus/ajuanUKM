<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMasterUkmRequest;
use App\Http\Requests\UpdateMasterUkmRequest;
use App\Models\AjuanKegiatan;
use App\Models\Ukm;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MasterUkmController extends Controller
{
    /**
     * Tampilkan daftar master UKM dengan filter dan ringkasan metrik.
     */
    public function index(Request $request): View
    {
        $query = Ukm::withCount(['users', 'ajuanKegiatans']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_ukm', 'like', "%{$search}%")
                  ->orWhere('kode', 'like', "%{$search}%")
                  ->orWhere('pembina_nama', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        $ukms = $query->orderBy('nama_ukm')->paginate(10)->withQueryString();

        $categories = Ukm::select('kategori')->distinct()->pluck('kategori');
        $totalUkm = Ukm::count();
        $totalKategori = $categories->count();
        $ukmWithPembina = Ukm::whereNotNull('pembina_nama')->where('pembina_nama', '!=', '')->count();

        return view('kemahasiswaan.ukm.index', compact(
            'ukms',
            'categories',
            'totalUkm',
            'totalKategori',
            'ukmWithPembina'
        ));
    }

    /**
     * Tampilkan formulir tambah UKM baru.
     */
    public function create(): View
    {
        $pembinas = User::where('role', 'pembina')->orderBy('name')->get();
        $commonCategories = [
            'Penalaran & Keilmuan',
            'Seni & Budaya',
            'Olahraga',
            'Kepetualangan & Lingkungan',
            'Keagamaan',
            'Kesejahteraan & Sosial',
            'Media & Jurnalistik',
            'Umum'
        ];

        return view('kemahasiswaan.ukm.create', compact('pembinas', 'commonCategories'));
    }

    /**
     * Simpan data UKM baru ke database.
     */
    public function store(StoreMasterUkmRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $pembinaNama = null;
        if ($request->filled('pembina_user_id')) {
            $pembinaUser = User::find($request->pembina_user_id);
            if ($pembinaUser) {
                $pembinaNama = $pembinaUser->name;
            }
        } elseif ($request->filled('pembina_nama_manual')) {
            $pembinaNama = $request->pembina_nama_manual;
        }

        $ukm = Ukm::create([
            'kode' => strtoupper(trim($validated['kode'])),
            'nama_ukm' => trim($validated['nama_ukm']),
            'kategori' => trim($validated['kategori']),
            'deskripsi' => $validated['deskripsi'] ? trim($validated['deskripsi']) : null,
            'pembina_nama' => $pembinaNama,
        ]);

        // Jika pembina dipilih dari user yang ada, hubungkan user tersebut ke UKM ini
        if ($request->filled('pembina_user_id')) {
            User::where('id', $request->pembina_user_id)->update(['ukm_id' => $ukm->id]);
        }

        return redirect()->route('kemahasiswaan.ukm.index')
            ->with('success', 'Master UKM "' . $ukm->nama_ukm . '" berhasil ditambahkan.');
    }

    /**
     * Tampilkan rincian profil UKM, daftar anggota, dan riwayat kegiatan.
     */
    public function show(int $id): View
    {
        $ukm = Ukm::with(['users', 'ajuanKegiatans' => function ($q) {
            $q->with(['user', 'details'])->latest();
        }])->withCount(['users', 'ajuanKegiatans'])->findOrFail($id);

        $pembinaUsers = $ukm->users->where('role', 'pembina');
        $mahasiswaUsers = $ukm->users->where('role', 'mahasiswa');

        $totalApproved = $ukm->ajuanKegiatans->where('status_akhir', 'disetujui')->count();
        $totalDanaDisetujui = 0;
        foreach ($ukm->ajuanKegiatans as $ajuan) {
            if ($ajuan->status_akhir === 'disetujui') {
                $totalDanaDisetujui += $ajuan->total_disetujui;
            }
        }

        return view('kemahasiswaan.ukm.show', compact(
            'ukm',
            'pembinaUsers',
            'mahasiswaUsers',
            'totalApproved',
            'totalDanaDisetujui'
        ));
    }

    /**
     * Tampilkan formulir edit UKM.
     */
    public function edit(int $id): View
    {
        $ukm = Ukm::with('users')->findOrFail($id);
        $pembinas = User::where('role', 'pembina')->orderBy('name')->get();
        $assignedPembina = User::where('ukm_id', $ukm->id)->where('role', 'pembina')->first();

        $commonCategories = [
            'Penalaran & Keilmuan',
            'Seni & Budaya',
            'Olahraga',
            'Kepetualangan & Lingkungan',
            'Keagamaan',
            'Kesejahteraan & Sosial',
            'Media & Jurnalistik',
            'Umum'
        ];

        return view('kemahasiswaan.ukm.edit', compact(
            'ukm',
            'pembinas',
            'assignedPembina',
            'commonCategories'
        ));
    }

    /**
     * Perbarui data master UKM.
     */
    public function update(UpdateMasterUkmRequest $request, int $id): RedirectResponse
    {
        $ukm = Ukm::findOrFail($id);

        $validated = $request->validated();

        $pembinaNama = $ukm->pembina_nama;
        if ($request->filled('pembina_user_id')) {
            $pembinaUser = User::find($request->pembina_user_id);
            if ($pembinaUser) {
                $pembinaNama = $pembinaUser->name;
                // Lepaskan pembina lama jika ada
                User::where('ukm_id', $ukm->id)
                    ->where('role', 'pembina')
                    ->where('id', '!=', $pembinaUser->id)
                    ->update(['ukm_id' => null]);
                // Kaitkan pembina baru
                $pembinaUser->update(['ukm_id' => $ukm->id]);
            }
        } elseif ($request->filled('pembina_nama_manual')) {
            $pembinaNama = $request->pembina_nama_manual;
        }

        $ukm->update([
            'kode' => strtoupper(trim($validated['kode'])),
            'nama_ukm' => trim($validated['nama_ukm']),
            'kategori' => trim($validated['kategori']),
            'deskripsi' => $validated['deskripsi'] ? trim($validated['deskripsi']) : null,
            'pembina_nama' => $pembinaNama,
        ]);

        return redirect()->route('kemahasiswaan.ukm.index')
            ->with('success', 'Data UKM "' . $ukm->nama_ukm . '" berhasil diperbarui.');
    }

    /**
     * Hapus data UKM dengan proteksi integritas usulan.
     */
    public function destroy(int $id): RedirectResponse
    {
        $ukm = Ukm::withCount('ajuanKegiatans')->findOrFail($id);

        // Proteksi: Jangan izinkan hapus jika ada riwayat ajuan kegiatan
        if ($ukm->ajuan_kegiatans_count > 0) {
            return back()->with('error', 'UKM "' . $ukm->nama_ukm . '" tidak dapat dihapus karena memiliki ' . $ukm->ajuan_kegiatans_count . ' riwayat usulan kegiatan. Silakan ubah data atau nonaktifkan saja.');
        }

        $namaUkm = $ukm->nama_ukm;

        // Lepaskan kaitan user yang terdaftar pada UKM ini
        User::where('ukm_id', $ukm->id)->update(['ukm_id' => null]);

        $ukm->delete();

        return redirect()->route('kemahasiswaan.ukm.index')
            ->with('success', 'Master UKM "' . $namaUkm . '" berhasil dihapus.');
    }
}
