<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $ajuan->status_akhir === 'ditolak' ? 'bg-rose-100 text-rose-800 border-rose-200' : ($ajuan->isPerluRevisi() ? 'bg-amber-100 text-amber-800 border-amber-200' : 'bg-blue-100 text-blue-800 border-blue-200') }} border">
                        @if($ajuan->status_akhir === 'ditolak')
                            Mode Perbaikan Usulan (Ditolak)
                        @elseif($ajuan->isPerluRevisi())
                            Mode Perbaikan / Revisi Usulan
                        @else
                            Mode Edit Usulan Kegiatan
                        @endif
                    </span>
                    @if($ajuan->isPerluRevisi())
                        <span class="text-xs text-gray-500">• Revisi ke-{{ $ajuan->revisi_count + 1 }}</span>
                    @endif
                </div>
                <h2 class="font-black text-2xl text-gray-900 leading-tight">
                    {{ $ajuan->isPerluRevisi() ? 'Perbaiki Usulan: ' : 'Edit Usulan: ' }}{{ $ajuan->nama_kegiatan }}
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

        @if($ajuan->isPerluRevisi())
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
        @else
            <!-- Banner Mode Edit Usulan Sebelum Disetujui -->
            <div class="bg-blue-50 border-2 border-blue-300 rounded-2xl p-5 shadow-sm flex items-start gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-200 text-blue-900 flex items-center justify-center font-bold text-lg shrink-0">
                    ℹ️
                </div>
                <div class="space-y-1">
                    <h3 class="font-bold text-blue-900 text-base">
                        Edit Usulan Kegiatan (Tahap Verifikasi)
                    </h3>
                    <p class="text-xs text-blue-800 leading-relaxed">
                        Usulan ini belum dinyatakan disetujui sehingga Anda masih dapat menyesuaikan informasi kegiatan dan rincian kebutuhan anggaran (RAB) sebelum usulan ini disahkan oleh pihak verifikator. Tekan <strong>Simpan Perubahan Usulan</strong> di bagian bawah setelah selesai mengedit.
                    </p>
                </div>
            </div>
        @endif

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

                    <!-- Jenis Kegiatan -->
                    <div>
                        <label for="jenis_kegiatan" class="block text-sm font-semibold text-gray-700 mb-1">
                            Jenis Kegiatan <span class="text-rose-500">*</span>
                        </label>
                        <select id="jenis_kegiatan" name="jenis_kegiatan" required
                                class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-sm">
                            <option value="" disabled>-- Pilih Jenis Kegiatan --</option>
                            <option value="Lomba" {{ old('jenis_kegiatan', $ajuan->jenis_kegiatan ?? 'Lainnya') === 'Lomba' ? 'selected' : '' }}>Lomba</option>
                            <option value="Pelatihan" {{ old('jenis_kegiatan', $ajuan->jenis_kegiatan ?? 'Lainnya') === 'Pelatihan' ? 'selected' : '' }}>Pelatihan</option>
                            <option value="Lainnya" {{ old('jenis_kegiatan', $ajuan->jenis_kegiatan ?? 'Lainnya') === 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                        @error('jenis_kegiatan')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Upload Dokumen Proposal Revisi (Opsional ganti file) -->
                    <div class="md:col-span-2">
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
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 sm:p-6 space-y-4">
                <div class="border-b border-gray-100 pb-3 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-xs">2</span>
                            Perbaikan Tabel Detail Kebutuhan Kegiatan (RAB)
                        </h3>
                        <p class="text-xs text-gray-500 mt-0.5">
                            Total terhitung otomatis: <strong class="text-indigo-600">Jumlah &times; Harga Satuan &times; Durasi</strong>.
                        </p>
                    </div>

                    <button type="button" @click="addRow()" class="inline-flex items-center px-3.5 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-xl text-xs font-bold transition gap-1.5 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Baris Baru
                    </button>
                </div>

                <!-- Interactive CRUD Table (Fit to screen, no horizontal scroll) -->
                <div class="w-full overflow-x-auto rounded-xl border border-gray-200">
                    <table class="w-full divide-y divide-gray-200 text-xs table-fixed">
                        <thead class="bg-slate-100 text-slate-700 font-bold uppercase tracking-wider text-[11px]">
                            <tr>
                                <th class="px-1.5 py-2.5 text-center w-8">No</th>
                                <th class="px-1.5 py-2.5 text-left w-[13%]">Nama Akun</th>
                                <th class="px-1.5 py-2.5 text-left w-[19%]">Nama Barang / Item</th>
                                <th class="px-1.5 py-2.5 text-right w-[12%]">Harga Satuan</th>
                                <th class="px-1 py-2.5 text-center w-[6%]">Jumlah</th>
                                <th class="px-1 py-2.5 text-center w-[8%]">Satuan</th>
                                <th class="px-1 py-2.5 text-center w-[6%]">Durasi</th>
                                <th class="px-1 py-2.5 text-center w-[9%]">Sat. Durasi</th>
                                <th class="px-1.5 py-2.5 text-right w-[12%]">Total (Rp)</th>
                                <th class="px-1.5 py-2.5 text-left w-[10%]">Catatan</th>
                                <th class="px-1 py-2.5 text-center w-8">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            <template x-for="(item, index) in items" :key="index">
                                <tr class="hover:bg-indigo-50/30 transition">
                                    <!-- No -->
                                    <td class="px-1.5 py-2 text-center font-bold text-gray-500" x-text="index + 1"></td>

                                    <!-- Nama Akun -->
                                    <td class="px-1.5 py-2">
                                        <input type="text" :name="'items[' + index + '][nama_akun]'" x-model="item.nama_akun" required
                                               placeholder="Misal: Konsumsi"
                                               class="w-full rounded-md border-gray-300 text-xs focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 p-1.5">
                                    </td>

                                    <!-- Nama Barang -->
                                    <td class="px-1.5 py-2">
                                        <input type="text" :name="'items[' + index + '][nama_barang]'" x-model="item.nama_barang" required
                                               placeholder="Nama barang"
                                               class="w-full rounded-md border-gray-300 text-xs focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 p-1.5">
                                    </td>

                                    <!-- Harga Satuan -->
                                    <td class="px-1.5 py-2">
                                        <input type="number" min="0" step="1000" :name="'items[' + index + '][harga_satuan]'" x-model.number="item.harga_satuan" required
                                               class="w-full rounded-md border-gray-300 text-xs text-right focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 p-1.5 font-mono">
                                    </td>

                                    <!-- Jumlah -->
                                    <td class="px-1 py-2">
                                        <input type="number" min="1" step="1" :name="'items[' + index + '][jumlah]'" x-model.number="item.jumlah" required
                                               class="w-full rounded-md border-gray-300 text-xs text-center focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 p-1.5 font-mono">
                                    </td>

                                    <!-- Satuan (untuk Jumlah) -->
                                    <td class="px-1 py-2">
                                        <select :name="'items[' + index + '][satuan]'" x-model="item.satuan" required
                                                class="w-full rounded-md border-gray-300 text-xs focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 p-1.5 bg-white font-medium">
                                            @foreach($satuans as $satuan)
                                                <option value="{{ $satuan->nama_satuan }}">{{ $satuan->nama_satuan }}</option>
                                            @endforeach
                                        </select>
                                    </td>

                                    <!-- Durasi -->
                                    <td class="px-1 py-2">
                                        <input type="number" min="0.1" step="any" placeholder="1" :name="'items[' + index + '][durasi]'" x-model.number="item.durasi" required
                                               class="w-full rounded-md border-gray-300 text-xs text-center focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 p-1.5 font-mono">
                                    </td>

                                    <!-- Satuan Durasi (sebelum Total) -->
                                    <td class="px-1 py-2">
                                        <select :name="'items[' + index + '][satuan_durasi]'" x-model="item.satuan_durasi" required
                                                class="w-full rounded-md border-gray-300 text-xs focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 p-1.5 bg-white font-medium">
                                            @foreach($satuans as $satuan)
                                                <option value="{{ $satuan->nama_satuan }}">{{ $satuan->nama_satuan }}</option>
                                            @endforeach
                                        </select>
                                    </td>

                                    <!-- Total (Calculated: Jumlah * Harga Satuan * Durasi) -->
                                    <td class="px-1.5 py-2 text-right font-bold text-indigo-700 font-mono whitespace-nowrap text-xs"
                                        x-text="formatRupiah((item.harga_satuan || 0) * (item.jumlah || 0) * (item.durasi || 1))">
                                    </td>

                                    <!-- Catatan -->
                                    <td class="px-1.5 py-2">
                                        <input type="text" :name="'items[' + index + '][catatan]'" x-model="item.catatan"
                                               placeholder="Catatan penyesuaian"
                                               class="w-full rounded-md border-gray-300 text-xs focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 p-1.5">
                                    </td>

                                    <!-- Tombol Hapus -->
                                    <td class="px-1 py-2 text-center">
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
                                <td colspan="8" class="px-3 py-2.5 text-right uppercase tracking-wider text-gray-600 text-[11px]">
                                    Total Anggaran Hasil Revisi:
                                </td>
                                <td class="px-2 py-2.5 text-right text-xs sm:text-sm text-indigo-700 font-extrabold font-mono whitespace-nowrap" x-text="formatRupiah(grandTotal())"></td>
                                <td colspan="2" class="px-2 py-2.5 text-xs text-gray-500 italic">
                                    <span x-text="items.length"></span> baris item
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Tombol Submit -->
                <div class="pt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-t border-gray-100">
                    <div class="text-xs text-gray-500">
                        {{ $ajuan->isPerluRevisi() ? 'Menekan tombol ini akan mengirimkan usulan yang sudah diperbaiki ke pihak verifikator terkait.' : 'Menekan tombol ini akan menyimpan perubahan usulan kegiatan dan rincian RAB ke sistem.' }}
                    </div>
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('mahasiswa.show', $ajuan->id) }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-semibold transition">
                            Batal
                        </a>
                        <button type="submit" onclick="return confirm('{{ $ajuan->isPerluRevisi() ? 'Apakah Anda yakin data revisi sudah lengkap dan siap dikirimkan kembali?' : 'Apakah Anda yakin ingin menyimpan perubahan data usulan ini?' }}')"
                                class="px-6 py-2.5 {{ $ajuan->isPerluRevisi() ? 'bg-amber-600 hover:bg-amber-700 active:bg-amber-800 shadow-amber-200' : 'bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 shadow-indigo-200' }} text-white rounded-xl text-sm font-bold shadow-md transition flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                            <span>{{ $ajuan->isPerluRevisi() ? 'Kirim Ulang Usulan (Resubmit)' : 'Simpan Perubahan Usulan' }}</span>
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
                            satuan: @json($item->satuan ?? ($satuans->first()->nama_satuan ?? 'Buah')),
                            durasi: {{ (float) ($item->durasi ?? 1) }},
                            satuan_durasi: @json($item->satuan_durasi ?? 'Hari'),
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
                        satuan: '{{ $satuans->first()->nama_satuan ?? "Buah" }}',
                        durasi: 1,
                        satuan_durasi: '{{ $satuans->where("nama_satuan", "Hari")->first()->nama_satuan ?? ($satuans->first()->nama_satuan ?? "Hari") }}',
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
                        return sum + ((Number(item.harga_satuan) || 0) * (Number(item.jumlah) || 0) * (Number(item.durasi) || 1));
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
                        if (!this.items[i].nama_akun || !this.items[i].nama_barang || !this.items[i].satuan || !this.items[i].satuan_durasi) {
                            alert('Harap lengkapi Nama Akun, Nama Barang, Satuan, dan Satuan Durasi pada baris ke-' + (i + 1));
                            e.preventDefault();
                            return;
                        }
                        if (!this.items[i].jumlah || Number(this.items[i].jumlah) <= 0) {
                            alert('Harap isi Jumlah minimal 1 pada baris ke-' + (i + 1));
                            e.preventDefault();
                            return;
                        }
                        if (!this.items[i].durasi || Number(this.items[i].durasi) <= 0) {
                            alert('Harap isi Durasi minimal 1 pada baris ke-' + (i + 1));
                            e.preventDefault();
                            return;
                        }
                    }
                }
            }
        }
    </script>
</x-app-layout>
