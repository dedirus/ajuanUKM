<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-xs font-bold px-2.5 py-0.5 rounded-full bg-purple-100 text-purple-800">
                        {{ $ajuan->ukm->nama_ukm }}
                    </span>
                    <span class="text-xs text-gray-400">• Pengusul: {{ $ajuan->user->name }}</span>
                </div>
                <h2 class="font-black text-2xl text-gray-900 leading-tight">
                    Verifikasi Usulan: {{ $ajuan->nama_kegiatan }}
                </h2>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('pembina.dashboard') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl text-xs transition">
                    ← Kembali ke Tabel Ajuan
                </a>
                <a href="{{ route('pembina.download', $ajuan->id) }}" class="inline-flex items-center px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold rounded-xl text-xs transition gap-1.5 border border-indigo-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Unduh Dokumen Proposal / ToR
                </a>
                <a href="{{ route('pembina.cetak-pengesahan', $ajuan->id) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs transition gap-1.5 shadow-sm shadow-emerald-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Cetak Pengesahan (PDF)
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        <!-- Info Ringkas Kegiatan -->
        <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-4 text-sm">
                <div class="p-3 bg-slate-50 rounded-xl">
                    <span class="text-xs text-gray-400 font-semibold uppercase block">Nama Kegiatan</span>
                    <span class="font-bold text-gray-900 mt-1 block truncate">{{ $ajuan->nama_kegiatan }}</span>
                </div>
                <div class="p-3 bg-slate-50 rounded-xl">
                    <span class="text-xs text-gray-400 font-semibold uppercase block">Jenis Kegiatan</span>
                    <span class="font-bold text-purple-700 mt-1 block">{{ $ajuan->jenis_kegiatan ?? 'Lainnya' }}</span>
                </div>
                <div class="p-3 bg-slate-50 rounded-xl">
                    <span class="text-xs text-gray-400 font-semibold uppercase block">Tanggal Pelaksanaan</span>
                    <span class="font-bold text-gray-900 mt-1 block">{{ \Carbon\Carbon::parse($ajuan->tanggal_kegiatan)->isoFormat('D MMMM Y') }}</span>
                </div>
                <div class="p-3 bg-slate-50 rounded-xl">
                    <span class="text-xs text-gray-400 font-semibold uppercase block">Total Usulan Anggaran</span>
                    <span class="font-extrabold text-indigo-700 font-mono text-base mt-1 block">Rp {{ number_format($ajuan->total_anggaran, 0, ',', '.') }}</span>
                </div>
                <div class="p-3 bg-slate-50 rounded-xl">
                    <span class="text-xs text-gray-400 font-semibold uppercase block">Status Saat Ini</span>
                    <div class="mt-1">
                        @if($ajuan->status_pembina === 'disetujui')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">✓ Disetujui Pembina</span>
                        @elseif($ajuan->status_pembina === 'revisi')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">⚠️ Perlu Revisi</span>
                        @elseif($ajuan->status_pembina === 'ditolak')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800">✗ Ditolak Pembina</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-700">⏳ Belum Disetujui</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Detail Ajuan Kegiatan -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-gray-900 text-lg">Tabel Detail Ajuan Kegiatan (RAB)</h3>
                    <p class="text-xs text-gray-500">Rincian item kebutuhan barang dan anggaran yang diajukan mahasiswa.</p>
                </div>
                <span class="text-xs font-semibold px-3 py-1 bg-slate-100 text-gray-700 rounded-lg">
                    {{ $ajuan->details->count() }} Item Rincian
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-slate-50 text-gray-600 text-xs uppercase font-bold tracking-wider">
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
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                            ✓ Disetujui
                                        </span>
                                    @elseif($item->status_keterangan === 'tidak_disetujui')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800">
                                            ✗ Tidak Disetujui
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                            ⏳ Belum Diverifikasi
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
                            <td colspan="8" class="px-4 py-4 text-right uppercase tracking-wider text-gray-600">
                                Total Jumlah Ajuan:
                            </td>
                            <td class="px-4 py-4 text-right text-base text-indigo-700 font-mono font-black">
                                Rp {{ number_format($ajuan->total_anggaran, 0, ',', '.') }}
                            </td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Form Tindakan Verifikasi Pembina -->
        <div class="bg-white rounded-2xl p-6 sm:p-8 border border-gray-200 shadow-sm">
            <h3 class="font-bold text-gray-900 text-lg flex items-center gap-2 mb-1">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Verifikasi Usulan Kegiatan oleh Pembina
            </h3>
            <p class="text-xs text-gray-500 mb-6">
                Setelah memeriksa rincian kegiatan dan tabel detail ajuan di atas, berikan catatan dan tekan tombol <strong>Disetujui</strong> untuk meneruskan usulan ke Kemahasiswaan.
            </p>

            <form action="{{ route('pembina.verify', $ajuan->id) }}" method="POST">
                @csrf

                <div class="mb-5">
                    <label for="catatan" class="block text-sm font-semibold text-gray-700 mb-1">
                        Catatan / Rekomendasi Pembina:
                    </label>
                    <textarea id="catatan" name="catatan" rows="3"
                              placeholder="Tuliskan catatan pembina mengenai kegiatan atau persetujuan anggaran..."
                              class="w-full rounded-xl border-gray-300 text-sm focus:ring-purple-500 focus:border-purple-500 shadow-sm">{{ old('catatan', $ajuan->catatan_pembina) }}</textarea>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-4 pt-2">
                    <div class="text-xs text-gray-500">
                        @if($ajuan->verified_pembina_at)
                            <span class="text-emerald-700 font-semibold">Telah diverifikasi pada: {{ $ajuan->verified_pembina_at->isoFormat('D MMMM Y, HH:mm') }}</span>
                        @else
                            <span class="text-amber-600 font-medium">Usulan ini masih menunggu verifikasi Anda.</span>
                        @endif
                    </div>

                    <div class="flex items-center space-x-3">
                        <!-- Tombol Tolak (Opsional jika ada koreksi fatal) -->
                        <button type="submit" name="status" value="ditolak" onclick="return confirm('Apakah Anda yakin ingin menolak usulan kegiatan ini?')"
                                class="px-4 py-2.5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-xl text-xs font-bold transition">
                            ✗ Tolak
                        </button>

                        <!-- Tombol Minta Revisi -->
                        <button type="submit" name="status" value="revisi" onclick="return confirm('Apakah Anda yakin ingin meminta perbaikan/revisi kepada mahasiswa? Pastikan catatan revisi telah diisi.')"
                                class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-white rounded-xl text-xs font-bold shadow-md shadow-amber-200 transition flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            ⚠️ Minta Revisi
                        </button>

                        <!-- Tombol Disetujui Utama -->
                        <button type="submit" name="status" value="disetujui" onclick="return confirm('Apakah Anda yakin menyetujui usulan kegiatan ini?')"
                                class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white rounded-xl text-xs font-extrabold shadow-md shadow-emerald-200 transition flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            ✓ Disetujui (Setujui Usulan)
                        </button>
                    </div>
                </div>
            </form>
        </div>

        @if($ajuan->lpj)
            <!-- CARD LAPORAN PERTANGGUNGJAWABAN (LPJ) & DATA SIMKATMAWA UNTUK PEMBINA -->
            <div class="bg-white rounded-2xl p-6 sm:p-8 border-2 border-indigo-200 shadow-md">
                <div class="border-b border-gray-100 pb-4 mb-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-indigo-700 bg-indigo-100 px-2.5 py-1 rounded-md">
                            Pasca-Pelaksanaan Kegiatan
                        </span>
                        <h3 class="font-extrabold text-xl text-gray-900 mt-2">
                            Laporan Pertanggungjawaban (LPJ) & Data SIMKATMAWA
                        </h3>
                        <p class="text-xs text-gray-500 mt-0.5">
                            Bukti capaian prestasi, nama peserta kontingen SIMKATMAWA, realisasi anggaran, serta 4 berkas dokumentasi kegiatan mahasiswa binaan Anda.
                        </p>
                    </div>
                    <div>
                        @if($ajuan->status_akhir === 'sudah_dilaksanakan')
                            <span class="px-3.5 py-1.5 text-xs font-bold rounded-xl bg-emerald-100 text-emerald-800 border border-emerald-300 flex items-center gap-1.5">
                                <span>✓ LPJ Telah Disahkan Kemahasiswaan</span>
                            </span>
                        @elseif($ajuan->status_akhir === 'revisi_lpj')
                            <span class="px-3.5 py-1.5 text-xs font-bold rounded-xl bg-rose-100 text-rose-800 border border-rose-300 flex items-center gap-1.5">
                                <span>⚠️ Status: Memerlukan Perbaikan LPJ</span>
                            </span>
                        @else
                            <span class="px-3.5 py-1.5 text-xs font-bold rounded-xl bg-purple-100 text-purple-800 border border-purple-300 flex items-center gap-1.5">
                                <span>⏳ Status: Menunggu Verifikasi LPJ</span>
                            </span>
                        @endif
                    </div>
                </div>

                <!-- KPI Ringkasan Capaian LPJ -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
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
                    <div class="bg-slate-50 rounded-xl p-4 border border-slate-200 mb-6">
                        <span class="text-xs font-bold text-gray-700 uppercase tracking-wider block mb-1">Evaluasi & Narasi Pelaksanaan dari Mahasiswa:</span>
                        <p class="text-xs text-gray-800 leading-relaxed italic">"{{ $ajuan->lpj->ringkasan_kegiatan }}"</p>
                    </div>
                @endif

                <!-- Tabel Capaian Prestasi & Medali (Multi-Juara) -->
                @if($ajuan->lpj->prestasies && $ajuan->lpj->prestasies->count() > 0)
                    <div class="mb-6">
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

                <!-- Tabel Mahasiswa Peserta SIMKATMAWA -->
                @if($ajuan->lpj->pesertas && $ajuan->lpj->pesertas->count() > 0)
                    <div class="mb-6">
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
                    <h4 class="font-bold text-xs uppercase tracking-wider text-gray-700 mb-2.5">📁 Berkas Bukti & Dokumentasi Terlampir:</h4>
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
                                    <a href="{{ route('pembina.lpj.download', ['id' => $ajuan->id, 'type' => 'sertifikat']) }}"
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
                                    <a href="{{ route('pembina.lpj.download', ['id' => $ajuan->id, 'type' => 'foto_kegiatan']) }}"
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
                                    <a href="{{ route('pembina.lpj.download', ['id' => $ajuan->id, 'type' => 'foto_penyerahan']) }}"
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
                                    <a href="{{ route('pembina.lpj.download', ['id' => $ajuan->id, 'type' => 'surat_undangan']) }}"
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
                    <div class="bg-indigo-50/70 border border-indigo-200 rounded-xl p-4 mt-6">
                        <span class="text-xs font-bold text-indigo-900 uppercase tracking-wider block mb-0.5">Catatan Verifikator Kemahasiswaan:</span>
                        <p class="text-xs text-indigo-950 italic">"{{ $ajuan->lpj->catatan_verifikasi_lpj }}"</p>
                    </div>
                @endif
            </div>
        @endif

        <!-- Timeline Riwayat Jejak Usulan Kegiatan -->
        <x-timeline-riwayat :riwayats="$ajuan->riwayats" />
    </div>
</x-app-layout>
