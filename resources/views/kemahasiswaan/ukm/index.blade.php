<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-black text-2xl text-gray-900 leading-tight flex items-center gap-2">
                    <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Master Data Unit Kegiatan Mahasiswa (UKM)
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">
                    Kelola data kelembagaan organisasi mahasiswa, kategori, dan penugasan dosen pembina.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('kemahasiswaan.ukm.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-md shadow-indigo-200 transition">
                    <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah UKM Baru
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
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            <div class="bg-white rounded-2xl p-5 border border-gray-200 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 block">Total UKM Terdaftar</span>
                    <span class="text-2xl font-black text-gray-900">{{ $totalUkm }} <span class="text-xs font-medium text-gray-500">Organisasi</span></span>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-gray-200 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-purple-50 border border-purple-100 flex items-center justify-center text-purple-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                </div>
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 block">Kategori Organisasi</span>
                    <span class="text-2xl font-black text-gray-900">{{ $totalKategori }} <span class="text-xs font-medium text-gray-500">Bidang</span></span>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-gray-200 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 block">UKM dengan Pembina</span>
                    <span class="text-2xl font-black text-emerald-700">{{ $ukmWithPembina }} <span class="text-xs font-medium text-gray-500">/ {{ $totalUkm }} UKM</span></span>
                </div>
            </div>
        </div>

        <!-- Filter & Search Toolbar -->
        <div class="bg-white rounded-2xl p-5 border border-gray-200 shadow-sm">
            <form action="{{ route('kemahasiswaan.ukm.index') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-3">
                <div class="relative flex-1 w-full">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Cari kode, nama UKM, atau nama pembina..."
                           class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div class="w-full sm:w-64">
                    <select name="kategori" class="w-full py-2 px-3 border border-gray-300 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">-- Semua Kategori --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ request('kategori') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-gray-900 hover:bg-black text-white text-sm font-semibold rounded-xl transition">
                        Filter
                    </button>
                    @if(request()->hasAny(['search', 'kategori']))
                        <a href="{{ route('kemahasiswaan.ukm.index') }}" class="w-full sm:w-auto px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-xl text-center transition">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tabel Data Master UKM -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-bold text-gray-900 text-base">Daftar Master UKM Terdaftar</h3>
                <span class="text-xs text-gray-500">Menampilkan {{ $ukms->count() }} dari total {{ $ukms->total() }} UKM</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600">
                    <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider border-b border-gray-200">
                        <tr>
                            <th class="px-5 py-3.5 w-12 text-center">No</th>
                            <th class="px-5 py-3.5">Kode & Nama UKM</th>
                            <th class="px-5 py-3.5">Kategori</th>
                            <th class="px-5 py-3.5">Dosen Pembina</th>
                            <th class="px-5 py-3.5 text-center">Anggota</th>
                            <th class="px-5 py-3.5 text-center">Total Usulan</th>
                            <th class="px-5 py-3.5 text-right w-44">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 font-normal">
                        @forelse($ukms as $index => $ukm)
                            <tr class="hover:bg-gray-50/80 transition">
                                <td class="px-5 py-4 text-center text-xs text-gray-400">
                                    {{ $ukms->firstItem() + $index }}
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100 font-mono tracking-wider">
                                            {{ $ukm->kode }}
                                        </span>
                                        <div>
                                            <a href="{{ route('kemahasiswaan.ukm.show', $ukm->id) }}" class="font-bold text-gray-900 hover:text-indigo-600 transition block">
                                                {{ $ukm->nama_ukm }}
                                            </a>
                                            @if($ukm->deskripsi)
                                                <span class="text-xs text-gray-400 truncate max-w-sm block">
                                                    {{ Str::limit($ukm->deskripsi, 60) }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ $ukm->kategori }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    @if($ukm->pembina_nama)
                                        <div class="flex items-center gap-1.5 text-gray-800 font-medium">
                                            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                            <span>{{ $ukm->pembina_nama }}</span>
                                        </div>
                                    @else
                                        <span class="text-xs text-amber-600 bg-amber-50 px-2 py-0.5 rounded font-medium">
                                            Belum Ditugaskan
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-blue-700">
                                        {{ $ukm->users_count }} Akun
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-purple-50 text-purple-700">
                                        {{ $ukm->ajuan_kegiatans_count }} Ajuan
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('kemahasiswaan.ukm.show', $ukm->id) }}"
                                           title="Detail Profil UKM"
                                           class="p-1.5 text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>
                                        <a href="{{ route('kemahasiswaan.ukm.edit', $ukm->id) }}"
                                           title="Edit UKM"
                                           class="p-1.5 text-gray-500 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>

                                        @if($ukm->ajuan_kegiatans_count == 0)
                                            <form action="{{ route('kemahasiswaan.ukm.destroy', $ukm->id) }}" method="POST"
                                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus UKM {{ $ukm->nama_ukm }}? Data yang dihapus tidak dapat dikembalikan.');"
                                                  class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" title="Hapus UKM"
                                                        class="p-1.5 text-gray-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        @else
                                            <span title="UKM memiliki {{ $ukm->ajuan_kegiatans_count }} riwayat usulan sehingga diproteksi dari penghapusan"
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
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                    </svg>
                                    Tidak ada data UKM yang cocok dengan kriteria pencarian.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($ukms->hasPages())
                <div class="p-4 border-t border-gray-100 bg-gray-50/50">
                    {{ $ukms->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
