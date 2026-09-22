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
                <div><span class="text-gray-400">Jenis Kegiatan:</span> <span class="font-bold text-purple-700 bg-purple-50 px-2 py-0.5 rounded-md border border-purple-200">{{ $ajuan->jenis_kegiatan ?? 'Lainnya' }}</span></div>
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

        <!-- Tab Navigation Menu -->
        <div class="bg-white rounded-2xl p-2 border border-gray-200 shadow-sm flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
            <div class="flex items-center p-1 bg-slate-100 rounded-xl gap-1 w-full sm:w-auto">
                <!-- Tab 1: Verifikasi & Penyesuaian Anggaran -->
                <button type="button"
                        @click="activeTab = 'anggaran'"
                        :class="activeTab === 'anggaran' ? 'bg-white text-indigo-700 shadow-sm font-black' : 'text-gray-600 hover:text-gray-900 font-semibold'"
                        class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-xs sm:text-sm transition-all duration-150 cursor-pointer">
                    <span class="text-base">📋</span>
                    <span>Verifikasi &amp; Penyesuaian Anggaran</span>
                    <span class="text-[10px] px-2 py-0.5 rounded-full font-bold font-mono"
                          :class="activeTab === 'anggaran' ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-gray-200 text-gray-700'">
                        {{ $ajuan->details->count() }} Item
                    </span>
                </button>

                <!-- Tab 2: Riwayat Jejak & Aktivitas Usulan -->
                <button type="button"
                        @click="activeTab = 'riwayat'"
                        :class="activeTab === 'riwayat' ? 'bg-white text-indigo-700 shadow-sm font-black' : 'text-gray-600 hover:text-gray-900 font-semibold'"
                        class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-xs sm:text-sm transition-all duration-150 cursor-pointer">
                    <span class="text-base">⏱️</span>
                    <span>Riwayat Jejak &amp; Aktivitas Usulan</span>
                    <span class="text-[10px] px-2 py-0.5 rounded-full font-bold"
                          :class="activeTab === 'riwayat' ? 'bg-indigo-100 text-indigo-800 border border-indigo-200' : 'bg-gray-200 text-gray-700'">
                        {{ $ajuan->riwayats->count() }}
                    </span>
                </button>
            </div>

            <!-- Tab Info Indicator -->
            <div class="text-xs text-gray-500 hidden md:flex items-center gap-2 px-3">
                <template x-if="activeTab === 'anggaran'">
                    <span class="text-gray-500 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                        Pemeriksaan rincian RAB, edit kebutuhan, dan keputusan persetujuan akhir
                    </span>
                </template>
                <template x-if="activeTab === 'riwayat'">
                    <span class="text-gray-500 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                        Linimasa riwayat status dan catatan verifikator dari awal pengajuan
                    </span>
                </template>
            </div>
        </div>

        <!-- KONTEN TAB 1: Verifikasi & Penyesuaian Anggaran -->
        <div x-show="activeTab === 'anggaran'" class="space-y-6">
            <!-- Form 1: Pemeriksaan Checklist Tabel Detail Kebutuhan Kegiatan -->
            <form action="{{ route('kemahasiswaan.update-details', $ajuan->id) }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            @csrf

            <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-xs font-bold uppercase tracking-wider text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-md border border-indigo-100">
                            Verifikasi & Penyesuaian Anggaran
                        </span>
                        <template x-if="isEditingRab">
                            <span class="text-xs font-bold text-amber-800 bg-amber-100 px-2 py-0.5 rounded-md border border-amber-300 flex items-center gap-1 animate-pulse">
                                <span>✏️ Mode Edit RAB Aktif</span>
                            </span>
                        </template>
                    </div>
                    <h3 class="font-bold text-gray-900 text-lg flex items-center gap-2">
                        <span>Pemeriksaan & Pengeditan Detail RAB Kegiatan</span>
                    </h3>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Periksa atau sesuaikan item anggaran (nama akun, barang, harga, jumlah, satuan, durasi) serta tentukan checklist persetujuan.
                    </p>
                </div>

                <!-- Batch Actions & Toggle Edit Mode -->
                <div class="flex items-center flex-wrap gap-2">
                    <!-- Tombol Toggle Edit Mode -->
                    <button type="button" @click="isEditingRab = !isEditingRab"
                            :class="isEditingRab ? 'bg-amber-600 hover:bg-amber-700 text-white shadow-amber-200' : 'bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200'"
                            class="inline-flex items-center px-3.5 py-1.5 rounded-xl text-xs font-bold transition shadow-sm gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        <span x-text="isEditingRab ? '✓ Tutup Mode Edit (Tampilan Ringkas)' : '✏️ Aktifkan Edit Tabel RAB'"></span>
                    </button>

                    <!-- Batch Actions Checklist -->
                    <button type="button" @click="checkAll('disetujui')" class="px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 rounded-xl text-xs font-bold transition border border-emerald-200">
                        ✓ Semua Disetujui
                    </button>
                    <button type="button" @click="checkAll('tidak_disetujui')" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-800 rounded-xl text-xs font-bold transition border border-rose-200">
                        ✗ Semua Tdk Disetujui
                    </button>
                </div>
            </div>

            <!-- Banner Informatif Saat Mode Edit Aktif -->
            <div x-show="isEditingRab" x-transition class="p-4 bg-amber-50 border-b border-amber-200 text-amber-900 text-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-amber-200 text-amber-900 flex items-center justify-center font-bold text-sm shrink-0">
                        ✏️
                    </div>
                    <div>
                        <span class="font-bold">Mode Edit Rincian RAB sedang aktif:</span>
                        <p class="text-amber-800 mt-0.5">Anda dapat mengubah langsung nama akun, nama barang, harga satuan, jumlah, durasi, satuan, atau menambah/menghapus baris item. Perhitungan total terupdate otomatis.</p>
                    </div>
                </div>
                <button type="button" @click="addRow()" class="inline-flex items-center px-3 py-1.5 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-xs font-bold shadow-sm transition gap-1 shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <span>+ Tambah Baris Baru</span>
                </button>
            </div>

            <!-- Tabel Detail dengan Checklist & Edit Interaktif -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-xs">
                    <thead class="bg-slate-100 text-slate-700 font-bold uppercase tracking-wider">
                        <tr>
                            <th class="px-3 py-3 text-center w-12">No</th>
                            <th class="px-3 py-3 text-left min-w-[130px]">Nama Akun</th>
                            <th class="px-3 py-3 text-left min-w-[180px]">Nama Barang / Item</th>
                            <th class="px-3 py-3 text-right min-w-[120px]">Harga Satuan</th>
                            <th class="px-2 py-3 text-center min-w-[65px]">Jumlah</th>
                            <th class="px-2 py-3 text-center min-w-[85px]">Satuan</th>
                            <th class="px-2 py-3 text-center min-w-[65px]">Durasi</th>
                            <th class="px-2 py-3 text-center min-w-[85px]">Sat. Durasi</th>
                            <th class="px-3 py-3 text-right min-w-[120px]">Total</th>
                            <!-- Kolom Keterangan berupa Checklist Disetujui / Tidak Disetujui -->
                            <th class="px-4 py-3 text-center min-w-[210px] bg-indigo-50/70 text-indigo-900 border-x border-indigo-100">
                                Keterangan (Persetujuan)
                            </th>
                            <th class="px-3 py-3 text-left min-w-[180px]">Catatan Verifikator</th>
                            <th x-show="isEditingRab" class="px-2 py-3 text-center w-12 text-rose-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        <template x-for="(item, index) in items" :key="index">
                            <tr class="hover:bg-slate-50 transition" :class="isEditingRab ? 'bg-amber-50/10' : ''">
                                <!-- No -->
                                <td class="px-3 py-3 text-center font-bold text-gray-400">
                                    <span x-text="index + 1"></span>
                                    <input type="hidden" :name="'items[' + index + '][id]'" :value="item.id">
                                </td>

                                <!-- Nama Akun -->
                                <td class="px-3 py-3">
                                    <div x-show="!isEditingRab" class="font-semibold text-gray-800" x-text="item.nama_akun"></div>
                                    <input x-show="isEditingRab" type="text" :name="'items[' + index + '][nama_akun]'" x-model="item.nama_akun"
                                           placeholder="Nama Akun"
                                           class="w-full rounded-lg border-gray-300 text-xs focus:ring-indigo-500 focus:border-indigo-500 p-1.5 font-semibold">
                                </td>

                                <!-- Nama Barang -->
                                <td class="px-3 py-3">
                                    <div x-show="!isEditingRab" class="font-medium text-gray-900" x-text="item.nama_barang"></div>
                                    <input x-show="isEditingRab" type="text" :name="'items[' + index + '][nama_barang]'" x-model="item.nama_barang"
                                           placeholder="Nama Barang / Item"
                                           class="w-full rounded-lg border-gray-300 text-xs focus:ring-indigo-500 focus:border-indigo-500 p-1.5 font-medium">
                                </td>

                                <!-- Harga Satuan -->
                                <td class="px-3 py-3">
                                    <div x-show="!isEditingRab" class="text-right font-mono text-gray-700" x-text="formatRupiah(item.harga_satuan)"></div>
                                    <input x-show="isEditingRab" type="number" min="0" step="1000" :name="'items[' + index + '][harga_satuan]'" x-model.number="item.harga_satuan"
                                           class="w-full text-right font-mono rounded-lg border-gray-300 text-xs focus:ring-indigo-500 focus:border-indigo-500 p-1.5">
                                </td>

                                <!-- Jumlah -->
                                <td class="px-2 py-3">
                                    <div x-show="!isEditingRab" class="text-center font-mono font-semibold text-gray-900" x-text="item.jumlah"></div>
                                    <input x-show="isEditingRab" type="number" min="1" step="1" :name="'items[' + index + '][jumlah]'" x-model.number="item.jumlah"
                                           class="w-full text-center font-mono rounded-lg border-gray-300 text-xs focus:ring-indigo-500 focus:border-indigo-500 p-1.5 font-semibold">
                                </td>

                                <!-- Satuan -->
                                <td class="px-2 py-3">
                                    <div x-show="!isEditingRab" class="text-center font-medium text-gray-700" x-text="item.satuan || '-'"></div>
                                    <select x-show="isEditingRab" :name="'items[' + index + '][satuan]'" x-model="item.satuan"
                                            class="w-full text-center rounded-lg border-gray-300 text-xs focus:ring-indigo-500 focus:border-indigo-500 p-1.5 bg-white font-medium">
                                        @foreach($satuans as $satuan)
                                            <option value="{{ $satuan->nama_satuan }}">{{ $satuan->nama_satuan }}</option>
                                        @endforeach
                                    </select>
                                </td>

                                <!-- Durasi -->
                                <td class="px-2 py-3">
                                    <div x-show="!isEditingRab" class="text-center font-mono text-gray-700" x-text="item.durasi"></div>
                                    <input x-show="isEditingRab" type="number" min="1" step="0.5" :name="'items[' + index + '][durasi]'" x-model.number="item.durasi"
                                           class="w-full text-center font-mono rounded-lg border-gray-300 text-xs focus:ring-indigo-500 focus:border-indigo-500 p-1.5">
                                </td>

                                <!-- Satuan Durasi -->
                                <td class="px-2 py-3">
                                    <div x-show="!isEditingRab" class="text-center font-medium text-gray-700" x-text="item.satuan_durasi || 'Hari'"></div>
                                    <select x-show="isEditingRab" :name="'items[' + index + '][satuan_durasi]'" x-model="item.satuan_durasi"
                                            class="w-full text-center rounded-lg border-gray-300 text-xs focus:ring-indigo-500 focus:border-indigo-500 p-1.5 bg-white font-medium">
                                        @foreach($satuans as $satuan)
                                            <option value="{{ $satuan->nama_satuan }}">{{ $satuan->nama_satuan }}</option>
                                        @endforeach
                                    </select>
                                </td>

                                <!-- Total -->
                                <td class="px-3 py-3 text-right font-mono font-bold text-indigo-700" x-text="formatRupiah(calculateRowTotal(item))"></td>

                                <!-- Kolom Keterangan: Checklist Disetujui / Tidak Disetujui -->
                                <td class="px-4 py-3 bg-indigo-50/20 border-x border-indigo-100">
                                    <div class="flex items-center justify-center space-x-2">
                                        <!-- Opsi Disetujui -->
                                        <label class="inline-flex items-center px-2 py-1 rounded-lg border cursor-pointer transition text-xs font-semibold"
                                               :class="item.status_keterangan === 'disetujui' ? 'bg-emerald-100 border-emerald-400 text-emerald-900' : 'bg-white border-gray-200 text-gray-600 hover:border-emerald-300'">
                                            <input type="radio" :name="'items[' + index + '][status_keterangan]'" value="disetujui"
                                                   x-model="item.status_keterangan"
                                                   class="text-emerald-600 focus:ring-emerald-500 w-3.5 h-3.5">
                                            <span class="ms-1">✓ Disetujui</span>
                                        </label>

                                        <!-- Opsi Tidak Disetujui -->
                                        <label class="inline-flex items-center px-2 py-1 rounded-lg border cursor-pointer transition text-xs font-semibold"
                                               :class="item.status_keterangan === 'tidak_disetujui' ? 'bg-rose-100 border-rose-400 text-rose-900' : 'bg-white border-gray-200 text-gray-600 hover:border-rose-300'">
                                            <input type="radio" :name="'items[' + index + '][status_keterangan]'" value="tidak_disetujui"
                                                   x-model="item.status_keterangan"
                                                   class="text-rose-600 focus:ring-rose-500 w-3.5 h-3.5">
                                            <span class="ms-1">✗ Tdk Disetujui</span>
                                        </label>
                                    </div>
                                </td>

                                <!-- Catatan Verifikator per baris -->
                                <td class="px-3 py-3">
                                    <input type="text" :name="'items[' + index + '][catatan]'" x-model="item.catatan"
                                           placeholder="Tulis catatan penyesuaian..."
                                           class="w-full rounded-lg border-gray-300 text-xs focus:ring-indigo-500 focus:border-indigo-500 p-1.5">
                                </td>

                                <!-- Tombol Hapus Baris (hanya saat mode edit aktif) -->
                                <td x-show="isEditingRab" class="px-2 py-3 text-center">
                                    <button type="button" @click="removeRow(index)"
                                            class="p-1.5 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-lg transition"
                                            title="Hapus baris kebutuhan ini">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                    <tfoot class="bg-slate-50 border-t-2 border-gray-200 font-bold text-xs text-gray-800">
                        <tr>
                            <td colspan="8" class="px-4 py-3 text-right uppercase tracking-wider text-gray-600">
                                Total Anggaran Usulan Kegiatan:
                            </td>
                            <td class="px-4 py-3 text-right text-sm text-indigo-700 font-mono font-black" x-text="formatRupiah(grandTotal())">
                                Rp {{ number_format($ajuan->total_anggaran, 0, ',', '.') }}
                            </td>
                            <td :colspan="isEditingRab ? 3 : 2" class="px-4 py-3 text-right">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-sm transition gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                                    <span>Simpan Perubahan RAB & Checklist</span>
                                </button>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Tombol Tambah Baris di Bawah Tabel saat Mode Edit Aktif -->
            <div x-show="isEditingRab" x-transition class="p-3.5 bg-slate-50 border-t border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <button type="button" @click="addRow()" class="inline-flex items-center px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition gap-1.5 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <span>+ Tambah Baris Kebutuhan Baru</span>
                </button>
                <span class="text-xs text-gray-500 italic">Total <span class="font-bold text-indigo-600" x-text="items.length"></span> baris item kebutuhan tercatat</span>
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

        @if($ajuan->lpj)
            <!-- Form 3 / Card: Pemeriksaan & Verifikasi Laporan Pertanggungjawaban (LPJ) -->
            <div class="bg-white rounded-2xl p-6 sm:p-8 border-2 border-purple-200 shadow-md">
                <div class="border-b border-gray-100 pb-4 mb-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-purple-700 bg-purple-100 px-2.5 py-1 rounded-md">
                            Pasca-Pelaksanaan Kegiatan
                        </span>
                        <h3 class="font-extrabold text-xl text-gray-900 mt-2">
                            Laporan Pertanggungjawaban (LPJ) & Data SIMKATMAWA
                        </h3>
                        <p class="text-xs text-gray-500 mt-0.5">
                            Periksa hasil capaian prestasi, nama peserta kontingen SIMKATMAWA, realisasi anggaran, serta 4 bukti berkas kegiatan yang diunggah mahasiswa.
                        </p>
                    </div>
                    <div>
                        @if($ajuan->status_akhir === 'sudah_dilaksanakan')
                            <span class="px-3.5 py-1.5 text-xs font-bold rounded-xl bg-emerald-100 text-emerald-800 border border-emerald-300 flex items-center gap-1.5">
                                <span>✓ LPJ Telah Disetujui & Selesai Dilaksanakan</span>
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

                <!-- Tabel Capaian Prestasi & Medali (Multi-Juara / SIMKATMAWA) -->
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
                            <span class="text-[10px] bg-purple-50 text-purple-700 px-2 py-0.5 rounded-full font-bold">{{ $ajuan->lpj->pesertas->count() }} orang</span>
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
                <div class="mb-6">
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
                                    <a href="{{ route('kemahasiswaan.lpj.download', ['id' => $ajuan->id, 'type' => 'sertifikat']) }}"
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
                                    <a href="{{ route('kemahasiswaan.lpj.download', ['id' => $ajuan->id, 'type' => 'foto_kegiatan']) }}"
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
                                    <a href="{{ route('kemahasiswaan.lpj.download', ['id' => $ajuan->id, 'type' => 'foto_penyerahan']) }}"
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
                                    <a href="{{ route('kemahasiswaan.lpj.download', ['id' => $ajuan->id, 'type' => 'surat_undangan']) }}"
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

                <!-- Formulir Keputusan Verifikasi LPJ -->
                <form action="{{ route('kemahasiswaan.verify-lpj', $ajuan->id) }}" method="POST" class="pt-4 border-t border-gray-200">
                    @csrf

                    <div class="mb-4">
                        <label for="catatan_lpj" class="block text-xs font-bold uppercase text-gray-700 mb-1.5">
                            Catatan Verifikator LPJ:
                        </label>
                        <textarea id="catatan_lpj" name="catatan" rows="2"
                                  placeholder="Tuliskan catatan verifikasi, apresiasi, atau rincian berkas yang perlu diperbaiki jika meminta revisi..."
                                  class="w-full rounded-xl border-gray-300 text-xs focus:ring-indigo-500 focus:border-indigo-500">{{ old('catatan', $ajuan->lpj->catatan_verifikasi_lpj) }}</textarea>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="text-xs text-gray-500">
                            @if($ajuan->status_akhir === 'sudah_dilaksanakan')
                                <span class="text-emerald-700 font-bold flex items-center gap-1">
                                    ✓ LPJ telah disahkan pada {{ $ajuan->lpj->verified_lpj_at ? $ajuan->lpj->verified_lpj_at->isoFormat('D MMMM Y, HH:mm') : '-' }}
                                </span>
                            @else
                                <span class="text-purple-700 font-medium">Silakan tentukan persetujuan LPJ di samping.</span>
                            @endif
                        </div>

                        <div class="flex items-center space-x-2.5">
                            <!-- Tombol Minta Revisi LPJ -->
                            <button type="submit" name="status" value="revisi" onclick="return confirm('Apakah Anda yakin ingin meminta perbaikan/revisi LPJ kepada mahasiswa?')"
                                    class="px-4 py-2 bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-300 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                                <span>⚠️ Minta Perbaikan LPJ</span>
                            </button>

                            <!-- Tombol Setujui LPJ -->
                            <button type="submit" name="status" value="disetujui" onclick="return confirm('Apakah Anda yakin untuk MENYETUJUI LPJ ini? Status kegiatan akan tuntas dan berubah menjadi Sudah Dilaksanakan.')"
                                    class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white rounded-xl text-xs font-black shadow-md shadow-emerald-200 transition flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                <span>✓ Setujui LPJ (Sudah Dilaksanakan)</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        @endif
        </div>

        <!-- KONTEN TAB 2: Riwayat Jejak & Aktivitas Usulan -->
        <div x-show="activeTab === 'riwayat'" x-cloak class="space-y-6">
            <!-- Timeline Riwayat Jejak Usulan Kegiatan -->
            <x-timeline-riwayat :riwayats="$ajuan->riwayats" />
        </div>
    </div>

    <!-- Alpine.js logic for checklist toggles & RAB editing -->
    <script>
        function kemahasiswaanReview() {
            return {
                activeTab: new URLSearchParams(window.location.search).get('tab') || (window.location.hash === '#riwayat' ? 'riwayat' : 'anggaran'),
                isEditingRab: false,
                items: [
                    @foreach($ajuan->details as $item)
                    {
                        id: {{ $item->id }},
                        nama_akun: @json($item->nama_akun ?? ''),
                        nama_barang: @json($item->nama_barang ?? ''),
                        harga_satuan: {{ (float) $item->harga_satuan }},
                        jumlah: {{ (float) $item->jumlah }},
                        satuan: @json($item->satuan ?? ''),
                        durasi: {{ (float) ($item->durasi ?: 1) }},
                        satuan_durasi: @json($item->satuan_durasi ?? 'Hari'),
                        status_keterangan: @json($item->status_keterangan ?: 'disetujui'),
                        catatan: @json($item->catatan ?? '')
                    },
                    @endforeach
                ],
                addRow() {
                    this.items.push({
                        id: '',
                        nama_akun: 'Perlengkapan',
                        nama_barang: '',
                        harga_satuan: 0,
                        jumlah: 1,
                        satuan: 'Pcs',
                        durasi: 1,
                        satuan_durasi: 'Hari',
                        status_keterangan: 'disetujui',
                        catatan: ''
                    });
                },
                removeRow(index) {
                    if (this.items.length <= 1) {
                        alert('Tabel minimal harus memiliki satu baris rincian kebutuhan.');
                        return;
                    }
                    if (confirm('Hapus baris kebutuhan ini dari daftar?')) {
                        this.items.splice(index, 1);
                    }
                },
                calculateRowTotal(item) {
                    const harga = parseFloat(item.harga_satuan) || 0;
                    const jumlah = parseFloat(item.jumlah) || 0;
                    const durasi = parseFloat(item.durasi) || 1;
                    return harga * jumlah * durasi;
                },
                grandTotal() {
                    return this.items.reduce((sum, item) => sum + this.calculateRowTotal(item), 0);
                },
                formatRupiah(number) {
                    return 'Rp ' + Number(number || 0).toLocaleString('id-ID');
                },
                checkAll(value) {
                    this.items.forEach(item => {
                        item.status_keterangan = value;
                    });
                }
            }
        }
    </script>
</x-app-layout>
