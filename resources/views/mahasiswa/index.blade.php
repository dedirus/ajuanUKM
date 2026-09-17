<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    Pengajuan Kegiatan UKM
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">
                    Mewakili: <span class="font-semibold text-indigo-700">{{ $ukm->nama_ukm ?? 'UKM Belum Diatur' }}</span> (Kode: {{ $ukm->kode ?? '-' }})
                </p>
            </div>
            <div>
                <a href="{{ route('mahasiswa.create') }}" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-medium rounded-xl text-sm shadow-sm shadow-indigo-200 transition gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Ajukan Kegiatan Baru
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        <!-- Info Card UKM -->
        <div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 rounded-2xl p-6 text-white shadow-lg">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <span class="inline-block px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-xs font-semibold tracking-wide uppercase mb-2">
                        Unit Kegiatan Mahasiswa
                    </span>
                    <h3 class="text-2xl font-black tracking-tight">{{ $ukm->nama_ukm }}</h3>
                    <p class="text-blue-100 text-sm mt-1 max-w-2xl">{{ $ukm->deskripsi }}</p>
                </div>
                <div class="bg-white/10 backdrop-blur-md rounded-xl p-4 border border-white/20 text-sm">
                    <div class="text-blue-200 text-xs uppercase font-medium">Dosen Pembina UKM</div>
                    <div class="font-bold text-white mt-0.5">{{ $ukm->pembina_nama ?? 'Belum ditentukan' }}</div>
                    <div class="text-xs text-blue-200 mt-2">Perwakilan Login: <span class="font-semibold text-white">{{ $user->name }}</span></div>
                </div>
            </div>
        </div>

        <!-- Tabel Riwayat Pengajuan -->
        <div class="bg-white shadow-sm rounded-2xl border border-gray-200 overflow-hidden">
            <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h4 class="font-bold text-gray-800 text-lg">Daftar Usulan Kegiatan</h4>
                    <p class="text-xs text-gray-500">Seluruh ajuan kegiatan mahasiswa yang telah diajukan ke Pembina dan Kemahasiswaan.</p>
                </div>
                <span class="text-xs font-semibold text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
                    Total: {{ $ajuans->total() }} Kegiatan
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-gray-600 text-xs uppercase font-bold tracking-wider">
                        <tr>
                            <th class="px-6 py-3.5 text-left">No</th>
                            <th class="px-6 py-3.5 text-left">Nama Kegiatan</th>
                            <th class="px-6 py-3.5 text-left">Tanggal Kegiatan</th>
                            <th class="px-6 py-3.5 text-left">Total Ajuan</th>
                            <th class="px-6 py-3.5 text-center">Verifikasi Pembina</th>
                            <th class="px-6 py-3.5 text-center">Verifikasi Kemahasiswaan</th>
                            <th class="px-6 py-3.5 text-center">Status Akhir</th>
                            <th class="px-6 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($ajuans as $index => $ajuan)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="px-6 py-4 font-semibold text-gray-500">
                                    {{ $ajuans->firstItem() + $index }}
                                </td>
                                <td class="px-6 py-4 font-bold text-gray-900">
                                    <a href="{{ route('mahasiswa.show', $ajuan->id) }}" class="hover:text-indigo-600 hover:underline">
                                        {{ $ajuan->nama_kegiatan }}
                                    </a>
                                    <div class="text-xs text-gray-400 font-normal mt-0.5 flex items-center gap-1.5">
                                        <span class="font-semibold text-purple-700 bg-purple-50 px-1.5 py-0.5 rounded border border-purple-200 text-[10px]">{{ $ajuan->jenis_kegiatan ?? 'Lainnya' }}</span>
                                        <span>• {{ $ajuan->details->count() }} rincian item</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-700 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($ajuan->tanggal_kegiatan)->isoFormat('D MMMM Y') }}
                                </td>
                                <td class="px-6 py-4 font-bold text-indigo-700 whitespace-nowrap">
                                    Rp {{ number_format($ajuan->total_anggaran, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    @if($ajuan->status_pembina === 'disetujui')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                            ✓ Disetujui
                                        </span>
                                    @elseif($ajuan->status_pembina === 'revisi')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                                            ⚠️ Perlu Revisi
                                        </span>
                                    @elseif($ajuan->status_pembina === 'ditolak')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-100 text-rose-800">
                                            ✗ Ditolak
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                                            ⏳ Belum Disetujui
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    @if($ajuan->status_kemahasiswaan === 'disetujui')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                            ✓ Disetujui
                                        </span>
                                    @elseif($ajuan->status_kemahasiswaan === 'revisi')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                                            ⚠️ Perlu Revisi
                                        </span>
                                    @elseif($ajuan->status_kemahasiswaan === 'ditolak')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-100 text-rose-800">
                                            ✗ Ditolak
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                                            ⏳ Belum Disetujui
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    @if($ajuan->status_akhir === 'sudah_dilaksanakan')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-600 text-white shadow-sm">
                                            ✓ Sudah Dilaksanakan
                                        </span>
                                    @elseif($ajuan->status_akhir === 'menunggu_verifikasi_lpj')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-purple-600 text-white shadow-sm">
                                            ⏳ Verifikasi LPJ
                                        </span>
                                    @elseif($ajuan->status_akhir === 'revisi_lpj')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-rose-600 text-white shadow-sm">
                                            ⚠️ Perlu Revisi LPJ
                                        </span>
                                    @elseif($ajuan->status_akhir === 'disetujui')
                                        @if($ajuan->tanggal_kegiatan->lessThanOrEqualTo(today()))
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-500 text-white shadow-sm">
                                                📢 Menunggu LPJ
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-500 text-white shadow-sm">
                                                Selesai Disetujui
                                            </span>
                                        @endif
                                    @elseif($ajuan->status_akhir === 'revisi_pembina')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-500 text-white shadow-sm">
                                            Perlu Revisi Pembina
                                        </span>
                                    @elseif($ajuan->status_akhir === 'revisi_kemahasiswaan')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-600 text-white shadow-sm">
                                            Perlu Revisi Kemahasiswaan
                                        </span>
                                    @elseif($ajuan->status_akhir === 'ditolak')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-rose-500 text-white shadow-sm">
                                            Ditolak
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
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end space-x-2">
                                        @if($ajuan->isSiapLpj() || $ajuan->isRevisiLpj())
                                            <a href="{{ route('mahasiswa.lpj', $ajuan->id) }}" class="inline-flex items-center px-2.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-bold shadow-sm transition gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                <span>{{ $ajuan->isRevisiLpj() ? 'Revisi LPJ' : 'Lapor LPJ' }}</span>
                                            </a>
                                        @endif
                                        @if($ajuan->isBisaDieditMahasiswa())
                                            <a href="{{ route('mahasiswa.edit', $ajuan->id) }}" class="inline-flex items-center px-2.5 py-1.5 {{ $ajuan->isPerluRevisi() ? ($ajuan->status_akhir === 'ditolak' ? 'bg-rose-600 hover:bg-rose-700' : 'bg-amber-500 hover:bg-amber-600') : 'bg-indigo-600 hover:bg-indigo-700' }} text-white rounded-lg text-xs font-bold shadow-sm transition gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                <span>{{ $ajuan->status_akhir === 'ditolak' ? 'Perbaiki' : ($ajuan->isPerluRevisi() ? 'Perbaiki' : 'Edit') }}</span>
                                            </a>
                                        @endif
                                        <a href="{{ route('mahasiswa.show', $ajuan->id) }}" class="inline-flex items-center px-3 py-1.5 bg-gray-100 hover:bg-indigo-50 hover:text-indigo-600 text-gray-700 rounded-lg text-xs font-semibold transition gap-1">
                                            <span>Detail</span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                        </a>
                                        <a href="{{ route('mahasiswa.cetak-pengesahan', $ajuan->id) }}" target="_blank" title="Cetak Lembar Pengesahan PDF" class="inline-flex items-center px-2 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-lg text-xs font-bold transition gap-1 border border-emerald-200">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                            <span>PDF</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-gray-400">
                                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    <p class="font-medium text-gray-600">Belum ada pengajuan kegiatan.</p>
                                    <p class="text-xs text-gray-400 mt-1">Silakan klik tombol "Ajukan Kegiatan Baru" untuk memulai usulan.</p>
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
