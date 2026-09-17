<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-xs font-bold px-2.5 py-0.5 rounded-full bg-indigo-100 text-indigo-800">
                        {{ $ajuan->ukm->nama_ukm }}
                    </span>
                    <span class="text-xs font-bold px-2.5 py-0.5 rounded-full bg-purple-100 text-purple-800">
                        {{ $ajuan->jenis_kegiatan ?? 'Lainnya' }}
                    </span>
                    <span class="text-xs text-gray-400">• Tanggal: {{ $ajuan->tanggal_kegiatan->isoFormat('D MMMM Y') }}</span>
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
                @if($ajuan->isBisaDieditMahasiswa())
                    <a href="{{ route('mahasiswa.edit', $ajuan->id) }}" class="inline-flex items-center px-4 py-2 {{ $ajuan->status_akhir === 'ditolak' ? 'bg-rose-600 hover:bg-rose-700 shadow-rose-200' : ($ajuan->isPerluRevisi() ? 'bg-amber-600 hover:bg-amber-700 shadow-amber-200' : 'bg-indigo-600 hover:bg-indigo-700 shadow-indigo-200') }} text-white font-bold rounded-xl text-xs transition gap-1.5 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        <span>{{ $ajuan->status_akhir === 'ditolak' ? 'Perbaiki Usulan (Ditolak)' : ($ajuan->isPerluRevisi() ? 'Perbaiki Usulan (Revisi)' : 'Edit Detail Usulan') }}</span>
                    </a>
                @endif
                @if($ajuan->isSiapLpj() || $ajuan->isRevisiLpj())
                    <a href="{{ route('mahasiswa.lpj', $ajuan->id) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-xs transition gap-1.5 shadow-sm shadow-indigo-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span>{{ $ajuan->isRevisiLpj() ? 'Perbaiki LPJ' : 'Isi Laporan LPJ' }}</span>
                    </a>
                @elseif($ajuan->isSudahDilaksanakan() || $ajuan->isMenungguVerifikasiLpj())
                    <a href="{{ route('mahasiswa.lpj', $ajuan->id) }}" class="inline-flex items-center px-4 py-2 bg-purple-50 hover:bg-purple-100 text-purple-700 font-bold rounded-xl text-xs transition gap-1.5 border border-purple-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        <span>Lihat / Ubah LPJ</span>
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

        @if($ajuan->isSiapLpj())
            <!-- Banner Siap LPJ (Waktunya Isi LPJ) -->
            <div class="bg-gradient-to-r from-amber-500 to-amber-600 rounded-2xl p-6 text-white shadow-lg flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center font-black text-2xl shrink-0">
                        📢
                    </div>
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider bg-white/25 px-2.5 py-0.5 rounded-full">Tahap Pelaporan Pasca-Kegiatan</span>
                        <h4 class="font-black text-lg mt-1">Kegiatan Telah Lewat Tanggal Pelaksanaan — Waktunya Isi LPJ</h4>
                        <p class="text-xs text-amber-100 mt-0.5 max-w-xl">
                            Usulan ini telah disetujui dan pelaksanaan telah selesai pada {{ $ajuan->tanggal_kegiatan->isoFormat('D MMMM Y') }}. Silakan segera mengisi Laporan Pertanggungjawaban (LPJ) dan mengunggah dokumentasi kegiatan.
                        </p>
                    </div>
                </div>
                <div class="shrink-0">
                    <a href="{{ route('mahasiswa.lpj', $ajuan->id) }}" class="inline-flex items-center px-5 py-3 bg-white text-amber-900 hover:bg-amber-50 rounded-xl text-xs font-black shadow-md transition gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span>Isi Laporan LPJ Sekarang</span>
                    </a>
                </div>
            </div>
        @elseif($ajuan->isRevisiLpj())
            <!-- Banner Revisi LPJ -->
            <div class="bg-rose-50 border-2 border-rose-400 rounded-2xl p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl bg-rose-200 text-rose-900 flex items-center justify-center font-bold text-lg shrink-0">
                        ⚠️
                    </div>
                    <div>
                        <h4 class="font-bold text-rose-900 text-base">Laporan Pertanggungjawaban (LPJ) Memerlukan Perbaikan</h4>
                        <p class="text-xs text-rose-800 mt-0.5">Catatan Kemahasiswaan:</p>
                        <div class="bg-white/80 p-3 rounded-xl border border-rose-200 text-rose-900 mt-2 text-xs italic">
                            "{{ $ajuan->lpj->catatan_verifikasi_lpj ?? 'Mohon periksa dan lengkapi berkas LPJ Anda.' }}"
                        </div>
                    </div>
                </div>
                <div class="shrink-0">
                    <a href="{{ route('mahasiswa.lpj', $ajuan->id) }}" class="inline-flex items-center px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold shadow-md transition gap-2">
                        Perbaiki Laporan LPJ
                    </a>
                </div>
            </div>
        @elseif($ajuan->isMenungguVerifikasiLpj())
            <!-- Banner Menunggu Verifikasi LPJ -->
            <div class="bg-purple-50 border-2 border-purple-300 rounded-2xl p-6 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-purple-200 text-purple-900 flex items-center justify-center font-black text-2xl shrink-0">
                    ⏳
                </div>
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-purple-700 bg-purple-100 px-2.5 py-0.5 rounded-full">Menunggu Verifikasi LPJ</span>
                    <h4 class="font-bold text-purple-900 text-base mt-1">Laporan Pertanggungjawaban (LPJ) Telah Dikirim</h4>
                    <p class="text-xs text-purple-700 mt-0.5">
                        Berkas laporan dan data SIMKATMAWA Anda saat ini sedang diperiksa oleh Biro Kemahasiswaan. Status kegiatan akan diperbarui setelah verifikasi selesai.
                    </p>
                </div>
            </div>
        @elseif($ajuan->isSudahDilaksanakan())
            <!-- Banner Sudah Dilaksanakan -->
            <div class="bg-emerald-50 border-2 border-emerald-300 rounded-2xl p-6 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-emerald-200 text-emerald-900 flex items-center justify-center font-black text-2xl shrink-0">
                    ✓
                </div>
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-700 bg-emerald-100 px-2.5 py-0.5 rounded-full">Selesai &amp; Disahkan</span>
                    <h4 class="font-bold text-emerald-900 text-base mt-1">Kegiatan Selesai Dilaksanakan &amp; LPJ Telah Disetujui</h4>
                    <p class="text-xs text-emerald-700 mt-0.5">
                        Laporan pertanggungjawaban kegiatan ini telah diverifikasi dan disetujui secara resmi oleh Biro Kemahasiswaan.
                    </p>
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
                            <th class="px-3 py-3.5 text-center w-10">No</th>
                            <th class="px-4 py-3.5 text-left">Nama Akun</th>
                            <th class="px-4 py-3.5 text-left">Nama Barang</th>
                            <th class="px-4 py-3.5 text-right">Harga Satuan</th>
                            <th class="px-3 py-3.5 text-center">Jumlah</th>
                            <th class="px-3 py-3.5 text-center">Satuan</th>
                            <th class="px-3 py-3.5 text-center">Durasi</th>
                            <th class="px-3 py-3.5 text-center">Sat. Durasi</th>
                            <th class="px-4 py-3.5 text-right">Total</th>
                            <th class="px-4 py-3.5 text-center">Keterangan</th>
                            <th class="px-4 py-3.5 text-left">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @foreach($ajuan->details as $index => $item)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="px-3 py-3 text-center font-bold text-gray-500">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 font-semibold text-gray-800">{{ $item->nama_akun }}</td>
                                <td class="px-4 py-3 text-gray-900 font-medium">{{ $item->nama_barang }}</td>
                                <td class="px-4 py-3 text-right font-mono text-gray-700">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                                <td class="px-3 py-3 text-center font-mono font-semibold">{{ $item->jumlah }}</td>
                                <td class="px-3 py-3 text-center font-medium text-gray-700">{{ $item->satuan ?? '-' }}</td>
                                <td class="px-3 py-3 text-center font-mono text-gray-700">{{ (float)$item->durasi == (int)$item->durasi ? (int)$item->durasi : $item->durasi }}</td>
                                <td class="px-3 py-3 text-center font-medium text-gray-700">{{ $item->satuan_durasi ?? 'Hari' }}</td>
                                <td class="px-4 py-3 text-right font-mono font-bold text-indigo-700">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-center whitespace-nowrap">
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
                                <td class="px-4 py-3 text-xs text-gray-600">
                                    {{ $item->catatan ?? '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-slate-50 border-t-2 border-gray-200 font-bold text-sm">
                        <tr>
                            <td colspan="8" class="px-4 py-3.5 text-right uppercase tracking-wider text-gray-600">
                                Total Keseluruhan (RAB):
                            </td>
                            <td class="px-4 py-3.5 text-right text-base text-indigo-700 font-mono font-extrabold">
                                Rp {{ number_format($ajuan->total_anggaran, 0, ',', '.') }}
                            </td>
                            <td colspan="2" class="px-4 py-3.5 text-xs text-gray-500 font-normal italic">
                                Total {{ $ajuan->details->count() }} item kebutuhan
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        @if($ajuan->lpj)
            <!-- CARD LAPORAN PERTANGGUNGJAWABAN (LPJ) & DATA SIMKATMAWA -->
            <div class="bg-white rounded-2xl shadow-sm border-2 border-indigo-200 overflow-hidden">
                <div class="p-6 border-b border-indigo-100 bg-gradient-to-r from-indigo-50/70 to-white flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold uppercase tracking-wider text-indigo-700 bg-indigo-100 px-2.5 py-0.5 rounded-md">Laporan Pasca-Kegiatan</span>
                            <span class="text-xs text-gray-500">• Dikirim {{ $ajuan->lpj->submitted_at ? $ajuan->lpj->submitted_at->isoFormat('D MMMM Y, HH:mm') : '-' }}</span>
                        </div>
                        <h3 class="font-bold text-gray-900 text-lg mt-1">Laporan Pertanggungjawaban (LPJ) & Data SIMKATMAWA</h3>
                    </div>
                    <div>
                        @if($ajuan->status_akhir === 'sudah_dilaksanakan')
                            <span class="px-3 py-1.5 text-xs font-bold rounded-xl bg-emerald-100 text-emerald-800 border border-emerald-300 flex items-center gap-1.5">
                                <span>✓ LPJ Telah Disetujui Kemahasiswaan</span>
                            </span>
                        @elseif($ajuan->status_akhir === 'revisi_lpj')
                            <span class="px-3 py-1.5 text-xs font-bold rounded-xl bg-rose-100 text-rose-800 border border-rose-300 flex items-center gap-1.5">
                                <span>⚠️ LPJ Memerlukan Perbaikan</span>
                            </span>
                        @else
                            <span class="px-3 py-1.5 text-xs font-bold rounded-xl bg-purple-100 text-purple-800 border border-purple-300 flex items-center gap-1.5">
                                <span>⏳ Sedang Diverifikasi Kemahasiswaan</span>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="p-6 space-y-6">
                    <!-- KPI Ringkasan Capaian -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="bg-slate-50 rounded-xl p-4 border border-slate-200">
                            <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider block">Hasil / Capaian Prestasi</span>
                            @if($ajuan->lpj->prestasies && $ajuan->lpj->prestasies->count() > 0)
                                <span class="text-sm font-black text-indigo-700 mt-1 block flex items-center gap-1">
                                    <span>🏆 {{ $ajuan->lpj->prestasies->count() }} Kejuaraan / Medali</span>
                                </span>
                            @else
                                <span class="text-sm font-bold text-gray-900 mt-1 block">{{ $ajuan->lpj->prestasi ?? 'Peserta / Partisipan' }}</span>
                            @endif
                        </div>
                        <div class="bg-slate-50 rounded-xl p-4 border border-slate-200">
                            <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider block">Jumlah Peserta Hadir</span>
                            <span class="text-sm font-bold text-gray-900 mt-1 block font-mono">{{ $ajuan->lpj->jumlah_peserta ?? 0 }} Mahasiswa</span>
                        </div>
                        <div class="bg-slate-50 rounded-xl p-4 border border-slate-200">
                            <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider block">Realisasi Anggaran Terpakai</span>
                            <span class="text-sm font-bold text-indigo-700 mt-1 block font-mono">Rp {{ number_format($ajuan->lpj->realisasi_anggaran, 0, ',', '.') }}</span>
                        </div>
                        <div class="bg-slate-50 rounded-xl p-4 border border-slate-200">
                            <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider block">Sisa Anggaran / Efisiensi</span>
                            @php
                                $paguDisetujui = $ajuan->details->where('status_keterangan', 'disetujui')->sum('total') ?: $ajuan->total_anggaran;
                                $sisa = $paguDisetujui - (float)$ajuan->lpj->realisasi_anggaran;
                            @endphp
                            <span class="text-sm font-bold {{ $sisa >= 0 ? 'text-emerald-700' : 'text-rose-700' }} mt-1 block font-mono">
                                {{ $sisa >= 0 ? '+ ' : '- ' }}Rp {{ number_format(abs($sisa), 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    @if($ajuan->lpj->ringkasan_kegiatan)
                        <div class="bg-slate-50 rounded-xl p-4 border border-slate-200">
                            <span class="text-xs font-bold text-gray-700 uppercase tracking-wider block mb-1">Evaluasi & Narasi Pelaksanaan:</span>
                            <p class="text-xs text-gray-800 leading-relaxed italic">"{{ $ajuan->lpj->ringkasan_kegiatan }}"</p>
                        </div>
                    @endif

                    <!-- Daftar Perolehan Juara & Capaian Prestasi (Multi-Juara / SIMKATMAWA) -->
                    @if($ajuan->lpj->prestasies && $ajuan->lpj->prestasies->count() > 0)
                        <div>
                            <h4 class="font-bold text-xs uppercase tracking-wider text-gray-700 mb-2.5 flex items-center gap-1.5">
                                <span>🏆 Daftar Perolehan Juara &amp; Capaian Prestasi Lomba</span>
                                <span class="text-[10px] bg-amber-100 text-amber-900 border border-amber-300 px-2 py-0.5 rounded-full font-bold">{{ $ajuan->lpj->prestasies->count() }} Capaian</span>
                            </h4>
                            <div class="overflow-x-auto border border-gray-200 rounded-xl">
                                <table class="min-w-full divide-y divide-gray-200 text-xs">
                                    <thead class="bg-slate-100 text-slate-700 font-bold uppercase">
                                        <tr>
                                            <th class="px-3 py-2.5 text-center w-12">No</th>
                                            <th class="px-4 py-2.5 text-left">Nomor / Cabang Lomba</th>
                                            <th class="px-4 py-2.5 text-left">Peringkat Juara</th>
                                            <th class="px-4 py-2.5 text-left">Tingkat Wilayah</th>
                                            <th class="px-4 py-2.5 text-left">Nama Peraih / Tim</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 bg-white">
                                        @foreach($ajuan->lpj->prestasies as $prIdx => $prItem)
                                            <tr class="hover:bg-amber-50/20">
                                                <td class="px-3 py-2.5 text-center text-gray-400 font-bold">{{ $prIdx + 1 }}</td>
                                                <td class="px-4 py-2.5 font-bold text-gray-900">{{ $prItem->kategori_lomba }}</td>
                                                <td class="px-4 py-2.5">
                                                    @if(str_contains(strtolower($prItem->peringkat_juara), 'juara 1') || str_contains(strtolower($prItem->peringkat_juara), 'emas'))
                                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-100 text-amber-900 font-bold text-xs border border-amber-300">
                                                            🥇 {{ $prItem->peringkat_juara }}
                                                        </span>
                                                    @elseif(str_contains(strtolower($prItem->peringkat_juara), 'juara 2') || str_contains(strtolower($prItem->peringkat_juara), 'perak'))
                                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-200 text-slate-900 font-bold text-xs border border-slate-300">
                                                            🥈 {{ $prItem->peringkat_juara }}
                                                        </span>
                                                    @elseif(str_contains(strtolower($prItem->peringkat_juara), 'juara 3') || str_contains(strtolower($prItem->peringkat_juara), 'perunggu'))
                                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-50 text-amber-800 font-bold text-xs border border-amber-200">
                                                            🥉 {{ $prItem->peringkat_juara }}
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-800 font-semibold text-xs border border-indigo-200">
                                                            🏅 {{ $prItem->peringkat_juara }}
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-2.5">
                                                    <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 text-[11px] font-semibold border border-slate-200">
                                                        {{ $prItem->tingkat ?? 'Nasional' }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-2.5 font-semibold text-gray-800">{{ $prItem->nama_peraih ?: '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    <!-- Daftar Mahasiswa Peserta SIMKATMAWA -->
                    @if($ajuan->lpj->pesertas && $ajuan->lpj->pesertas->count() > 0)
                        <div>
                            <h4 class="font-bold text-xs uppercase tracking-wider text-gray-700 mb-2 flex items-center gap-1.5">
                                <span>👥 Daftar Mahasiswa Peserta / Kontingen (SIMKATMAWA)</span>
                                <span class="text-[10px] bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded-full font-bold">{{ $ajuan->lpj->pesertas->count() }} orang</span>
                            </h4>
                            <div class="overflow-x-auto border border-gray-200 rounded-xl">
                                <table class="min-w-full divide-y divide-gray-200 text-xs">
                                    <thead class="bg-slate-100 text-slate-700 font-bold uppercase">
                                        <tr>
                                            <th class="px-3 py-2.5 text-center w-12">No</th>
                                            <th class="px-4 py-2.5 text-left">Nama Mahasiswa</th>
                                            <th class="px-4 py-2.5 text-left">NIM / NPM</th>
                                            <th class="px-4 py-2.5 text-left">Peran / Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 bg-white">
                                        @foreach($ajuan->lpj->pesertas as $pIdx => $peserta)
                                            <tr class="hover:bg-slate-50">
                                                <td class="px-3 py-2 text-center text-gray-400 font-bold">{{ $pIdx + 1 }}</td>
                                                <td class="px-4 py-2 font-bold text-gray-900">{{ $peserta->nama }}</td>
                                                <td class="px-4 py-2 font-mono text-gray-700">{{ $peserta->npm }}</td>
                                                <td class="px-4 py-2 text-gray-600">{{ $peserta->peran ?? 'Peserta' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    <!-- Berkas Bukti & Dokumentasi Kegiatan -->
                    <div>
                        <h4 class="font-bold text-xs uppercase tracking-wider text-gray-700 mb-2.5">📁 Berkas Bukti & Dokumentasi Kegiatan:</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            <!-- 1. Sertifikat -->
                            <div class="border rounded-xl p-3.5 flex flex-col justify-between {{ $ajuan->lpj->file_sertifikat ? 'bg-indigo-50/40 border-indigo-200' : 'bg-slate-50/60 border-dashed border-gray-200' }}">
                                <div>
                                    <div class="flex items-center gap-2 mb-1.5">
                                        <span class="text-base">📜</span>
                                        <span class="text-xs font-bold text-gray-800">Sertifikat Lomba</span>
                                    </div>
                                    <p class="text-[11px] text-gray-500 truncate">{{ $ajuan->lpj->file_sertifikat_nama ?? 'Belum diunggah' }}</p>
                                </div>
                                <div class="mt-3 pt-2 border-t border-gray-100">
                                    @if($ajuan->lpj->file_sertifikat)
                                        <a href="{{ route('mahasiswa.lpj.download', ['id' => $ajuan->id, 'type' => 'sertifikat']) }}"
                                           class="inline-flex items-center gap-1 text-xs font-bold text-indigo-700 hover:text-indigo-900">
                                            <span>Unduh Berkas</span>
                                            <span>↓</span>
                                        </a>
                                    @else
                                        <span class="text-[11px] text-gray-400 italic">Tidak dilampirkan</span>
                                    @endif
                                </div>
                            </div>

                            <!-- 2. Foto Kegiatan -->
                            <div class="border rounded-xl p-3.5 flex flex-col justify-between {{ $ajuan->lpj->foto_kegiatan ? 'bg-indigo-50/40 border-indigo-200' : 'bg-slate-50/60 border-dashed border-gray-200' }}">
                                <div>
                                    <div class="flex items-center gap-2 mb-1.5">
                                        <span class="text-base">📸</span>
                                        <span class="text-xs font-bold text-gray-800">Foto Kegiatan</span>
                                    </div>
                                    <p class="text-[11px] text-gray-500 truncate">{{ $ajuan->lpj->foto_kegiatan_nama ?? 'Belum diunggah' }}</p>
                                </div>
                                <div class="mt-3 pt-2 border-t border-gray-100">
                                    @if($ajuan->lpj->foto_kegiatan)
                                        <a href="{{ route('mahasiswa.lpj.download', ['id' => $ajuan->id, 'type' => 'foto_kegiatan']) }}"
                                           class="inline-flex items-center gap-1 text-xs font-bold text-indigo-700 hover:text-indigo-900">
                                            <span>Unduh Foto</span>
                                            <span>↓</span>
                                        </a>
                                    @else
                                        <span class="text-[11px] text-gray-400 italic">Tidak dilampirkan</span>
                                    @endif
                                </div>
                            </div>

                            <!-- 3. Foto Penyerahan Piala -->
                            <div class="border rounded-xl p-3.5 flex flex-col justify-between {{ $ajuan->lpj->foto_penyerahan ? 'bg-indigo-50/40 border-indigo-200' : 'bg-slate-50/60 border-dashed border-gray-200' }}">
                                <div>
                                    <div class="flex items-center gap-2 mb-1.5">
                                        <span class="text-base">🏆</span>
                                        <span class="text-xs font-bold text-gray-800">Foto Penyerahan</span>
                                    </div>
                                    <p class="text-[11px] text-gray-500 truncate">{{ $ajuan->lpj->foto_penyerahan_nama ?? 'Belum diunggah' }}</p>
                                </div>
                                <div class="mt-3 pt-2 border-t border-gray-100">
                                    @if($ajuan->lpj->foto_penyerahan)
                                        <a href="{{ route('mahasiswa.lpj.download', ['id' => $ajuan->id, 'type' => 'foto_penyerahan']) }}"
                                           class="inline-flex items-center gap-1 text-xs font-bold text-indigo-700 hover:text-indigo-900">
                                            <span>Unduh Foto</span>
                                            <span>↓</span>
                                        </a>
                                    @else
                                        <span class="text-[11px] text-gray-400 italic">Tidak dilampirkan</span>
                                    @endif
                                </div>
                            </div>

                            <!-- 4. Surat Undangan -->
                            <div class="border rounded-xl p-3.5 flex flex-col justify-between {{ $ajuan->lpj->surat_undangan ? 'bg-indigo-50/40 border-indigo-200' : 'bg-slate-50/60 border-dashed border-gray-200' }}">
                                <div>
                                    <div class="flex items-center gap-2 mb-1.5">
                                        <span class="text-base">✉️</span>
                                        <span class="text-xs font-bold text-gray-800">Surat Undangan</span>
                                    </div>
                                    <p class="text-[11px] text-gray-500 truncate">{{ $ajuan->lpj->surat_undangan_nama ?? 'Belum diunggah' }}</p>
                                </div>
                                <div class="mt-3 pt-2 border-t border-gray-100">
                                    @if($ajuan->lpj->surat_undangan)
                                        <a href="{{ route('mahasiswa.lpj.download', ['id' => $ajuan->id, 'type' => 'surat_undangan']) }}"
                                           class="inline-flex items-center gap-1 text-xs font-bold text-indigo-700 hover:text-indigo-900">
                                            <span>Unduh Surat</span>
                                            <span>↓</span>
                                        </a>
                                    @else
                                        <span class="text-[11px] text-gray-400 italic">Tidak dilampirkan</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($ajuan->lpj->catatan_verifikasi_lpj)
                        <div class="bg-indigo-50/70 border border-indigo-200 rounded-xl p-4">
                            <span class="text-xs font-bold text-indigo-900 uppercase tracking-wider block mb-0.5">Catatan Verifikator Kemahasiswaan:</span>
                            <p class="text-xs text-indigo-950 italic">"{{ $ajuan->lpj->catatan_verifikasi_lpj }}"</p>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Timeline Riwayat Jejak Usulan Kegiatan -->
        <x-timeline-riwayat :riwayats="$ajuan->riwayats" />
    </div>
</x-app-layout>
