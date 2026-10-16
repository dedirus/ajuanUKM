<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMasterSatuanRequest;
use App\Http\Requests\UpdateMasterSatuanRequest;
use App\Models\DetailKegiatan;
use App\Models\Satuan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MasterSatuanController extends Controller
{
    /**
     * Tampilkan daftar master satuan dengan filter dan status pemakaian.
     */
    public function index(Request $request): View
    {
        $query = Satuan::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nama_satuan', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%");
        }

        if ($request->filled('status')) {
            $query->where('is_aktif', $request->status === 'aktif');
        }

        $satuans = $query->orderBy('nama_satuan')->paginate(15)->withQueryString();

        // Hitung pemakaian tiap satuan di detail_kegiatans
        foreach ($satuans as $s) {
            $s->usage_count = DetailKegiatan::where('satuan', $s->nama_satuan)->count();
        }

        $totalSatuan = Satuan::count();
        $totalAktif = Satuan::where('is_aktif', true)->count();
        $totalNonAktif = Satuan::where('is_aktif', false)->count();

        return view('kemahasiswaan.satuan.index', compact(
            'satuans',
            'totalSatuan',
            'totalAktif',
            'totalNonAktif'
        ));
    }

    /**
     * Simpan master satuan baru ke database.
     */
    public function store(StoreMasterSatuanRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $validated['nama_satuan'] = trim($validated['nama_satuan']);
        $validated['is_aktif'] = $request->has('is_aktif');

        Satuan::create($validated);

        return redirect()->route('kemahasiswaan.satuan.index')
            ->with('success', "Satuan '{$validated['nama_satuan']}' berhasil ditambahkan ke master data!");
    }

    /**
     * Perbarui master satuan yang ada.
     */
    public function update(UpdateMasterSatuanRequest $request, int $id): RedirectResponse
    {
        $satuan = Satuan::findOrFail($id);

        $validated = $request->validated();

        $oldName = $satuan->nama_satuan;
        $newName = trim($validated['nama_satuan']);

        $satuan->update([
            'nama_satuan' => $newName,
            'keterangan' => $validated['keterangan'] ?? null,
            'is_aktif' => $request->has('is_aktif'),
        ]);

        // Sinkronkan nama satuan pada detail_kegiatans yang sudah pernah tersimpan jika nama berubah
        if ($oldName !== $newName) {
            DetailKegiatan::where('satuan', $oldName)->update(['satuan' => $newName]);
        }

        return redirect()->route('kemahasiswaan.satuan.index')
            ->with('success', "Satuan '{$newName}' berhasil diperbarui!");
    }

    /**
     * Hapus master satuan jika belum digunakan pada pengajuan RAB.
     */
    public function destroy(int $id): RedirectResponse
    {
        $satuan = Satuan::findOrFail($id);
        $usageCount = DetailKegiatan::where('satuan', $satuan->nama_satuan)->count();

        if ($usageCount > 0) {
            return redirect()->route('kemahasiswaan.satuan.index')
                ->with('error', "Satuan '{$satuan->nama_satuan}' tidak dapat dihapus karena telah digunakan pada {$usageCount} item RAB kegiatan. Silakan ubah status menjadi Non-Aktif jika tidak ingin menampilkannya lagi.");
        }

        $nama = $satuan->nama_satuan;
        $satuan->delete();

        return redirect()->route('kemahasiswaan.satuan.index')
            ->with('success', "Satuan '{$nama}' berhasil dihapus dari sistem!");
    }
}
