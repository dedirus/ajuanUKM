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
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                <div class="p-3 bg-slate-50 rounded-xl">
                    <span class="text-xs text-gray-400 font-semibold uppercase block">Nama Kegiatan</span>
                    <span class="font-bold text-gray-900 mt-1 block">{{ $ajuan->nama_kegiatan }}</span>
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
                            <th class="px-5 py-3.5 text-center w-12">No</th>
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
                                <td class="px-5 py-3.5 text-xs text-gray-600">
                                    {{ $item->catatan ?? '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-slate-50 border-t-2 border-gray-200 font-bold text-sm">
                        <tr>
                            <td colspan="5" class="px-5 py-4 text-right uppercase tracking-wider text-gray-600">
                                Total Jumlah Ajuan:
                            </td>
                            <td class="px-5 py-4 text-right text-base text-indigo-700 font-mono font-black">
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

    </div>
</x-app-layout>
