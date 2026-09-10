<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-black text-2xl text-gray-900 leading-tight">
                    Pengelolaan Ajuan Kegiatan Seluruh UKM
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">
                    Biro Kemahasiswaan & Alumni Universitas
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 bg-amber-100 text-amber-900 rounded-full text-xs font-bold">
                    Role: Kemahasiswaan
                </span>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        <!-- Filter Box -->
        <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                    <h3 class="font-bold text-gray-900 text-sm">Filter & Pencarian Usulan Kegiatan</h3>
                </div>
                @if(request()->hasAny(['ukm_id', 'tanggal_kegiatan', 'search', 'status_pembina']))
                    <a href="{{ route('kemahasiswaan.dashboard') }}" class="text-xs text-rose-600 hover:underline font-semibold">
                        ✕ Reset Filter
                    </a>
                @endif
            </div>

            <form action="{{ route('kemahasiswaan.dashboard') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                <!-- Filter Nama UKM -->
                <div>
                    <label for="ukm_id" class="block text-xs font-bold text-gray-700 uppercase mb-1">
                        Filter Nama UKM
                    </label>
                    <select name="ukm_id" id="ukm_id" class="w-full rounded-xl border-gray-300 text-xs focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
                        <option value="">-- Semua UKM --</option>
                        @foreach($ukms as $ukm)
                            <option value="{{ $ukm->id }}" {{ request('ukm_id') == $ukm->id ? 'selected' : '' }}>
                                {{ $ukm->nama_ukm }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Tanggal Kegiatan -->
                <div>
                    <label for="tanggal_kegiatan" class="block text-xs font-bold text-gray-700 uppercase mb-1">
                        Filter Tanggal Kegiatan
                    </label>
                    <input type="date" name="tanggal_kegiatan" id="tanggal_kegiatan" value="{{ request('tanggal_kegiatan') }}"
                           class="w-full rounded-xl border-gray-300 text-xs focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
                </div>

                <!-- Cari Nama Kegiatan -->
                <div>
                    <label for="search" class="block text-xs font-bold text-gray-700 uppercase mb-1">
                        Nama Kegiatan
                    </label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Ketik nama kegiatan..."
                           class="w-full rounded-xl border-gray-300 text-xs focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
                </div>

                <!-- Tombol Submit Filter -->
                <div class="flex items-end gap-2">
                    <button type="submit" class="w-full px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white rounded-xl text-xs font-bold shadow-sm transition flex items-center justify-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        Terapkan Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Tabel Ajuan Seluruh UKM -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <h3 class="font-bold text-gray-900 text-lg">Tabel Usulan Kegiatan Seluruh UKM</h3>
                    <p class="text-xs text-gray-500">Daftar usulan kegiatan mahasiswa dari semua unit kegiatan di lingkungan kampus.</p>
                </div>
                <div class="text-xs text-gray-500 font-medium">
                    Menampilkan {{ $ajuans->count() }} dari total {{ $ajuans->total() }} usulan
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-slate-50 text-gray-600 text-xs uppercase font-bold tracking-wider">
                        <tr>
                            <th class="px-5 py-3.5 text-left w-12">No</th>
                            <th class="px-5 py-3.5 text-left">Nama UKM</th>
                            <th class="px-5 py-3.5 text-left">Nama Kegiatan</th>
                            <th class="px-5 py-3.5 text-left">Tanggal Kegiatan</th>
                            <th class="px-5 py-3.5 text-center">Verifikasi Pembina</th>
                            <th class="px-5 py-3.5 text-center">Keterangan</th>
                            <th class="px-5 py-3.5 text-left">Catatan</th>
                            <th class="px-5 py-3.5 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($ajuans as $index => $ajuan)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="px-5 py-4 font-bold text-gray-400">
                                    {{ $ajuans->firstItem() + $index }}
                                </td>
                                <!-- Nama UKM -->
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-extrabold bg-indigo-50 text-indigo-800 border border-indigo-100">
                                        {{ $ajuan->ukm->nama_ukm ?? '-' }}
                                    </span>
                                    <div class="text-[11px] text-gray-400 mt-1">
                                        Kode: {{ $ajuan->ukm->kode ?? '-' }}
                                    </div>
                                </td>
                                <!-- Nama Kegiatan -->
                                <td class="px-5 py-4">
                                    <div class="font-bold text-gray-900 text-sm">
                                        {{ $ajuan->nama_kegiatan }}
                                    </div>
                                    <div class="text-xs text-indigo-700 font-mono font-semibold mt-0.5">
                                        Total: Rp {{ number_format($ajuan->total_anggaran, 0, ',', '.') }}
                                    </div>
                                </td>
                                <!-- Tanggal Kegiatan -->
                                <td class="px-5 py-4 text-gray-700 whitespace-nowrap text-xs">
                                    {{ \Carbon\Carbon::parse($ajuan->tanggal_kegiatan)->isoFormat('D MMMM Y') }}
                                </td>
                                <!-- Verifikasi Pembina sesuai ketentuan soal -->
                                <td class="px-5 py-4 text-center whitespace-nowrap">
                                    @if($ajuan->status_pembina === 'disetujui')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                            ✓ Disetujui
                                        </span>
                                    @elseif($ajuan->status_pembina === 'revisi')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                                            ⚠️ Perlu Revisi
                                        </span>
                                    @elseif($ajuan->status_pembina === 'ditolak')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800">
                                            ✗ Ditolak
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                                            ⏳ Belum Disetujui
                                        </span>
                                    @endif
                                </td>
                                <!-- Keterangan (Status Kemahasiswaan / Status Akhir) -->
                                <td class="px-5 py-4 text-center whitespace-nowrap">
                                    @if($ajuan->status_kemahasiswaan === 'disetujui')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                            ✓ Disetujui
                                        </span>
                                    @elseif($ajuan->status_kemahasiswaan === 'revisi')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                                            ⚠️ Perlu Revisi
                                        </span>
                                    @elseif($ajuan->status_kemahasiswaan === 'ditolak')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800">
                                            ✗ Ditolak
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                                            ⏳ Belum Disetujui
                                        </span>
                                    @endif
                                </td>
                                <!-- Catatan -->
                                <td class="px-5 py-4 text-xs text-gray-600 max-w-xs">
                                    <div>{{ $ajuan->catatan_kemahasiswaan ?? ($ajuan->catatan_pembina ?? '-') }}</div>
                                </td>
                                <!-- Link Tombol Detail & Cetak PDF -->
                                <td class="px-5 py-4 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <a href="{{ route('kemahasiswaan.show', $ajuan->id) }}" class="inline-flex items-center px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-bold shadow-sm transition gap-1">
                                            <span>Detail</span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                        </a>
                                        <a href="{{ route('kemahasiswaan.cetak-pengesahan', $ajuan->id) }}" target="_blank" title="Cetak Lembar Pengesahan PDF" class="inline-flex items-center p-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-lg text-xs font-bold border border-emerald-200 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-gray-400">
                                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    <p class="font-medium text-gray-600">Tidak ada data ajuan kegiatan yang sesuai dengan filter.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($ajuans->hasPages())
                <div class="p-4 border-t border-gray-100">
                    {{ $ajuans->links() }}
                </div>
            @endif
        </div>

    </div>
</x-app-layout>
