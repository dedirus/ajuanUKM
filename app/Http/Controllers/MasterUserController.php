<?php

namespace App\Http\Controllers;

use App\Models\Ukm;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MasterUserController extends Controller
{
    /**
     * Tampilkan daftar pengguna dengan filter peran, UKM, dan pencarian.
     */
    public function index(Request $request): View
    {
        $query = User::with('ukm')->withCount('ajuanKegiatans');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('nim_nip', 'like', "%{$search}%")
                  ->orWhere('no_hp', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('ukm_id')) {
            $query->where('ukm_id', $request->ukm_id);
        }

        $users = $query->orderBy('role')->orderBy('name')->paginate(15)->withQueryString();

        $ukms = Ukm::orderBy('nama_ukm')->get();
        $totalUsers = User::count();
        $totalMahasiswa = User::where('role', 'mahasiswa')->count();
        $totalPembina = User::where('role', 'pembina')->count();
        $totalKemahasiswaan = User::where('role', 'kemahasiswaan')->count();

        return view('kemahasiswaan.users.index', compact(
            'users',
            'ukms',
            'totalUsers',
            'totalMahasiswa',
            'totalPembina',
            'totalKemahasiswaan'
        ));
    }

    /**
     * Tampilkan formulir tambah akun pengguna baru.
     */
    public function create(): View
    {
        $ukms = Ukm::orderBy('nama_ukm')->get();
        return view('kemahasiswaan.users.create', compact('ukms'));
    }

    /**
     * Simpan pengguna baru ke database.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:mahasiswa,pembina,kemahasiswaan',
            'ukm_id' => [
                'nullable',
                Rule::requiredIf(fn () => in_array($request->role, ['mahasiswa', 'pembina'])),
                'exists:ukms,id'
            ],
            'nim_nip' => 'nullable|string|max:50',
            'no_hp' => 'nullable|string|max:25',
        ], [
            'ukm_id.required_if' => 'UKM wajib dipilih untuk role Mahasiswa atau Pembina.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'password.min' => 'Kata sandi minimal berjumlah 8 karakter.',
            'email.unique' => 'Alamat email sudah terdaftar di sistem.',
        ]);

        $user = User::create([
            'name' => trim($validated['name']),
            'email' => strtolower(trim($validated['email'])),
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'ukm_id' => $validated['role'] === 'kemahasiswaan' ? null : $validated['ukm_id'],
            'nim_nip' => $validated['nim_nip'] ? trim($validated['nim_nip']) : null,
            'no_hp' => $validated['no_hp'] ? trim($validated['no_hp']) : null,
        ]);

        // Jika user adalah pembina dan UKM belum memiliki nama pembina, perbarui otomatis
        if ($user->role === 'pembina' && $user->ukm_id) {
            $ukm = Ukm::find($user->ukm_id);
            if ($ukm && empty($ukm->pembina_nama)) {
                $ukm->update(['pembina_nama' => $user->name]);
            }
        }

        return redirect()->route('kemahasiswaan.users.index')
            ->with('success', 'Akun pengguna "' . $user->name . '" (' . ucfirst($user->role) . ') berhasil dibuat.');
    }

    /**
     * Tampilkan formulir edit data pengguna.
     */
    public function edit(int $id): View
    {
        $user = User::with('ukm')->findOrFail($id);
        $ukms = Ukm::orderBy('nama_ukm')->get();

        return view('kemahasiswaan.users.edit', compact('user', 'ukms'));
    }

    /**
     * Perbarui data pengguna di database.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:mahasiswa,pembina,kemahasiswaan',
            'ukm_id' => [
                'nullable',
                Rule::requiredIf(fn () => in_array($request->role, ['mahasiswa', 'pembina'])),
                'exists:ukms,id'
            ],
            'nim_nip' => 'nullable|string|max:50',
            'no_hp' => 'nullable|string|max:25',
        ], [
            'ukm_id.required_if' => 'UKM wajib dipilih untuk role Mahasiswa atau Pembina.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
            'password.min' => 'Kata sandi baru minimal berjumlah 8 karakter.',
            'email.unique' => 'Alamat email sudah terdaftar di sistem.',
        ]);

        $userData = [
            'name' => trim($validated['name']),
            'email' => strtolower(trim($validated['email'])),
            'role' => $validated['role'],
            'ukm_id' => $validated['role'] === 'kemahasiswaan' ? null : $validated['ukm_id'],
            'nim_nip' => $validated['nim_nip'] ? trim($validated['nim_nip']) : null,
            'no_hp' => $validated['no_hp'] ? trim($validated['no_hp']) : null,
        ];

        // Jika ada password baru diisi
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);

        // Jika role pembina, perbarui nama pembina pada profil UKM
        if ($user->role === 'pembina' && $user->ukm_id) {
            $ukm = Ukm::find($user->ukm_id);
            if ($ukm) {
                $ukm->update(['pembina_nama' => $user->name]);
            }
        }

        return redirect()->route('kemahasiswaan.users.index')
            ->with('success', 'Data akun "' . $user->name . '" berhasil diperbarui.');
    }

    /**
     * Hapus pengguna dengan proteksi akun aktif dan riwayat usulan.
     */
    public function destroy(int $id): RedirectResponse
    {
        $user = User::withCount('ajuanKegiatans')->findOrFail($id);

        // Proteksi 1: Tidak bisa menghapus diri sendiri yang sedang login
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Tindakan ditolak: Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif login.');
        }

        // Proteksi 2: Tidak bisa menghapus mahasiswa yang sudah memiliki riwayat usulan kegiatan
        if ($user->ajuan_kegiatans_count > 0) {
            return back()->with('error', 'Tindakan ditolak: Pengguna "' . $user->name . '" tidak dapat dihapus karena tercatat memiliki ' . $user->ajuan_kegiatans_count . ' riwayat usulan kegiatan.');
        }

        $namaUser = $user->name;
        $user->delete();

        return redirect()->route('kemahasiswaan.users.index')
            ->with('success', 'Akun pengguna "' . $namaUser . '" berhasil dihapus.');
    }
}
