<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1.5">
                    <span class="text-xs font-bold px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200">
                        {{ $ajuan->ukm->nama_ukm }}
                    </span>
                    <span class="text-xs text-gray-500 font-medium">• Tanggal Pelaksanaan: <strong class="text-gray-700">{{ $ajuan->tanggal_kegiatan->isoFormat('D MMMM Y') }}</strong></span>
                </div>
                <h2 class="font-black text-2xl text-gray-900 leading-tight">
                    Laporan Pertanggungjawaban (LPJ): {{ $ajuan->nama_kegiatan }}
                </h2>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('mahasiswa.show', $ajuan->id) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white hover:bg-gray-50 text-gray-700 font-bold rounded-xl text-xs transition border border-gray-200 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    <span>← Kembali ke Detail</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6 pb-12" x-data="lpjForm()">

        <!-- Banner Panduan LPJ yang Elegan & Berkontras Tinggi -->
        <div class="relative overflow-hidden rounded-2xl p-6 sm:p-7 text-white shadow-xl border border-indigo-800/40"
             style="background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 45%, #312e81 100%);">
            <!-- Dekorasi latar belakang halus -->
            <div class="absolute -right-10 -top-10 w-48 h-48 rounded-full bg-indigo-500/10 blur-2xl pointer-events-none"></div>
            <div class="absolute right-40 -bottom-10 w-48 h-48 rounded-full bg-purple-500/10 blur-2xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div class="space-y-2.5 max-w-3xl">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/20 text-indigo-200 text-xs font-bold border border-indigo-400/30 backdrop-blur-sm">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>📋 Formulir Laporan Pertanggungjawaban Resmi</span>
                    </div>

                    <h3 class="text-2xl font-black tracking-tight text-white drop-shadow-sm">
                        Pelaporan Pasca-Kegiatan &amp; Data SIMKATMAWA
                    </h3>

                    <p class="text-xs sm:text-sm text-indigo-100/90 leading-relaxed max-w-2xl font-normal">
                        Lengkapi laporan hasil kegiatan, data mahasiswa peserta untuk borang <strong>SIMKATMAWA</strong>, serta unggah bukti dokumentasi kegiatan. Semua berkas lampiran bersifat <span class="underline decoration-indigo-300 font-semibold text-white">opsional (tidak mandatori)</span>.
                    </p>
                </div>

                <!-- Card Pagu Anggaran Ringkas -->
                <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 sm:p-5 border border-white/20 shadow-inner shrink-0 flex sm:flex-col justify-between items-start sm:items-end gap-2">
                    <div>
                        <span class="text-[11px] font-bold text-indigo-200 uppercase tracking-wider block sm:text-right">
                            Pagu Anggaran Disetujui
                        </span>
                        <div class="text-xl sm:text-2xl font-black font-mono text-emerald-300 tracking-tight sm:text-right mt-0.5"
                             x-text="formatRupiah(pagu_disetujui)">
                            Rp {{ number_format($paguDisetujui, 0, ',', '.') }}
                        </div>
                    </div>
                    <span class="text-[10px] text-indigo-200/80 italic sm:text-right">
                        Realisasi dihitung di Bagian 3
                    </span>
                </div>
            </div>
        </div>

        @if($ajuan->isRevisiLpj())
            <div class="bg-amber-50 border-2 border-amber-400 rounded-2xl p-5 shadow-sm">
                <div class="flex items-start gap-3">
                    <span class="text-xl">⚠️</span>
                    <div>
                        <h4 class="font-bold text-amber-900 text-sm">Catatan Perbaikan LPJ dari Kemahasiswaan:</h4>
                        <p class="text-xs text-amber-800 italic mt-1">"{{ $ajuan->lpj->catatan_verifikasi_lpj ?? 'Mohon periksa dan lengkapi berkas LPJ Anda.' }}"</p>
                    </div>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-rose-50 border border-rose-200 rounded-2xl p-4 text-rose-800 text-xs">
                <p class="font-bold mb-1">Terdapat kesalahan input formulir:</p>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('mahasiswa.lpj.store', $ajuan->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- BAGIAN 1: HASIL & CAPAIAN PRESTASI KEGIATAN (MULTI-JUARA / DINAMIS) -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 sm:p-8">
                <div class="border-b border-gray-100 pb-4 mb-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-md">Bagian 1</span>
                        <h3 class="font-bold text-gray-900 text-lg mt-1">Capaian, Hasil &amp; Perolehan Juara Kegiatan</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Catat setiap medali/juara yang dimenangkan (bisa lebih dari satu kategori, misal: Juara 1 Gaya Dada, Juara 2 Gaya Bebas).</p>
                    </div>
                    <button type="button" @click="addPrestasi()"
                            class="inline-flex items-center px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-xs transition shadow-sm gap-1.5 self-start sm:self-auto">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        <span>+ Tambah Prestasi / Juara</span>
                    </button>
                </div>

                <!-- Daftar Baris Multi-Prestasi / Juara -->
                <div class="mb-6">
                    <template x-if="prestasies.length === 0">
                        <div class="bg-slate-50 border-2 border-dashed border-gray-200 rounded-2xl p-6 text-center">
                            <div class="w-12 h-12 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center mx-auto mb-2 font-bold text-xl">
                                🏆
                            </div>
                            <p class="text-xs font-bold text-gray-800">Belum Ada Catatan Kejuaraan / Medali</p>
                            <p class="text-[11px] text-gray-500 mt-0.5 max-w-md mx-auto">
                                Jika kegiatan ini menghasilkan juara perlombaan, klik tombol tambah di bawah. Untuk kegiatan non-lomba (seperti pelatihan), Anda dapat membiarkannya kosong.
                            </p>
                            <div class="mt-3 flex items-center justify-center gap-2">
                                <button type="button" @click="addPrestasi()"
                                        class="px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-xs transition shadow-sm flex items-center gap-1">
                                    <span>+ Catat Juara / Medali</span>
                                </button>
                            </div>
                        </div>
                    </template>

                    <div class="space-y-3" x-show="prestasies.length > 0">
                        <template x-for="(prestasiItem, prIdx) in prestasies" :key="prIdx">
                            <div class="bg-slate-50/80 border border-slate-200 rounded-xl p-4 transition hover:border-indigo-300">
                                <div class="flex items-center justify-between gap-2 mb-3 pb-2 border-b border-slate-200/70">
                                    <div class="flex items-center gap-2">
                                        <span class="w-6 h-6 rounded-full bg-amber-100 text-amber-900 text-xs font-black flex items-center justify-center"
                                              x-text="prIdx + 1"></span>
                                        <span class="text-xs font-bold text-gray-800">
                                            Capaian / Juara Ke-<span x-text="prIdx + 1"></span>
                                        </span>
                                    </div>
                                    <button type="button" @click="removePrestasi(prIdx)"
                                            class="text-rose-500 hover:text-rose-700 text-xs font-bold transition flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        <span>Hapus</span>
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                                    <!-- Nomor / Kategori Lomba -->
                                    <div class="sm:col-span-4">
                                        <label class="block text-[11px] font-bold text-gray-700 mb-1">
                                            Nomor / Kategori Cabang Lomba <span class="text-rose-500">*</span>
                                        </label>
                                        <input type="text"
                                               :name="'prestasies[' + prIdx + '][kategori_lomba]'"
                                               x-model="prestasiItem.kategori_lomba"
                                               placeholder="Contoh: Gaya Dada 100m Putra"
                                               required
                                               class="w-full rounded-lg border-gray-300 text-xs focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>

                                    <!-- Peringkat Juara -->
                                    <div class="sm:col-span-3">
                                        <label class="block text-[11px] font-bold text-gray-700 mb-1">
                                            Peringkat Capaian <span class="text-rose-500">*</span>
                                        </label>
                                        <select :name="'prestasies[' + prIdx + '][peringkat_juara]'"
                                                x-model="prestasiItem.peringkat_juara"
                                                required
                                                class="w-full rounded-lg border-gray-300 text-xs focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                                            <option value="Juara 1 (Emas / Gold Medal)">🥇 Juara 1 (Emas)</option>
                                            <option value="Juara 2 (Perak / Silver Medal)">🥈 Juara 2 (Perak)</option>
                                            <option value="Juara 3 (Perunggu / Bronze Medal)">🥉 Juara 3 (Perunggu)</option>
                                            <option value="Juara Harapan 1">🏅 Juara Harapan 1</option>
                                            <option value="Juara Harapan 2">🏅 Juara Harapan 2</option>
                                            <option value="Juara Favorit / Kategori Khusus">⭐ Juara Favorit / Khusus</option>
                                            <option value="Finalis / Lolos Seleksi">🎯 Finalis / Lolos Seleksi</option>
                                            <option value="Peserta / Partisipan Aktif">🎖️ Peserta / Partisipan</option>
                                        </select>
                                    </div>

                                    <!-- Tingkat Wilayah (SIMKATMAWA) -->
                                    <div class="sm:col-span-2">
                                        <label class="block text-[11px] font-bold text-gray-700 mb-1">
                                            Tingkat Wilayah <span class="text-rose-500">*</span>
                                        </label>
                                        <select :name="'prestasies[' + prIdx + '][tingkat]'"
                                                x-model="prestasiItem.tingkat"
                                                required
                                                class="w-full rounded-lg border-gray-300 text-xs focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                                            <option value="Internasional">Internasional</option>
                                            <option value="Nasional">Nasional</option>
                                            <option value="Wilayah / LLDIKTI">Wilayah / LLDIKTI</option>
                                            <option value="Provinsi / Daerah">Provinsi / Daerah</option>
                                            <option value="Internal Perguruan Tinggi">Internal PT</option>
                                        </select>
                                    </div>

                                    <!-- Nama Mahasiswa / Tim Peraih -->
                                    <div class="sm:col-span-3">
                                        <label class="block text-[11px] font-bold text-gray-700 mb-1">
                                            Nama Mahasiswa / Tim Peraih
                                        </label>
                                        <input type="text"
                                               :name="'prestasies[' + prIdx + '][nama_peraih]'"
                                               x-model="prestasiItem.nama_peraih"
                                               placeholder="Contoh: Budi Santoso / Tim A"
                                               class="w-full rounded-lg border-gray-300 text-xs focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Input Pendukung: Jumlah Peserta & Evaluasi -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-gray-100">
                    <div>
                        <label for="jumlah_peserta" class="block text-xs font-bold uppercase text-gray-700 mb-1.5">
                            Jumlah Peserta Riil yang Mengikuti Kegiatan
                        </label>
                        <input type="number" id="jumlah_peserta" name="jumlah_peserta"
                               min="0" max="50"
                               x-model.number="jumlah_peserta"
                               @input="updatePesertaFromCount()"
                               placeholder="Contoh: 3"
                               class="w-full rounded-xl border-gray-300 text-xs font-mono font-bold focus:ring-indigo-500 focus:border-indigo-500">
                        <p class="text-[11px] text-indigo-600 font-semibold mt-1">
                            💡 Mengisi angka ini akan otomatis memunculkan formulir input Nama &amp; NPM di Bagian 2 di bawah.
                        </p>
                    </div>

                    <div>
                        <label for="prestasi_ringkas" class="block text-xs font-bold uppercase text-gray-700 mb-1.5">
                            Keterangan Ringkasan Prestasi (Opsional)
                        </label>
                        <input type="text" id="prestasi_ringkas" name="prestasi"
                               x-model="prestasi"
                               placeholder="Otomatis terisi jika tabel di atas diisi, atau ketik 'Kegiatan Non-Lomba'"
                               class="w-full rounded-xl border-gray-300 text-xs focus:ring-indigo-500 focus:border-indigo-500">
                        <p class="text-[11px] text-gray-500 mt-1">Digunakan sebagai teks ringkasan untuk rekapitulasi data.</p>
                    </div>

                    <div class="md:col-span-2">
                        <label for="ringkasan_kegiatan" class="block text-xs font-bold uppercase text-gray-700 mb-1.5">
                            Ringkasan Pelaksanaan &amp; Evaluasi Kegiatan (Opsional)
                        </label>
                        <textarea id="ringkasan_kegiatan" name="ringkasan_kegiatan" rows="3"
                                  placeholder="Ceritakan secara singkat jalannya kegiatan, kendala yang dihadapi, serta evaluasi untuk perbaikan ke depan..."
                                  class="w-full rounded-xl border-gray-300 text-xs focus:ring-indigo-500 focus:border-indigo-500">{{ old('ringkasan_kegiatan', $ajuan->lpj->ringkasan_kegiatan ?? '') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- BAGIAN 2: DATA MAHASISWA PESERTA (SIMKATMAWA) -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 sm:p-8">
                <div class="border-b border-gray-100 pb-4 mb-5 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-md">Bagian 2</span>
                        <h3 class="font-bold text-gray-900 text-lg mt-1">Data Mahasiswa Peserta / Kontingen (SIMKATMAWA)</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Dibutuhkan untuk borang akreditasi dan pemeringkatan SIMKATMAWA Kemendikbudristek.</p>
                    </div>
                    <button type="button" @click="addPeserta()"
                            class="inline-flex items-center px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold rounded-xl text-xs transition border border-emerald-200 gap-1 self-start sm:self-auto">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        <span>+ Tambah Mahasiswa Peserta</span>
                    </button>
                </div>

                <template x-if="pesertas.length === 0">
                    <div class="bg-slate-50 border-2 border-dashed border-gray-200 rounded-2xl p-8 text-center">
                        <div class="w-12 h-12 rounded-full bg-slate-100 text-gray-400 flex items-center justify-center mx-auto mb-2 font-bold text-lg">
                            👥
                        </div>
                        <p class="text-xs font-bold text-gray-700">Belum Ada Baris Peserta</p>
                        <p class="text-[11px] text-gray-500 mt-0.5">
                            Isi <strong>Jumlah Peserta Riil</strong> di Bagian 1 (misal ketik <code>3</code>) atau klik tombol <strong>"+ Tambah Mahasiswa Peserta"</strong> di atas.
                        </p>
                    </div>
                </template>

                <div class="space-y-3" x-show="pesertas.length > 0">
                    <template x-for="(peserta, index) in pesertas" :key="index">
                        <div class="bg-slate-50/80 border border-slate-200 rounded-xl p-3.5 transition hover:border-indigo-300">
                            <div class="flex items-center justify-between gap-2 mb-2">
                                <span class="text-xs font-bold text-slate-700 flex items-center gap-1.5">
                                    <span class="w-5 h-5 rounded-full bg-indigo-100 text-indigo-800 text-[10px] font-bold flex items-center justify-center" x-text="index + 1"></span>
                                    <span>Peserta / Anggota Ke-<span x-text="index + 1"></span></span>
                                </span>
                                <button type="button" @click="removePeserta(index)"
                                        class="text-rose-500 hover:text-rose-700 text-xs font-bold transition flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    <span>Hapus</span>
                                </button>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                                <div class="sm:col-span-5">
                                    <label class="block text-[11px] font-semibold text-gray-600 mb-1">Nama Lengkap Mahasiswa</label>
                                    <input type="text"
                                           :name="'pesertas[' + index + '][nama]'"
                                           x-model="peserta.nama"
                                           placeholder="Contoh: Muhammad Farhan"
                                           class="w-full rounded-lg border-gray-300 text-xs focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                <div class="sm:col-span-4">
                                    <label class="block text-[11px] font-semibold text-gray-600 mb-1">NIM / NPM</label>
                                    <input type="text"
                                           :name="'pesertas[' + index + '][npm]'"
                                           x-model="peserta.npm"
                                           placeholder="Contoh: 2105101001"
                                           class="w-full rounded-lg border-gray-300 text-xs font-mono focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                <div class="sm:col-span-3">
                                    <label class="block text-[11px] font-semibold text-gray-600 mb-1">Peran / Keterangan</label>
                                    <input type="text"
                                           :name="'pesertas[' + index + '][peran]'"
                                           x-model="peserta.peran"
                                           placeholder="Ketua Tim / Anggota"
                                           class="w-full rounded-lg border-gray-300 text-xs focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- BAGIAN 3: REALISASI ANGGARAN (KEUANGAN) -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 sm:p-8">
                <div class="border-b border-gray-100 pb-4 mb-5 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-amber-600 bg-amber-50 px-2.5 py-1 rounded-md">Bagian 3</span>
                        <h3 class="font-bold text-gray-900 text-lg mt-1">Realisasi Anggaran &amp; Penggunaan Biaya</h3>
                    </div>
                    <span class="text-xs text-gray-400">Pertanggungjawaban Keuangan</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                        <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider block">Pagu Anggaran Awal</span>
                        <span class="text-base font-black font-mono text-gray-800 mt-1 block" x-text="formatRupiah(pagu_disetujui)"></span>
                        <span class="text-[10px] text-gray-400 mt-0.5 block">Sesuai persetujuan Kemahasiswaan</span>
                    </div>

                    <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-4">
                        <label for="realisasi_anggaran" class="text-[11px] font-bold text-indigo-900 uppercase tracking-wider block mb-1">
                            Realisasi Biaya Terpakai (Rp)
                        </label>
                        <input type="number" id="realisasi_anggaran" name="realisasi_anggaran"
                               x-model.number="realisasi_anggaran"
                               min="0" step="1000"
                               class="w-full rounded-lg border-indigo-300 text-xs font-mono font-bold text-indigo-900 focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="text-[10px] text-indigo-600 mt-1 block">Total pengeluaran riil kegiatan</span>
                    </div>

                    <div class="border rounded-xl p-4 transition"
                         :class="sisaAnggaran() >= 0 ? 'bg-emerald-50 border-emerald-200 text-emerald-900' : 'bg-rose-50 border-rose-200 text-rose-900'">
                        <span class="text-[11px] font-semibold uppercase tracking-wider block"
                              x-text="sisaAnggaran() >= 0 ? 'Sisa Dana / Efisiensi (+)' : 'Defisit Anggaran (-)'"></span>
                        <span class="text-base font-black font-mono mt-1 block" x-text="formatRupiah(Math.abs(sisaAnggaran()))"></span>
                        <span class="text-[10px] mt-0.5 block"
                              x-text="sisaAnggaran() >= 0 ? 'Dana efisien tersisa' : 'Biaya melebihi pagu awal'"></span>
                    </div>
                </div>
            </div>

            <!-- BAGIAN 4: UNGGAH 4 BERKAS BUKTI & DOKUMENTASI (TIDAK MANDATORI) -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 sm:p-8">
                <div class="border-b border-gray-100 pb-4 mb-5 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-purple-600 bg-purple-50 px-2.5 py-1 rounded-md">Bagian 4</span>
                        <h3 class="font-bold text-gray-900 text-lg mt-1">Unggah Dokumen Bukti & Sertifikat Kegiatan</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Semua berkas bersifat <strong>tidak mandatori (opsional)</strong>. Format: PDF, JPG, PNG (maks. 10MB per berkas).</p>
                    </div>
                    <span class="text-xs px-2.5 py-1 bg-slate-100 text-slate-700 font-bold rounded-md">4 Berkas Opsional</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- 1. Sertifikat Perlombaan -->
                    <div class="border border-gray-200 rounded-2xl p-5 hover:border-indigo-300 transition bg-slate-50/50">
                        <div class="flex items-center gap-2.5 mb-2">
                            <span class="text-xl">📜</span>
                            <div>
                                <h4 class="font-bold text-xs text-gray-800">1. Sertifikat Perlombaan / Penghargaan</h4>
                                <span class="text-[10px] text-gray-400">Scan sertifikat juara atau keikutsertaan</span>
                            </div>
                        </div>
                        @if($ajuan->lpj && $ajuan->lpj->file_sertifikat)
                            <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-2 text-xs text-emerald-800 flex items-center justify-between mb-2">
                                <span class="truncate max-w-[200px] font-semibold">✓ {{ $ajuan->lpj->file_sertifikat_nama }}</span>
                                <a href="{{ route('mahasiswa.lpj.download', ['id' => $ajuan->id, 'type' => 'sertifikat']) }}" class="text-emerald-700 font-bold underline hover:text-emerald-900 text-[11px]">Unduh</a>
                            </div>
                        @endif
                        <input type="file" name="file_sertifikat" accept=".pdf,.jpg,.jpeg,.png"
                               class="w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                    </div>

                    <!-- 2. Foto Kegiatan -->
                    <div class="border border-gray-200 rounded-2xl p-5 hover:border-indigo-300 transition bg-slate-50/50">
                        <div class="flex items-center gap-2.5 mb-2">
                            <span class="text-xl">📸</span>
                            <div>
                                <h4 class="font-bold text-xs text-gray-800">2. Foto Suasana Kegiatan</h4>
                                <span class="text-[10px] text-gray-400">Dokumentasi jalannya kegiatan / lomba</span>
                            </div>
                        </div>
                        @if($ajuan->lpj && $ajuan->lpj->foto_kegiatan)
                            <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-2 text-xs text-emerald-800 flex items-center justify-between mb-2">
                                <span class="truncate max-w-[200px] font-semibold">✓ {{ $ajuan->lpj->foto_kegiatan_nama }}</span>
                                <a href="{{ route('mahasiswa.lpj.download', ['id' => $ajuan->id, 'type' => 'foto_kegiatan']) }}" class="text-emerald-700 font-bold underline hover:text-emerald-900 text-[11px]">Unduh</a>
                            </div>
                        @endif
                        <input type="file" name="foto_kegiatan" accept=".jpg,.jpeg,.png,.pdf"
                               class="w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                    </div>

                    <!-- 3. Foto Penyerahan Sertifikat / Piala -->
                    <div class="border border-gray-200 rounded-2xl p-5 hover:border-indigo-300 transition bg-slate-50/50">
                        <div class="flex items-center gap-2.5 mb-2">
                            <span class="text-xl">🏆</span>
                            <div>
                                <h4 class="font-bold text-xs text-gray-800">3. Foto Penyerahan Sertifikat / Piala Juara</h4>
                                <span class="text-[10px] text-gray-400">Momen penyerahan penghargaan di podium</span>
                            </div>
                        </div>
                        @if($ajuan->lpj && $ajuan->lpj->foto_penyerahan)
                            <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-2 text-xs text-emerald-800 flex items-center justify-between mb-2">
                                <span class="truncate max-w-[200px] font-semibold">✓ {{ $ajuan->lpj->foto_penyerahan_nama }}</span>
                                <a href="{{ route('mahasiswa.lpj.download', ['id' => $ajuan->id, 'type' => 'foto_penyerahan']) }}" class="text-emerald-700 font-bold underline hover:text-emerald-900 text-[11px]">Unduh</a>
                            </div>
                        @endif
                        <input type="file" name="foto_penyerahan" accept=".jpg,.jpeg,.png,.pdf"
                               class="w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                    </div>

                    <!-- 4. Surat Undangan Kegiatan -->
                    <div class="border border-gray-200 rounded-2xl p-5 hover:border-indigo-300 transition bg-slate-50/50">
                        <div class="flex items-center gap-2.5 mb-2">
                            <span class="text-xl">✉️</span>
                            <div>
                                <h4 class="font-bold text-xs text-gray-800">4. Surat Undangan Kegiatan Resmi</h4>
                                <span class="text-[10px] text-gray-400">Surat undangan resmi dari pihak penyelenggara</span>
                            </div>
                        </div>
                        @if($ajuan->lpj && $ajuan->lpj->surat_undangan)
                            <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-2 text-xs text-emerald-800 flex items-center justify-between mb-2">
                                <span class="truncate max-w-[200px] font-semibold">✓ {{ $ajuan->lpj->surat_undangan_nama }}</span>
                                <a href="{{ route('mahasiswa.lpj.download', ['id' => $ajuan->id, 'type' => 'surat_undangan']) }}" class="text-emerald-700 font-bold underline hover:text-emerald-900 text-[11px]">Unduh</a>
                            </div>
                        @endif
                        <input type="file" name="surat_undangan" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                               class="w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                    </div>
                </div>
            </div>

            <!-- Submit Button Bar -->
            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('mahasiswa.show', $ajuan->id) }}"
                   class="px-5 py-2.5 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-50 font-bold text-xs transition">
                    Batal
                </a>
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-200 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>Simpan & Kirim Laporan LPJ</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Alpine.js logic for LPJ form & SIMKATMAWA participants -->
    <script>
        function lpjForm() {
            return {
                prestasi: @json(old('prestasi', $ajuan->lpj->prestasi ?? '')),
                prestasies: @json($prestasiesInitial),
                jumlah_peserta: {{ (int) old('jumlah_peserta', $ajuan->lpj->jumlah_peserta ?? ($ajuan->lpj ? $ajuan->lpj->pesertas->count() : 0)) }},
                pagu_disetujui: {{ (float) $paguDisetujui }},
                realisasi_anggaran: {{ (float) old('realisasi_anggaran', $ajuan->lpj->realisasi_anggaran ?? $paguDisetujui) }},
                pesertas: @json($pesertasInitial),

                init() {
                    if (this.pesertas.length === 0 && this.jumlah_peserta > 0) {
                        this.updatePesertaFromCount();
                    }
                },

                addPrestasi(defaultPeringkat = 'Juara 1 (Emas / Gold Medal)') {
                    this.prestasies.push({
                        kategori_lomba: '',
                        peringkat_juara: defaultPeringkat,
                        tingkat: 'Nasional',
                        nama_peraih: ''
                    });
                },

                removePrestasi(index) {
                    this.prestasies.splice(index, 1);
                },

                updatePesertaFromCount() {
                    let count = parseInt(this.jumlah_peserta) || 0;
                    if (count < 0) count = 0;
                    if (count > 50) count = 50;

                    while (this.pesertas.length < count) {
                        this.pesertas.push({
                            nama: '',
                            npm: '',
                            peran: this.pesertas.length === 0 ? 'Ketua Tim' : 'Anggota Tim'
                        });
                    }
                    while (this.pesertas.length > count) {
                        this.pesertas.pop();
                    }
                },

                addPeserta() {
                    this.pesertas.push({
                        nama: '',
                        npm: '',
                        peran: this.pesertas.length === 0 ? 'Ketua Tim' : 'Anggota Tim'
                    });
                    this.jumlah_peserta = this.pesertas.length;
                },

                removePeserta(index) {
                    this.pesertas.splice(index, 1);
                    this.jumlah_peserta = this.pesertas.length;
                },

                formatRupiah(num) {
                    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(num || 0);
                },

                sisaAnggaran() {
                    return this.pagu_disetujui - (Number(this.realisasi_anggaran) || 0);
                }
            };
        }
    </script>
</x-app-layout>
