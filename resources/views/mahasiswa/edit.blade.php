<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $ajuan->status_akhir === 'ditolak' ? 'bg-rose-100 text-rose-800 border-rose-200' : 'bg-amber-100 text-amber-800 border-amber-200' }} border">
                        {{ $ajuan->status_akhir === 'ditolak' ? 'Mode Perbaikan Usulan (Ditolak)' : 'Mode Perbaikan / Revisi Usulan' }}
                    </span>
                    <span class="text-xs text-gray-500">• Revisi ke-{{ $ajuan->revisi_count + 1 }}</span>
                </div>
                <h2 class="font-black text-2xl text-gray-900 leading-tight">
                    Perbaiki Usulan: {{ $ajuan->nama_kegiatan }}
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">
                    UKM Pemohon: <span class="font-bold text-indigo-700">{{ $ukm->nama_ukm }}</span>
                </p>
            </div>
            <a href="{{ route('mahasiswa.show', $ajuan->id) }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl text-xs transition">
                ← Batal & Kembali
            </a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6" x-data="editForm()">

        <!-- Banner Catatan Revisi dari Verifikator -->
        <div class="{{ $ajuan->status_akhir === 'ditolak' ? 'bg-rose-50 border-2 border-rose-300' : 'bg-amber-50 border-2 border-amber-300' }} rounded-2xl p-6 shadow-sm">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-xl {{ $ajuan->status_akhir === 'ditolak' ? 'bg-rose-200 text-rose-900' : 'bg-amber-200 text-amber-900' }} flex items-center justify-center font-bold text-lg shrink-0">
                    {{ $ajuan->status_akhir === 'ditolak' ? '✕' : '⚠️' }}
                </div>
                <div class="space-y-1">
                    <h3 class="font-black {{ $ajuan->status_akhir === 'ditolak' ? 'text-rose-900' : 'text-amber-900' }} text-base">
                        @if($ajuan->status_akhir === 'ditolak')
                            Alasan Penolakan & Arahan Perbaikan dari {{ $ajuan->isRevisiPembina() ? 'Dosen Pembina UKM' : 'Biro Kemahasiswaan Universitas' }}
                        @elseif($ajuan->isRevisiPembina())
                            Instruksi Revisi dari Dosen Pembina UKM
                        @else
                            Instruksi Revisi dari Biro Kemahasiswaan Universitas
                        @endif
                    </h3>
                    <p class="text-sm {{ $ajuan->status_akhir === 'ditolak' ? 'text-rose-800' : 'text-amber-800' }} italic bg-white/70 p-3 rounded-xl border {{ $ajuan->status_akhir === 'ditolak' ? 'border-rose-200' : 'border-amber-200' }} mt-1">
                        "{{ $ajuan->catatan_revisi_terakhir ?? ($ajuan->catatan_kemahasiswaan ?: $ajuan->catatan_pembina) }}"
                    </p>
                    <p class="text-xs {{ $ajuan->status_akhir === 'ditolak' ? 'text-rose-700' : 'text-amber-700' }} mt-2">
                        Silakan sesuaikan data kegiatan, dokumen proposal, atau rincian item anggaran pada tabel RAB di bawah ini sesuai arahan di atas, kemudian tekan <strong>Kirim Ulang Usulan</strong>.
                    </p>
                </div>
            </div>
        </div>

        <form action="{{ route('mahasiswa.update', $ajuan->id) }}" method="POST" enctype="multipart/form-data" @submit="validateSubmit($event)">
            @csrf
            @method('PUT')

            <!-- Bagian 1: Informasi Kegiatan & Dokumen -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 sm:p-8 space-y-6 mb-6">
                <div class="border-b border-gray-100 pb-4">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-sm">1</span>
                        Informasi Utama & Dokumen Usulan
                    </h3>
                    <p class="text-xs text-gray-500 mt-0.5">Perbarui nama kegiatan, tanggal pelaksanaan, atau unggah dokumen proposal revisi baru.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nama Kegiatan -->
                    <div class="md:col-span-2">
                        <label for="nama_kegiatan" class="block text-sm font-semibold text-gray-700 mb-1">
                            Nama Kegiatan <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="nama_kegiatan" name="nama_kegiatan" value="{{ old('nama_kegiatan', $ajuan->nama_kegiatan) }}" required
                               class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-sm">
                        @error('nama_kegiatan')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tanggal Pelaksanaan -->
                    <div>
                        <label for="tanggal_kegiatan" class="block text-sm font-semibold text-gray-700 mb-1">
                            Tanggal Pelaksanaan Kegiatan <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" id="tanggal_kegiatan" name="tanggal_kegiatan" value="{{ old('tanggal_kegiatan', $ajuan->tanggal_kegiatan->toDateString()) }}" required
                               class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-sm">
                        @error('tanggal_kegiatan')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Upload Dokumen Proposal Revisi (Opsional ganti file) -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            Dokumen Proposal / ToR
                        </label>
                        <div class="mb-2 text-xs flex items-center gap-2 p-2 bg-slate-50 border rounded-lg text-slate-700">
                            <svg class="w-4 h-4 text-indigo-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <span class="truncate">File Saat Ini: <strong>{{ $ajuan->dokumen_nama_asli }}</strong></span>
                        </div>

                        <div class="relative">
                            <input type="file" id="dokumen_kegiatan" name="dokumen_kegiatan" accept=".pdf,.doc,.docx"
                                   @change="handleFileUpload($event)"
                                   class="hidden">
                            <label for="dokumen_kegiatan" class="cursor-pointer border-2 border-dashed rounded-xl p-3 flex items-center justify-between transition hover:border-indigo-400"
                                   :class="newFileName ? 'border-emerald-400 bg-emerald-50/50' : 'border-gray-300 bg-white'">
                                <div class="flex items-center space-x-2 truncate">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-sm"
                                         :class="newFileName ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600'">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                    </div>
                                    <div class="truncate text-xs">
                                        <p class="font-medium text-gray-800 truncate" x-text="newFileName ? newFileName : 'Unggah file baru (opsional)'"></p>
                                        <p class="text-[10px] text-gray-400" x-text="newFileName ? newFileSize : 'Biarkan kosong jika tetap memakai file lama'"></p>
                                    </div>
                                </div>
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-lg shrink-0 bg-indigo-50 text-indigo-700 border border-indigo-200">
                                    Browse Baru
                                </span>
                            </label>
                        </div>
                        @error('dokumen_kegiatan')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Bagian 2: CRUD Tabel Detail Kebutuhan Kegiatan -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 sm:p-8 space-y-6">
                <div class="border-b border-gray-100 pb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            <span class="w-7 h-7 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-sm">2</span>
                            Perbaikan Tabel Detail Kebutuhan Kegiatan (RAB)
                        </h3>
                        <p class="text-xs text-gray-500 mt-0.5">
                            Sesuaikan rincian harga atau kuantitas barang, hapus item yang tidak disetujui, atau tambah item pengganti.
                        </p>
                    </div>

                    <button type="button" @click="addRow()" class="inline-flex items-center px-3.5 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-xl text-xs font-bold transition gap-1.5 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Baris Baru
                    </button>
                </div>

                <!-- Interactive CRUD Table -->
                <div class="overflow-x-auto rounded-xl border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200 text-xs">
                        <thead class="bg-slate-100 text-slate-700 font-bold uppercase tracking-wider">
                            <tr>
                                <th class="px-3 py-3 text-center w-12">No</th>
                                <th class="px-3 py-3 text-left min-w-[140px]">Nama Akun</th>
                                <th class="px-3 py-3 text-left min-w-[180px]">Nama Barang / Item</th>
                                <th class="px-3 py-3 text-right min-w-[130px]">Harga Satuan (Rp)</th>
                                <th class="px-3 py-3 text-center min-w-[90px]">Jumlah</th>
                                <th class="px-3 py-3 text-right min-w-[130px]">Total (Rp)</th>
                                <th class="px-3 py-3 text-left min-w-[180px]">Catatan / Keterangan</th>
                                <th class="px-3 py-3 text-center w-12">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            <template x-for="(item, index) in items" :key="index">
                                <tr class="hover:bg-indigo-50/30 transition">
                                    <!-- No -->
                                    <td class="px-3 py-2.5 text-center font-bold text-gray-500" x-text="index + 1"></td>

                                    <!-- Nama Akun -->
                                    <td class="px-3 py-2.5">
                                        <input type="text" :name="'items[' + index + '][nama_akun]'" x-model="item.nama_akun" required
                                               placeholder="Misal: Konsumsi / Perlengkapan"
                                               class="w-full rounded-lg border-gray-300 text-xs focus:ring-indigo-500 focus:border-indigo-500 p-1.5">
                                    </td>

                                    <!-- Nama Barang -->
                                    <td class="px-3 py-2.5">
                                        <input type="text" :name="'items[' + index + '][nama_barang]'" x-model="item.nama_barang" required
                                               placeholder="Nama barang"
                                               class="w-full rounded-lg border-gray-300 text-xs focus:ring-indigo-500 focus:border-indigo-500 p-1.5">
                                    </td>

                                    <!-- Harga Satuan -->
                                    <td class="px-3 py-2.5">
                                        <input type="number" min="0" step="1000" :name="'items[' + index + '][harga_satuan]'" x-model.number="item.harga_satuan" required
                                               class="w-full rounded-lg border-gray-300 text-xs text-right focus:ring-indigo-500 focus:border-indigo-500 p-1.5 font-mono">
                                    </td>

                                    <!-- Jumlah -->
                                    <td class="px-3 py-2.5">
                                        <input type="number" min="1" step="1" :name="'items[' + index + '][jumlah]'" x-model.number="item.jumlah" required
                                               class="w-full rounded-lg border-gray-300 text-xs text-center focus:ring-indigo-500 focus:border-indigo-500 p-1.5 font-mono">
                                    </td>

                                    <!-- Total (Calculated) -->
                                    <td class="px-3 py-2.5 text-right font-bold text-indigo-700 font-mono whitespace-nowrap"
                                        x-text="formatRupiah((item.harga_satuan || 0) * (item.jumlah || 0))">
                                    </td>

                                    <!-- Catatan -->
                                    <td class="px-3 py-2.5">
                                        <input type="text" :name="'items[' + index + '][catatan]'" x-model="item.catatan"
                                               placeholder="Catatan penyesuaian"
                                               class="w-full rounded-lg border-gray-300 text-xs focus:ring-indigo-500 focus:border-indigo-500 p-1.5">
                                    </td>

                                    <!-- Tombol Hapus -->
                                    <td class="px-3 py-2.5 text-center">
                                        <button type="button" @click="removeRow(index)" :disabled="items.length <= 1"
                                                :class="items.length <= 1 ? 'opacity-30 cursor-not-allowed text-gray-400' : 'hover:bg-rose-50 text-rose-600 hover:text-rose-800'"
                                                class="p-1 rounded-md transition" title="Hapus baris ini">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                        <tfoot class="bg-slate-50 font-bold text-xs text-gray-800 border-t-2 border-gray-200">
                            <tr>
                                <td colspan="5" class="px-4 py-3 text-right uppercase tracking-wider text-gray-600">
                                    Total Anggaran Hasil Revisi:
                                </td>
                                <td class="px-3 py-3 text-right text-base text-indigo-700 font-extrabold font-mono" x-text="formatRupiah(grandTotal())"></td>
                                <td colspan="2" class="px-3 py-3 text-xs text-gray-500 italic">
                                    <span x-text="items.length"></span> baris item rincian
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Tombol Resubmit -->
                <div class="pt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-t border-gray-100">
                    <div class="text-xs text-gray-500">
                        Menekan tombol ini akan mengirimkan usulan yang sudah diperbaiki ke pihak verifikator terkait.
                    </div>
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('mahasiswa.show', $ajuan->id) }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-semibold transition">
                            Batal
                        </a>
                        <button type="submit" onclick="return confirm('Apakah Anda yakin data revisi sudah lengkap dan siap dikirimkan kembali?')"
                                class="px-6 py-2.5 bg-amber-600 hover:bg-amber-700 active:bg-amber-800 text-white rounded-xl text-sm font-bold shadow-md shadow-amber-200 transition flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            Kirim Ulang Usulan (Resubmit)
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Script Alpine.js pre-filled dengan data lama -->
    <script>
        function editForm() {
            return {
                newFileName: '',
                newFileSize: '',
                items: [
                    @foreach($ajuan->details as $item)
                        {
                            nama_akun: @json($item->nama_akun),
                            nama_barang: @json($item->nama_barang),
                            harga_satuan: {{ (float) $item->harga_satuan }},
                            jumlah: {{ (int) $item->jumlah }},
                            catatan: @json($item->catatan ?? '')
                        },
                    @endforeach
                ],
                handleFileUpload(event) {
                    const file = event.target.files[0];
                    if (file) {
                        this.newFileName = file.name;
                        this.newFileSize = (file.size / (1024 * 1024)).toFixed(2) + ' MB';
                    }
                },
                addRow() {
                    this.items.push({
                        nama_akun: '',
                        nama_barang: '',
                        harga_satuan: 0,
                        jumlah: 1,
                        catatan: ''
                    });
                },
                removeRow(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                    }
                },
                grandTotal() {
                    return this.items.reduce((sum, item) => {
                        return sum + ((Number(item.harga_satuan) || 0) * (Number(item.jumlah) || 0));
                    }, 0);
                },
                formatRupiah(number) {
                    return 'Rp ' + Number(number).toLocaleString('id-ID');
                },
                validateSubmit(e) {
                    if (this.items.length === 0) {
                        alert('Harap masukkan minimal 1 baris kebutuhan kegiatan.');
                        e.preventDefault();
                        return;
                    }
                    for (let i = 0; i < this.items.length; i++) {
                        if (!this.items[i].nama_akun || !this.items[i].nama_barang) {
                            alert('Harap lengkapi Nama Akun dan Nama Barang pada baris ke-' + (i + 1));
                            e.preventDefault();
                            return;
                        }
                    }
                }
            }
        }
    </script>
</x-app-layout>
