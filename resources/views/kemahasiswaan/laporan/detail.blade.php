<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-xs font-bold px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-800 border border-blue-200">
                        Laporan Detail UKM
                    </span>
                    <span class="text-xs text-gray-400">• Kode: {{ $ukm->kode }}</span>
                </div>
                <h2 class="font-black text-2xl text-gray-900 leading-tight">
                    {{ $ukm->nama_ukm }}
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">
                    Kategori: <strong class="text-gray-700">{{ $ukm->kategori ?? 'Umum' }}</strong> &nbsp;|&nbsp;
                    Dosen Pembina: <strong class="text-gray-700">{{ $ukm->pembina_nama ?? '-' }}</strong>
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('kemahasiswaan.laporan') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl text-xs transition">
                    ← Kembali ke Laporan Rekapitulasi
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        <!-- Ringkasan Statistik UKM Ini -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="bg-white rounded-2xl p-4 border border-gray-200 shadow-sm">
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">Total Usulan</span>
                <div class="text-2xl font-black text-gray-900 mt-1">{{ $stats['total_usulan'] }}</div>
                <div class="text-[11px] text-gray-400 mt-0.5">Semua pengajuan kegiatan</div>
            </div>
            <div class="bg-white rounded-2xl p-4 border border-emerald-200 shadow-sm bg-emerald-50/20">
                <span class="text-xs font-semibold text-emerald-700 uppercase tracking-wider block">Disetujui / Terealisasi</span>
                <div class="flex items-baseline gap-2 mt-1">
                    <span class="text-2xl font-black text-emerald-600">{{ $stats['disetujui_count'] }}</span>
                    <span class="text-xs font-bold text-emerald-700 bg-emerald-100 px-1.5 py-0.5 rounded">{{ $stats['persentase_realisasi'] }}%</span>
                </div>
                <div class="text-[11px] text-emerald-800 mt-0.5">Rp {{ number_format($stats['total_anggaran_disetujui'], 0, ',', '.') }}</div>
            </div>
            <div class="bg-white rounded-2xl p-4 border border-amber-200 shadow-sm bg-amber-50/20">
                <span class="text-xs font-semibold text-amber-700 uppercase tracking-wider block">Dalam Proses</span>
                <div class="text-2xl font-black text-amber-600 mt-1">{{ $stats['proses_count'] }}</div>
                <div class="text-[11px] text-amber-800 mt-0.5">Menunggu / Revisi</div>
            </div>
            <div class="bg-white rounded-2xl p-4 border border-rose-200 shadow-sm bg-rose-50/20">
                <span class="text-xs font-semibold text-rose-700 uppercase tracking-wider block">Ditolak</span>
                <div class="text-2xl font-black text-rose-600 mt-1">{{ $stats['ditolak_count'] }}</div>
                <div class="text-[11px] text-rose-800 mt-0.5">Perlu perbaikan usulan</div>
            </div>
        </div>

        <!-- Tabel Daftar Usulan Kegiatan UKM Ini -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="font-black text-gray-900 text-base">Daftar Seluruh Usulan Kegiatan ({{ $ukm->nama_ukm }})</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Rincian status verifikasi berjenjang dan rincian anggaran setiap kegiatan</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 text-gray-600 font-bold uppercase tracking-wider border-b border-gray-200">
                            <th class="px-5 py-3.5 text-center w-12">No</th>
                            <th class="px-5 py-3.5">Nama Kegiatan</th>
                            <th class="px-5 py-3.5">Tanggal Pelaksanaan</th>
                            <th class="px-5 py-3.5">Mahasiswa Pengusul</th>
                            <th class="px-5 py-3.5 text-right">Anggaran</th>
                            <th class="px-5 py-3.5 text-center">Status Pembina</th>
                            <th class="px-5 py-3.5 text-center">Status Kemahasiswaan</th>
                            <th class="px-5 py-3.5 text-center">Status Akhir</th>
                            <th class="px-5 py-3.5 text-center w-32">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($ukm->ajuanKegiatans as $index => $ajuan)
                            <tr class="hover:bg-indigo-50/20 transition">
                                <td class="px-5 py-4 text-center font-bold text-gray-400">
                                    {{ $index + 1 }}
                                </td>
                                <td class="px-5 py-4">
                                    <div class="font-bold text-gray-900 text-sm">
                                        {{ $ajuan->nama_kegiatan }}
                                    </div>
                                    <div class="text-[11px] text-gray-400 mt-0.5">
                                        Diajukan: {{ $ajuan->created_at ? $ajuan->created_at->isoFormat('D MMMM Y') : '-' }}
                                        @if($ajuan->revisi_count > 0)
                                            • <span class="text-amber-600 font-bold">Revisi ke-{{ $ajuan->revisi_count }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-gray-700">
                                    {{ \Carbon\Carbon::parse($ajuan->tanggal_kegiatan)->isoFormat('D MMMM Y') }}
                                </td>
                                <td class="px-5 py-4">
                                    <div class="font-semibold text-gray-800">{{ $ajuan->user->name ?? '-' }}</div>
                                    <div class="text-[11px] text-gray-400">NIM: {{ $ajuan->user->nim_nip ?? '-' }}</div>
                                </td>
                                <td class="px-5 py-4 text-right font-mono font-bold text-indigo-700 whitespace-nowrap">
                                    Rp {{ number_format($ajuan->total_anggaran, 0, ',', '.') }}
                                </td>
                                <td class="px-5 py-4 text-center whitespace-nowrap">
                                    @if($ajuan->status_pembina === 'disetujui')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                            ✓ Disetujui
                                        </span>
                                    @elseif($ajuan->status_pembina === 'revisi')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                                            ⚠️ Perlu Revisi
                                        </span>
                                    @elseif($ajuan->status_pembina === 'ditolak')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800">
                                            ✕ Ditolak
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                                            ⏳ Belum
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-center whitespace-nowrap">
                                    @if($ajuan->status_kemahasiswaan === 'disetujui')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                            ✓ Disetujui
                                        </span>
                                    @elseif($ajuan->status_kemahasiswaan === 'revisi')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                                            ⚠️ Perlu Revisi
                                        </span>
                                    @elseif($ajuan->status_kemahasiswaan === 'ditolak')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800">
                                            ✕ Ditolak
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                                            ⏳ Belum
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-center whitespace-nowrap">
                                    @if($ajuan->status_akhir === 'disetujui')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black bg-emerald-600 text-white shadow-sm">
                                            Selesai Disetujui
                                        </span>
                                    @elseif($ajuan->status_akhir === 'ditolak')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-rose-600 text-white shadow-sm">
                                            Ditolak
                                        </span>
                                    @elseif($ajuan->status_akhir === 'revisi_kemahasiswaan')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-600 text-white shadow-sm">
                                            Revisi Kemahasiswaan
                                        </span>
                                    @elseif($ajuan->status_akhir === 'revisi_pembina')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-500 text-white shadow-sm">
                                            Revisi Pembina
                                        </span>
                                    @elseif($ajuan->status_akhir === 'menunggu_kemahasiswaan')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                                            Di Meja Kemahasiswaan
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                                            Menunggu Pembina
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <a href="{{ route('kemahasiswaan.show', $ajuan->id) }}" class="inline-flex items-center px-2.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-bold shadow-sm transition gap-1">
                                            <span>Periksa</span>
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
                                <td colspan="9" class="px-6 py-12 text-center text-gray-400">
                                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    <p class="font-medium text-gray-600">UKM ini belum memiliki riwayat pengajuan kegiatan.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
