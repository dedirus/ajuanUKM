<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-black text-2xl text-gray-900 leading-tight flex items-center gap-2">
                    <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    Manajemen Akun Pengguna Sistem
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">
                    Kelola hak akses pengguna, penugasan peran (Mahasiswa, Pembina, Kemahasiswaan), dan integrasi UKM.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('kemahasiswaan.users.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-md shadow-indigo-200 transition">
                    <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Pengguna Baru
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center gap-3 text-emerald-800 text-sm">
                <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl flex items-center gap-3 text-rose-800 text-sm">
                <svg class="w-5 h-5 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Summary Metric Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-5">
            <div class="bg-white rounded-2xl p-5 border border-gray-200 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gray-100 flex items-center justify-center text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 block">Total Pengguna</span>
                    <span class="text-2xl font-black text-gray-900">{{ $totalUsers }} <span class="text-xs font-medium text-gray-500">Akun</span></span>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-gray-200 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                    </svg>
                </div>
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 block">Mahasiswa</span>
                    <span class="text-2xl font-black text-emerald-700">{{ $totalMahasiswa }} <span class="text-xs font-medium text-gray-500">Akun</span></span>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-gray-200 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-purple-50 border border-purple-100 flex items-center justify-center text-purple-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 block">Dosen Pembina</span>
                    <span class="text-2xl font-black text-purple-700">{{ $totalPembina }} <span class="text-xs font-medium text-gray-500">Akun</span></span>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-gray-200 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 block">Kemahasiswaan</span>
                    <span class="text-2xl font-black text-amber-700">{{ $totalKemahasiswaan }} <span class="text-xs font-medium text-gray-500">Akun</span></span>
                </div>
            </div>
        </div>

        <!-- Filter & Search Toolbar -->
        <div class="bg-white rounded-2xl p-5 border border-gray-200 shadow-sm">
            <form action="{{ route('kemahasiswaan.users.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                <div class="relative sm:col-span-5">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Cari nama, email, NIM, atau NIP..."
                           class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div class="sm:col-span-3">
                    <select name="role" class="w-full py-2 px-3 border border-gray-300 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">-- Semua Role --</option>
                        <option value="mahasiswa" {{ request('role') == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa UKM</option>
                        <option value="pembina" {{ request('role') == 'pembina' ? 'selected' : '' }}>Dosen Pembina</option>
                        <option value="kemahasiswaan" {{ request('role') == 'kemahasiswaan' ? 'selected' : '' }}>Kemahasiswaan</option>
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <select name="ukm_id" class="w-full py-2 px-3 border border-gray-300 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">-- Semua UKM --</option>
                        @foreach($ukms as $ukm)
                            <option value="{{ $ukm->id }}" {{ request('ukm_id') == $ukm->id ? 'selected' : '' }}>{{ $ukm->kode }} - {{ $ukm->nama_ukm }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2 flex items-center gap-2">
                    <button type="submit" class="flex-1 py-2 px-3 bg-gray-900 hover:bg-black text-white text-sm font-semibold rounded-xl transition text-center">
                        Cari
                    </button>
                    @if(request()->hasAny(['search', 'role', 'ukm_id']))
                        <a href="{{ route('kemahasiswaan.users.index') }}" class="py-2 px-3 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-xl text-center transition">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tabel Data Pengguna -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-bold text-gray-900 text-base">Daftar Akun Pengguna</h3>
                <span class="text-xs text-gray-500">Menampilkan {{ $users->count() }} dari total {{ $users->total() }} akun</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600">
                    <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider border-b border-gray-200">
                        <tr>
                            <th class="px-5 py-3.5 w-12 text-center">No</th>
                            <th class="px-5 py-3.5">Nama & Identitas</th>
                            <th class="px-5 py-3.5">Email Akun</th>
                            <th class="px-5 py-3.5 text-center">Role / Peran</th>
                            <th class="px-5 py-3.5">UKM Terkait</th>
                            <th class="px-5 py-3.5">Kontak HP</th>
                            <th class="px-5 py-3.5 text-right w-36">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 font-normal">
                        @forelse($users as $index => $u)
                            <tr class="hover:bg-gray-50/80 transition {{ $u->id === Auth::id() ? 'bg-indigo-50/30' : '' }}">
                                <td class="px-5 py-4 text-center text-xs text-gray-400">
                                    {{ $users->firstItem() + $index }}
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-2">
                                        <div>
                                            <div class="font-bold text-gray-900 flex items-center gap-1.5">
                                                {{ $u->name }}
                                                @if($u->id === Auth::id())
                                                    <span class="px-1.5 py-0.5 text-[10px] bg-indigo-100 text-indigo-800 rounded font-semibold">Anda</span>
                                                @endif
                                            </div>
                                            <span class="text-xs text-gray-400 block">
                                                {{ $u->nim_nip ? ($u->role === 'mahasiswa' ? 'NIM: ' : 'NIP/NIDN: ') . $u->nim_nip : 'Tanpa NIM/NIP' }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4 font-mono text-xs text-gray-700">
                                    {{ $u->email }}
                                </td>
                                <td class="px-5 py-4 text-center">
                                    @if($u->role === 'mahasiswa')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                            Mahasiswa
                                        </span>
                                    @elseif($u->role === 'pembina')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-100 text-purple-800">
                                            Pembina UKM
                                        </span>
                                    @elseif($u->role === 'kemahasiswaan')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                                            Kemahasiswaan
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-4">
                                    @if($u->ukm)
                                        <a href="{{ route('kemahasiswaan.ukm.show', $u->ukm->id) }}" class="inline-flex items-center gap-1.5 text-xs text-indigo-700 hover:underline font-semibold">
                                            <span class="px-1.5 py-0.5 bg-gray-100 rounded text-[10px] text-gray-700 font-mono">{{ $u->ukm->kode }}</span>
                                            {{ $u->ukm->nama_ukm }}
                                        </a>
                                    @elseif($u->role === 'kemahasiswaan')
                                        <span class="text-xs text-gray-400 italic">Seluruh UKM (Admin)</span>
                                    @else
                                        <span class="text-xs text-amber-600 font-medium">Belum Dipilih</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-xs text-gray-500 font-mono">
                                    {{ $u->no_hp ?: '-' }}
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('kemahasiswaan.users.edit', $u->id) }}"
                                           title="Edit Pengguna"
                                           class="p-1.5 text-gray-500 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>

                                        @if($u->id !== Auth::id() && $u->ajuan_kegiatans_count == 0)
                                            <form action="{{ route('kemahasiswaan.users.destroy', $u->id) }}" method="POST"
                                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun {{ $u->name }}? Data yang dihapus tidak dapat dikembalikan.');"
                                                  class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" title="Hapus Pengguna"
                                                        class="p-1.5 text-gray-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        @else
                                            <span title="{{ $u->id === Auth::id() ? 'Tidak dapat menghapus akun sendiri' : 'Memiliki ' . $u->ajuan_kegiatans_count . ' usulan kegiatan' }}"
                                                  class="p-1.5 text-gray-300 cursor-not-allowed">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                                </svg>
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-12 text-center text-gray-400">
                                    <svg class="w-10 h-10 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    Tidak ada pengguna yang cocok dengan kriteria pencarian.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($users->hasPages())
                <div class="p-4 border-t border-gray-100 bg-gray-50/50">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
