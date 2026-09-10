<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-xs font-bold px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-900 border border-amber-200">
                        {{ $ajuan->ukm->nama_ukm }}
                    </span>
                    <span class="text-xs text-gray-400">• Diajukan oleh: <strong class="text-gray-700">{{ $ajuan->user->name }}</strong></span>
                </div>
                <h2 class="font-black text-2xl text-gray-900 leading-tight">
                    Pemeriksaan Ajuan: {{ $ajuan->nama_kegiatan }}
                </h2>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('kemahasiswaan.dashboard') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl text-xs transition">
                    ← Kembali ke Daftar Ajuan
                </a>
                <a href="{{ route('kemahasiswaan.download', $ajuan->id) }}" class="inline-flex items-center px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold rounded-xl text-xs transition gap-1.5 border border-indigo-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Unduh Proposal / ToR
                </a>
                <a href="{{ route('kemahasiswaan.cetak-pengesahan', $ajuan->id) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs transition gap-1.5 shadow-sm shadow-emerald-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Cetak Pengesahan (PDF)
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6" x-data="kemahasiswaanReview()">

        <!-- Status & Verifikasi Pembina Card -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Info Kegiatan -->
            <div class="bg-white rounded-2xl p-5 border border-gray-200 shadow-sm space-y-2 text-xs">
                <div class="font-bold text-gray-800 text-sm border-b pb-2 flex items-center gap-2">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Informasi Kegiatan
                </div>
                <div><span class="text-gray-400">UKM:</span> <span class="font-bold text-gray-800">{{ $ajuan->ukm->nama_ukm }}</span></div>
                <div><span class="text-gray-400">Tanggal Pelaksanaan:</span> <span class="font-bold text-gray-800">{{ \Carbon\Carbon::parse($ajuan->tanggal_kegiatan)->isoFormat('D MMMM Y') }}</span></div>
                <div><span class="text-gray-400">Total Anggaran:</span> <span class="font-black text-indigo-700 font-mono text-sm">Rp {{ number_format($ajuan->total_anggaran, 0, ',', '.') }}</span></div>
                <div><span class="text-gray-400">Dokumen Lampiran:</span> <span class="font-medium text-gray-700">{{ $ajuan->dokumen_nama_asli }}</span></div>
            </div>

            <!-- Verifikasi Pembina UKM -->
            <div class="bg-white rounded-2xl p-5 border shadow-sm {{ $ajuan->status_pembina === 'disetujui' ? 'border-emerald-200 bg-emerald-50/20' : 'border-amber-200 bg-amber-50/20' }} space-y-2 text-xs">
                <div class="font-bold text-gray-800 text-sm border-b pb-2 flex items-center justify-between">
                    <span class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full {{ $ajuan->status_pembina === 'disetujui' ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                        Verifikasi Pembina UKM
                    </span>
                    @if($ajuan->status_pembina === 'disetujui')
                        <span class="px-2 py-0.5 text-[11px] font-bold rounded-full bg-emerald-100 text-emerald-800">✓ Disetujui</span>
                    @else
                        <span class="px-2 py-0.5 text-[11px] font-bold rounded-full bg-amber-100 text-amber-800">⏳ Belum Disetujui</span>
                    @endif
                </div>
                <div><span class="text-gray-400">Dosen Pembina:</span> <span class="font-bold text-gray-800">{{ $ajuan->ukm->pembina_nama ?? 'Dosen Pembina' }}</span></div>
                @if($ajuan->verified_pembina_at)
                    <div><span class="text-gray-400">Waktu Verifikasi:</span> <span class="text-gray-700">{{ $ajuan->verified_pembina_at->isoFormat('D MMM Y, HH:mm') }}</span></div>
                @endif
                <div class="pt-1">
                    <span class="text-gray-500 font-semibold block">Catatan Pembina:</span>
                    <p class="text-gray-800 italic mt-0.5">{{ $ajuan->catatan_pembina ?? 'Tidak ada catatan khusus dari pembina.' }}</p>
                </div>
            </div>

            <!-- Status Kemahasiswaan -->
            <div class="bg-white rounded-2xl p-5 border shadow-sm {{ $ajuan->status_kemahasiswaan === 'disetujui' ? 'border-emerald-200 bg-emerald-50/30' : ($ajuan->status_kemahasiswaan === 'ditolak' ? 'border-rose-200 bg-rose-50/30' : 'border-amber-200 bg-amber-50/30') }} space-y-2 text-xs">
                <div class="font-bold text-gray-800 text-sm border-b pb-2 flex items-center justify-between">
                    <span class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full {{ $ajuan->status_kemahasiswaan === 'disetujui' ? 'bg-emerald-500' : ($ajuan->status_kemahasiswaan === 'ditolak' ? 'bg-rose-500' : 'bg-amber-500') }}"></span>
                        Status Kemahasiswaan
                    </span>
                    @if($ajuan->status_kemahasiswaan === 'disetujui')
                        <span class="px-2.5 py-1 text-[11px] font-black rounded-full bg-emerald-600 text-white">✓ DISETUJUI</span>
                    @elseif($ajuan->status_kemahasiswaan === 'revisi')
                        <span class="px-2.5 py-1 text-[11px] font-black rounded-full bg-amber-500 text-white">⚠️ PERLU REVISI</span>
                    @elseif($ajuan->status_kemahasiswaan === 'ditolak')
                        <span class="px-2.5 py-1 text-[11px] font-black rounded-full bg-rose-600 text-white">✗ DITOLAK</span>
                    @else
                        <span class="px-2.5 py-1 text-[11px] font-bold rounded-full bg-slate-100 text-slate-700">⏳ Belum Disetujui</span>
                    @endif
                </div>
                @if($ajuan->verified_kemahasiswaan_at)
                    <div><span class="text-gray-400">Diverifikasi oleh:</span> <span class="font-bold text-gray-800">{{ $ajuan->kemahasiswaanVerifier->name ?? 'Kemahasiswaan' }}</span></div>
                    <div><span class="text-gray-400">Tanggal Verifikasi:</span> <span class="text-gray-700">{{ $ajuan->verified_kemahasiswaan_at->isoFormat('D MMMM Y, HH:mm') }}</span></div>
                @else
                    <div class="text-amber-700 font-medium">Silakan periksa checklist pada setiap item tabel di bawah lalu berikan persetujuan akhir.</div>
                @endif
                <div class="pt-1">
                    <span class="text-gray-500 font-semibold block">Catatan Kemahasiswaan:</span>
                    <p class="text-gray-800 italic mt-0.5">{{ $ajuan->catatan_kemahasiswaan ?? 'Belum ada catatan final.' }}</p>
                </div>
            </div>
        </div>

        <!-- Form 1: Pemeriksaan Checklist Tabel Detail Kebutuhan Kegiatan -->
        <form action="{{ route('kemahasiswaan.update-details', $ajuan->id) }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            @csrf

            <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h3 class="font-bold text-gray-900 text-lg flex items-center gap-2">
                        <span>Pemeriksaan Item Detail Ajuan Kegiatan</span>
                    </h3>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Periksa setiap rincian item kebutuhan kegiatan di bawah ini. Centang opsi <strong>Disetujui</strong> atau <strong>Tidak Disetujui</strong> pada kolom Keterangan serta beri catatan jika diperlukan.
                    </p>
                </div>

                <!-- Batch Actions -->
                <div class="flex items-center space-x-2">
                    <button type="button" @click="checkAll('disetujui')" class="px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 rounded-lg text-xs font-bold transition border border-emerald-200">
                        ✓ Centang Semua Disetujui
                    </button>
                    <button type="button" @click="checkAll('tidak_disetujui')" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-800 rounded-lg text-xs font-bold transition border border-rose-200">
                        ✗ Centang Semua Tdk Disetujui
                    </button>
                </div>
            </div>

            <!-- Tabel Detail dengan Checklist Interaktif -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-xs">
                    <thead class="bg-slate-100 text-slate-700 font-bold uppercase tracking-wider">
                        <tr>
                            <th class="px-3 py-3 text-center w-12">No</th>
                            <th class="px-4 py-3 text-left min-w-[140px]">Nama Akun</th>
                            <th class="px-4 py-3 text-left min-w-[180px]">Nama Barang / Item</th>
                            <th class="px-4 py-3 text-right min-w-[120px]">Harga Satuan</th>
                            <th class="px-3 py-3 text-center min-w-[70px]">Jumlah</th>
                            <th class="px-4 py-3 text-right min-w-[120px]">Total</th>
                            <!-- Kolom Keterangan berupa Checklist Disetujui / Tidak Disetujui -->
                            <th class="px-4 py-3 text-center min-w-[220px] bg-indigo-50/70 text-indigo-900 border-x border-indigo-100">
                                Keterangan (Checklist Persetujuan)
                            </th>
                            <th class="px-4 py-3 text-left min-w-[200px]">Catatan Verifikator</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @foreach($ajuan->details as $index => $item)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-3 py-3 text-center font-bold text-gray-400">
                                    {{ $index + 1 }}
                                    <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                                </td>
                                <td class="px-4 py-3 font-semibold text-gray-800">{{ $item->nama_akun }}</td>
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $item->nama_barang }}</td>
                                <td class="px-4 py-3 text-right font-mono text-gray-700">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                                <td class="px-3 py-3 text-center font-mono font-semibold">{{ $item->jumlah }}</td>
                                <td class="px-4 py-3 text-right font-mono font-bold text-indigo-700">Rp {{ number_format($item->total, 0, ',', '.') }}</td>

                                <!-- Kolom Keterangan: Checklist Disetujui / Tidak Disetujui -->
                                <td class="px-4 py-3 bg-indigo-50/20 border-x border-indigo-100">
                                    <div class="flex items-center justify-center space-x-3">
                                        <!-- Opsi Disetujui -->
                                        <label class="inline-flex items-center px-2.5 py-1 rounded-lg border cursor-pointer transition text-xs font-semibold"
                                               :class="statuses[{{ $index }}] === 'disetujui' ? 'bg-emerald-100 border-emerald-400 text-emerald-900' : 'bg-white border-gray-200 text-gray-600 hover:border-emerald-300'">
                                            <input type="radio" name="items[{{ $index }}][status_keterangan]" value="disetujui"
                                                   x-model="statuses[{{ $index }}]"
                                                   class="text-emerald-600 focus:ring-emerald-500 w-3.5 h-3.5">
                                            <span class="ms-1.5">✓ Disetujui</span>
                                        </label>

                                        <!-- Opsi Tidak Disetujui -->
                                        <label class="inline-flex items-center px-2.5 py-1 rounded-lg border cursor-pointer transition text-xs font-semibold"
                                               :class="statuses[{{ $index }}] === 'tidak_disetujui' ? 'bg-rose-100 border-rose-400 text-rose-900' : 'bg-white border-gray-200 text-gray-600 hover:border-rose-300'">
                                            <input type="radio" name="items[{{ $index }}][status_keterangan]" value="tidak_disetujui"
                                                   x-model="statuses[{{ $index }}]"
                                                   class="text-rose-600 focus:ring-rose-500 w-3.5 h-3.5">
                                            <span class="ms-1.5">✗ Tdk Disetujui</span>
                                        </label>
                                    </div>
                                </td>

                                <!-- Catatan Verifikator per baris -->
                                <td class="px-4 py-3">
                                    <input type="text" name="items[{{ $index }}][catatan]" value="{{ $item->catatan }}"
                                           placeholder="Tulis catatan jika ada penyesuaian..."
                                           class="w-full rounded-lg border-gray-300 text-xs focus:ring-indigo-500 focus:border-indigo-500 p-1.5">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-slate-50 border-t-2 border-gray-200 font-bold text-xs text-gray-800">
                        <tr>
                            <td colspan="5" class="px-4 py-3 text-right uppercase tracking-wider text-gray-600">
                                Total Anggaran Yang Diajukan:
                            </td>
                            <td class="px-4 py-3 text-right text-sm text-indigo-700 font-mono font-black">
                                Rp {{ number_format($ajuan->total_anggaran, 0, ',', '.') }}
                            </td>
                            <td colspan="2" class="px-4 py-3 text-right">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-sm transition gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                                    Simpan Status Checklist Detail
                                </button>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </form>

        <!-- Form 2: Persetujuan Akhir Usulan Kegiatan Secara Keseluruhan Sesuai Permintaan Soal -->
        <div class="bg-white rounded-2xl p-6 sm:p-8 border-2 border-indigo-200 shadow-md">
            <div class="border-b border-gray-100 pb-4 mb-5">
                <span class="text-xs font-bold uppercase tracking-wider text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-md">
                    Tahap Akhir
                </span>
                <h3 class="font-extrabold text-xl text-gray-900 mt-2">
                    Persetujuan Usulan Kegiatan Keseluruhan
                </h3>
                <p class="text-xs text-gray-500 mt-0.5">
                    Setelah selesai memeriksa tabel detail ajuan kegiatan di atas, tekan tombol <strong>Disetujui</strong> di bawah ini untuk menandai bahwa usulan kegiatan secara keseluruhan telah resmi disetujui oleh Biro Kemahasiswaan.
                </p>
            </div>

            <form action="{{ route('kemahasiswaan.approve', $ajuan->id) }}" method="POST">
                @csrf

                <div class="mb-5">
                    <label for="catatan_kemahasiswaan" class="block text-sm font-semibold text-gray-700 mb-1">
                        Catatan Resmi Kemahasiswaan (Opsional):
                    </label>
                    <textarea id="catatan_kemahasiswaan" name="catatan_kemahasiswaan" rows="3"
                              placeholder="Tuliskan pesan persetujuan, instruksi pencairan dana, atau syarat pelaksanaan kegiatan..."
                              class="w-full rounded-xl border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">{{ old('catatan_kemahasiswaan', $ajuan->catatan_kemahasiswaan) }}</textarea>
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-2">
                    <div class="text-xs text-gray-500">
                        @if($ajuan->status_kemahasiswaan === 'disetujui')
                            <span class="text-emerald-700 font-bold text-sm flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                Usulan ini telah Disetujui Secara Keseluruhan
                            </span>
                        @else
                            <span class="text-amber-600 font-medium">Usulan ini menunggu keputusan akhir Kemahasiswaan.</span>
                        @endif
                    </div>

                    <div class="flex items-center space-x-3">
                        <!-- Tombol Tolak Keseluruhan jika tidak memenuhi syarat -->
                        <button type="submit" name="status" value="ditolak" onclick="return confirm('Apakah Anda yakin ingin menolak usulan kegiatan ini secara keseluruhan?')"
                                class="px-4 py-2.5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-xl text-xs font-bold transition">
                            ✗ Tolak Usulan
                        </button>

                        <!-- Tombol Minta Revisi -->
                        <button type="submit" name="status" value="revisi" onclick="return confirm('Apakah Anda yakin ingin meminta perbaikan/revisi kepada mahasiswa? Pastikan catatan revisi telah diisi.')"
                                class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-white rounded-xl text-xs font-bold shadow-md shadow-amber-200 transition flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            ⚠️ Minta Revisi Usulan
                        </button>

                        <!-- Tombol Disetujui yang menandai usulan kegiatan secara keseluruhan disetujui -->
                        <button type="submit" name="status" value="disetujui" onclick="return confirm('Apakah Anda yakin untuk MENYETUJUI usulan kegiatan ini secara keseluruhan?')"
                                class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white rounded-xl text-xs font-black shadow-lg shadow-emerald-200 transition flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            <span>✓ Disetujui (Setujui Seluruh Usulan)</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>

    </div>

    <!-- Alpine.js logic for checklist toggles -->
    <script>
        function kemahasiswaanReview() {
            return {
                statuses: [
                    @foreach($ajuan->details as $item)
                        '{{ $item->status_keterangan ?: "disetujui" }}',
                    @endforeach
                ],
                checkAll(value) {
                    for (let i = 0; i < this.statuses.length; i++) {
                        this.statuses[i] = value;
                    }
                }
            }
        }
    </script>
</x-app-layout>
