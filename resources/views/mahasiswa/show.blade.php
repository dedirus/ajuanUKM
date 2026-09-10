<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-xs font-bold px-2.5 py-0.5 rounded-full bg-indigo-100 text-indigo-800">
                        {{ $ajuan->ukm->nama_ukm }}
                    </span>
                    <span class="text-xs text-gray-400">• Diajukan {{ $ajuan->created_at->isoFormat('D MMMM Y, HH:mm') }}</span>
                </div>
                <h2 class="font-black text-2xl text-gray-900 leading-tight">
                    {{ $ajuan->nama_kegiatan }}
                </h2>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('mahasiswa.dashboard') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl text-xs transition">
                    ← Kembali
                </a>
                @if($ajuan->isPerluRevisi())
                    <a href="{{ route('mahasiswa.edit', $ajuan->id) }}" class="inline-flex items-center px-4 py-2 {{ $ajuan->status_akhir === 'ditolak' ? 'bg-rose-600 hover:bg-rose-700 shadow-rose-200' : 'bg-amber-600 hover:bg-amber-700 shadow-amber-200' }} text-white font-bold rounded-xl text-xs transition gap-1.5 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        <span>{{ $ajuan->status_akhir === 'ditolak' ? 'Perbaiki Usulan (Ditolak)' : 'Perbaiki Usulan (Revisi)' }}</span>
                    </a>
                @endif
                <a href="{{ route('mahasiswa.download', $ajuan->id) }}" class="inline-flex items-center px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold rounded-xl text-xs transition gap-1.5 border border-indigo-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Unduh Proposal
                </a>
                <a href="{{ route('mahasiswa.cetak-pengesahan', $ajuan->id) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs transition gap-1.5 shadow-sm shadow-emerald-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Cetak Pengesahan (PDF)
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        @if($ajuan->isPerluRevisi())
            <!-- Banner Peringatan Revisi / Ditolak -->
            <div class="{{ $ajuan->status_akhir === 'ditolak' ? 'bg-rose-50 border-2 border-rose-400' : 'bg-amber-50 border-2 border-amber-400' }} rounded-2xl p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl {{ $ajuan->status_akhir === 'ditolak' ? 'bg-rose-200 text-rose-900' : 'bg-amber-200 text-amber-900' }} flex items-center justify-center font-bold text-lg shrink-0">
                        {{ $ajuan->status_akhir === 'ditolak' ? '✕' : '⚠️' }}
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h4 class="font-bold {{ $ajuan->status_akhir === 'ditolak' ? 'text-rose-900' : 'text-amber-900' }} text-base">
                                {{ $ajuan->status_akhir === 'ditolak' ? 'Usulan Ditolak & Memerlukan Perbaikan' : 'Usulan Memerlukan Perbaikan (Revisi)' }}
                            </h4>
                            <span class="text-xs {{ $ajuan->status_akhir === 'ditolak' ? 'bg-rose-200 text-rose-900' : 'bg-amber-200 text-amber-900' }} font-bold px-2 py-0.5 rounded-full">Revisi ke-{{ $ajuan->revisi_count + 1 }}</span>
                        </div>
                        <p class="text-xs {{ $ajuan->status_akhir === 'ditolak' ? 'text-rose-800' : 'text-amber-800' }} mt-0.5">
                            Sumber: <strong>{{ $ajuan->isRevisiPembina() ? 'Dosen Pembina UKM' : 'Biro Kemahasiswaan Universitas' }}</strong>
                        </p>
                        <div class="bg-white/80 p-3 rounded-xl border {{ $ajuan->status_akhir === 'ditolak' ? 'border-rose-200 text-rose-900' : 'border-amber-200 text-amber-900' }} mt-2 text-xs italic">
                            "{{ $ajuan->catatan_revisi_terakhir ?? ($ajuan->catatan_kemahasiswaan ?: $ajuan->catatan_pembina) }}"
                        </div>
                    </div>
                </div>
                <div class="shrink-0">
                    <a href="{{ route('mahasiswa.edit', $ajuan->id) }}" class="inline-flex items-center px-5 py-2.5 {{ $ajuan->status_akhir === 'ditolak' ? 'bg-rose-600 hover:bg-rose-700 active:bg-rose-800 shadow-rose-200' : 'bg-amber-600 hover:bg-amber-700 active:bg-amber-800 shadow-amber-200' }} text-white rounded-xl text-xs font-bold shadow-md transition gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        Perbaiki Usulan Sekarang
                    </a>
                </div>
            </div>
        @endif

        <!-- Status Verifikasi Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Verifikasi Pembina -->
            <div class="bg-white rounded-2xl p-6 border shadow-sm {{ $ajuan->status_pembina === 'disetujui' ? 'border-emerald-200 bg-emerald-50/20' : ($ajuan->status_pembina === 'ditolak' ? 'border-rose-200 bg-rose-50/20' : ($ajuan->status_pembina === 'revisi' ? 'border-amber-300 bg-amber-50/30' : 'border-gray-200 bg-slate-50/20')) }}">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full {{ $ajuan->status_pembina === 'disetujui' ? 'bg-emerald-500' : ($ajuan->status_pembina === 'ditolak' ? 'bg-rose-500' : ($ajuan->status_pembina === 'revisi' ? 'bg-amber-500' : 'bg-gray-400')) }}"></span>
                        <h4 class="font-bold text-gray-800 text-sm">Verifikasi Pembina UKM</h4>
                    </div>
                    @if($ajuan->status_pembina === 'disetujui')
                        <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-emerald-100 text-emerald-800">Disetujui</span>
                    @elseif($ajuan->status_pembina === 'revisi')
                        <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-amber-100 text-amber-800">Perlu Revisi</span>
                    @elseif($ajuan->status_pembina === 'ditolak')
                        <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-rose-100 text-rose-800">Ditolak</span>
                    @else
                        <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-slate-100 text-slate-700">Belum Disetujui</span>
                    @endif
                </div>

                <div class="text-xs text-gray-600 space-y-1">
                    <div>Pembina: <span class="font-semibold text-gray-900">{{ $ajuan->ukm->pembina_nama ?? 'Dosen Pembina' }}</span></div>
                    @if($ajuan->verified_pembina_at)
                        <div class="text-gray-400">Diverifikasi pada: {{ $ajuan->verified_pembina_at->isoFormat('D MMMM Y, HH:mm') }}</div>
                    @endif
                </div>

                <div class="mt-3 pt-3 border-t border-gray-100">
                    <span class="text-xs font-semibold text-gray-500 block">Catatan Pembina:</span>
                    <p class="text-xs text-gray-800 italic mt-0.5">{{ $ajuan->catatan_pembina ?? 'Belum ada catatan dari Pembina.' }}</p>
                </div>
            </div>

            <!-- Verifikasi Kemahasiswaan -->
            <div class="bg-white rounded-2xl p-6 border shadow-sm {{ $ajuan->status_kemahasiswaan === 'disetujui' ? 'border-emerald-200 bg-emerald-50/20' : ($ajuan->status_kemahasiswaan === 'ditolak' ? 'border-rose-200 bg-rose-50/20' : ($ajuan->status_kemahasiswaan === 'revisi' ? 'border-amber-300 bg-amber-50/30' : 'border-gray-200 bg-slate-50/20')) }}">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full {{ $ajuan->status_kemahasiswaan === 'disetujui' ? 'bg-emerald-500' : ($ajuan->status_kemahasiswaan === 'ditolak' ? 'bg-rose-500' : ($ajuan->status_kemahasiswaan === 'revisi' ? 'bg-amber-500' : 'bg-gray-400')) }}"></span>
                        <h4 class="font-bold text-gray-800 text-sm">Verifikasi Kemahasiswaan Universitas</h4>
                    </div>
                    @if($ajuan->status_kemahasiswaan === 'disetujui')
                        <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-emerald-100 text-emerald-800">Disetujui</span>
                    @elseif($ajuan->status_kemahasiswaan === 'revisi')
                        <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-amber-100 text-amber-800">Perlu Revisi</span>
                    @elseif($ajuan->status_kemahasiswaan === 'ditolak')
                        <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-rose-100 text-rose-800">Ditolak</span>
                    @else
                        <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-slate-100 text-slate-700">Belum Disetujui</span>
                    @endif
                </div>

                <div class="text-xs text-gray-600 space-y-1">
                    <div>Verifikator: <span class="font-semibold text-gray-900">{{ $ajuan->kemahasiswaanVerifier->name ?? 'Biro Kemahasiswaan' }}</span></div>
                    @if($ajuan->verified_kemahasiswaan_at)
                        <div class="text-gray-400">Diverifikasi pada: {{ $ajuan->verified_kemahasiswaan_at->isoFormat('D MMMM Y, HH:mm') }}</div>
                    @endif
                </div>

                <div class="mt-3 pt-3 border-t border-gray-100">
                    <span class="text-xs font-semibold text-gray-500 block">Catatan Kemahasiswaan:</span>
                    <p class="text-xs text-gray-800 italic mt-0.5">{{ $ajuan->catatan_kemahasiswaan ?? 'Belum ada catatan dari Kemahasiswaan.' }}</p>
                </div>
            </div>
        </div>

        <!-- Tabel Detail Kebutuhan Kegiatan -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <h3 class="font-bold text-gray-900 text-lg">Rincian Anggaran Biaya (RAB) Kegiatan</h3>
                    <p class="text-xs text-gray-500">Tabel kebutuhan kegiatan dan status persetujuan dari Kemahasiswaan.</p>
                </div>
                <div class="text-right">
                    <span class="text-xs text-gray-500 block">Total Pengajuan Anggaran</span>
                    <span class="text-xl font-black text-indigo-700 font-mono">
                        Rp {{ number_format($ajuan->total_anggaran, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-gray-600 text-xs uppercase font-bold tracking-wider">
                        <tr>
                            <th class="px-5 py-3.5 text-center w-14">No</th>
                            <th class="px-5 py-3.5 text-left">Nama Akun</th>
                            <th class="px-5 py-3.5 text-left">Nama Barang</th>
                            <th class="px-5 py-3.5 text-right">Harga Satuan</th>
                            <th class="px-5 py-3.5 text-center">Jumlah</th>
                            <th class="px-5 py-3.5 text-right">Total</th>
                            <th class="px-5 py-3.5 text-center">Keterangan</th>
                            <th class="px-5 py-3.5 text-left">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @foreach($ajuan->details as $index => $item)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="px-5 py-3.5 text-center font-bold text-gray-500">{{ $index + 1 }}</td>
                                <td class="px-5 py-3.5 font-semibold text-gray-800">{{ $item->nama_akun }}</td>
                                <td class="px-5 py-3.5 text-gray-900 font-medium">{{ $item->nama_barang }}</td>
                                <td class="px-5 py-3.5 text-right font-mono text-gray-700">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                                <td class="px-5 py-3.5 text-center font-mono font-semibold">{{ $item->jumlah }}</td>
                                <td class="px-5 py-3.5 text-right font-mono font-bold text-indigo-700">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                                <td class="px-5 py-3.5 text-center whitespace-nowrap">
                                    @if($item->status_keterangan === 'disetujui')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                            ✓ Disetujui
                                        </span>
                                    @elseif($item->status_keterangan === 'tidak_disetujui')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800">
                                            ✗ Tidak Disetujui
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                            ⏳ Menunggu
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-xs text-gray-600">
                                    {{ $item->catatan ?? '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-slate-50 border-t-2 border-gray-200 font-bold text-sm">
                        <tr>
                            <td colspan="5" class="px-5 py-4 text-right uppercase tracking-wider text-gray-600">
                                Total Keseluruhan (RAB):
                            </td>
                            <td class="px-5 py-4 text-right text-base text-indigo-700 font-mono font-extrabold">
                                Rp {{ number_format($ajuan->total_anggaran, 0, ',', '.') }}
                            </td>
                            <td colspan="2" class="px-5 py-4 text-xs text-gray-500 font-normal italic">
                                Total {{ $ajuan->details->count() }} item kebutuhan
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
