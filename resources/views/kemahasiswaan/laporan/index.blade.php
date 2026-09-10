<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-xs font-bold px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-800 border border-blue-200">
                        Biro Kemahasiswaan & Alumni
                    </span>
                    <span class="text-xs text-gray-400">• Dashboard Laporan & Statistik</span>
                </div>
                <h2 class="font-black text-2xl text-gray-900 leading-tight">
                    Laporan Realisasi Usulan Kegiatan per UKM
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">
                    Rekapitulasi jumlah pengajuan, status realisasi persetujuan, serapan anggaran, dan rincian kegiatan setiap UKM.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('kemahasiswaan.dashboard') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl text-xs transition">
                    ← Kelola Ajuan
                </a>
                <a href="{{ route('kemahasiswaan.laporan.export', request()->query()) }}" class="inline-flex items-center px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-bold rounded-xl text-xs transition gap-2 shadow-sm shadow-emerald-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    <span>Download Excel (.xlsx)</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        <!-- 1. Executive Summary Cards (KPI Metrics) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Total Usulan -->
            <div class="bg-white rounded-2xl p-5 border border-gray-200 shadow-sm relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">Total Usulan Masuk</span>
                        <div class="flex items-baseline gap-2 mt-1">
                            <span class="text-3xl font-black text-gray-900">{{ $summary['total_usulan'] }}</span>
                            <span class="text-xs text-gray-500 font-medium">dari {{ $summary['total_ukm'] }} UKM</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                </div>
                <div class="mt-3 text-xs text-gray-500">
                    Total Anggaran Usulan: <strong class="text-gray-800">Rp {{ number_format($summary['total_anggaran_diajukan'], 0, ',', '.') }}</strong>
                </div>
            </div>

            <!-- Disetujui / Terealisasi -->
            <div class="bg-white rounded-2xl p-5 border border-emerald-200 shadow-sm bg-emerald-50/10 relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold text-emerald-700 uppercase tracking-wider block">Disetujui / Terealisasi</span>
                        <div class="flex items-baseline gap-2 mt-1">
                            <span class="text-3xl font-black text-emerald-600">{{ $summary['total_disetujui'] }}</span>
                            <span class="text-xs font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full">{{ $summary['persentase_realisasi'] }}%</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                </div>
                <div class="mt-3 text-xs text-emerald-800">
                    Anggaran Terealisasi: <strong class="text-emerald-900">Rp {{ number_format($summary['total_anggaran_disetujui'], 0, ',', '.') }}</strong>
                </div>
            </div>

            <!-- Dalam Proses -->
            <div class="bg-white rounded-2xl p-5 border border-amber-200 shadow-sm bg-amber-50/10 relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold text-amber-700 uppercase tracking-wider block">Dalam Proses Verifikasi</span>
                        <div class="flex items-baseline gap-2 mt-1">
                            <span class="text-3xl font-black text-amber-600">{{ $summary['total_proses'] }}</span>
                            <span class="text-xs text-gray-500 font-medium">usulan aktif</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center font-bold">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div class="mt-3 text-xs text-amber-800">
                    Menunggu Pembina, Kemahasiswaan, atau Revisi
                </div>
            </div>

            <!-- Ditolak -->
            <div class="bg-white rounded-2xl p-5 border border-rose-200 shadow-sm bg-rose-50/10 relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold text-rose-700 uppercase tracking-wider block">Ditolak / Perlu Perbaikan</span>
                        <div class="flex items-baseline gap-2 mt-1">
                            <span class="text-3xl font-black text-rose-600">{{ $summary['total_ditolak'] }}</span>
                            <span class="text-xs text-rose-600 font-medium">usulan</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center font-bold">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </div>
                </div>
                <div class="mt-3 text-xs text-rose-800">
                    Ditolak & dapat diperbaiki kembali oleh UKM
                </div>
            </div>
        </div>

        <!-- 2. Filter Toolbar -->
        <div class="bg-white rounded-2xl p-5 border border-gray-200 shadow-sm">
            <form method="GET" action="{{ route('kemahasiswaan.laporan') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <div class="sm:col-span-5">
                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Cari UKM / Kode / Pembina</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik nama UKM, kode, atau pembina..."
                           class="w-full rounded-xl border-gray-300 text-xs focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="sm:col-span-4">
                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Kategori UKM</label>
                    <select name="kategori" class="w-full rounded-xl border-gray-300 text-xs focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">-- Semua Kategori --</option>
                        @foreach($kategoris as $kat)
                            <option value="{{ $kat }}" {{ request('kategori') == $kat ? 'selected' : '' }}>{{ $kat }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm:col-span-3 flex items-center gap-2">
                    <button type="submit" class="w-full py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        Filter
                    </button>
                    @if(request()->hasAny(['search', 'kategori']))
                        <a href="{{ route('kemahasiswaan.laporan') }}" class="py-2.5 px-3 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl text-xs font-semibold transition" title="Reset Filter">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- 3. Tabel Rekapitulasi per UKM -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="font-black text-gray-900 text-base">Tabel Rekapitulasi Realisasi per UKM</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Daftar seluruh Unit Kegiatan Mahasiswa beserta metrik performa usulan</p>
                </div>
                <span class="text-xs font-bold bg-slate-100 text-slate-700 px-3 py-1 rounded-full">
                    {{ count($ukmStats) }} UKM Terdaftar
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50/80 text-gray-600 font-bold uppercase tracking-wider border-b border-gray-200">
                            <th class="px-5 py-3.5 text-center w-12">No</th>
                            <th class="px-5 py-3.5">Organisasi / UKM</th>
                            <th class="px-5 py-3.5">Dosen Pembina</th>
                            <th class="px-4 py-3.5 text-center">Total Usulan</th>
                            <th class="px-4 py-3.5 text-center">Disetujui</th>
                            <th class="px-4 py-3.5 text-center">Dalam Proses</th>
                            <th class="px-4 py-3.5 text-center">Ditolak</th>
                            <th class="px-5 py-3.5 text-center">Tingkat Realisasi</th>
                            <th class="px-5 py-3.5 text-right">Anggaran Disetujui</th>
                            <th class="px-5 py-3.5 text-center w-28">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($ukmStats as $index => $stat)
                            <tr class="hover:bg-indigo-50/20 transition group">
                                <td class="px-5 py-4 text-center font-bold text-gray-400">
                                    {{ $index + 1 }}
                                </td>
                                <td class="px-5 py-4">
                                    <div class="font-bold text-gray-900 text-sm group-hover:text-indigo-600 transition">
                                        {{ $stat['nama_ukm'] }}
                                    </div>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-gray-100 text-gray-700 font-mono">
                                            {{ $stat['kode'] }}
                                        </span>
                                        <span class="text-[11px] text-gray-400">{{ $stat['kategori'] }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-gray-700">
                                    {{ $stat['pembina_nama'] }}
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <span class="font-black text-gray-900 text-sm">{{ $stat['total_usulan'] }}</span>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    @if($stat['disetujui_count'] > 0)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                            ✓ {{ $stat['disetujui_count'] }}
                                        </span>
                                    @else
                                        <span class="text-gray-300 font-medium">0</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-center">
                                    @if($stat['proses_count'] > 0)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                                            ⏳ {{ $stat['proses_count'] }}
                                        </span>
                                    @else
                                        <span class="text-gray-300 font-medium">0</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-center">
                                    @if($stat['ditolak_count'] > 0)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800">
                                            ✕ {{ $stat['ditolak_count'] }}
                                        </span>
                                    @else
                                        <span class="text-gray-300 font-medium">0</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <div class="w-16 bg-gray-200 rounded-full h-2 overflow-hidden">
                                            <div class="bg-emerald-500 h-2 rounded-full" style="width: {{ min(100, $stat['persentase_realisasi']) }}%"></div>
                                        </div>
                                        <span class="font-bold text-gray-700 min-w-[36px] text-right">{{ $stat['persentase_realisasi'] }}%</span>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <div class="font-bold text-emerald-700 font-mono text-sm">
                                        Rp {{ number_format($stat['total_anggaran_disetujui'], 0, ',', '.') }}
                                    </div>
                                    <div class="text-[10px] text-gray-400">
                                        Usulan: Rp {{ number_format($stat['total_anggaran_diajukan'], 0, ',', '.') }}
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <a href="{{ route('kemahasiswaan.laporan.detail', $stat['id']) }}" class="inline-flex items-center px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold rounded-lg text-xs transition gap-1 border border-indigo-200">
                                        <span>Detail</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-6 py-12 text-center text-gray-400">
                                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    <p class="font-medium text-gray-600">Tidak ada data UKM yang sesuai dengan kriteria pencarian.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-slate-50 border-t-2 border-gray-200 font-bold text-xs">
                        <tr>
                            <td colspan="3" class="px-5 py-4 text-right uppercase tracking-wider text-gray-700">
                                TOTAL KESELURUHAN UNIVERSITAS:
                            </td>
                            <td class="px-4 py-4 text-center text-base font-black text-gray-900">
                                {{ $summary['total_usulan'] }}
                            </td>
                            <td class="px-4 py-4 text-center text-emerald-700 font-bold">
                                {{ $summary['total_disetujui'] }}
                            </td>
                            <td class="px-4 py-4 text-center text-amber-700 font-bold">
                                {{ $summary['total_proses'] }}
                            </td>
                            <td class="px-4 py-4 text-center text-rose-700 font-bold">
                                {{ $summary['total_ditolak'] }}
                            </td>
                            <td class="px-5 py-4 text-center text-emerald-800 font-black text-sm">
                                {{ $summary['persentase_realisasi'] }}%
                            </td>
                            <td class="px-5 py-4 text-right font-mono font-black text-sm text-emerald-700">
                                Rp {{ number_format($summary['total_anggaran_disetujui'], 0, ',', '.') }}
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
