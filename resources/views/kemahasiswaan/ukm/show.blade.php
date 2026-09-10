<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs text-gray-500 mb-1">
                    <a href="{{ route('kemahasiswaan.ukm.index') }}" class="hover:text-indigo-600 transition">Master UKM</a>
                    <span>/</span>
                    <span class="text-gray-900 font-medium">Profil UKM</span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="px-2.5 py-1 rounded-lg text-sm font-bold bg-indigo-50 text-indigo-700 border border-indigo-100 font-mono">
                        {{ $ukm->kode }}
                    </span>
                    <h2 class="font-black text-2xl text-gray-900 leading-tight">
                        {{ $ukm->nama_ukm }}
                    </h2>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('kemahasiswaan.ukm.edit', $ukm->id) }}"
                   class="inline-flex items-center px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold rounded-xl shadow-sm transition">
                    <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit UKM
                </a>
                <a href="{{ route('kemahasiswaan.laporan.detail', $ukm->id) }}"
                   class="inline-flex items-center px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-sm font-semibold rounded-xl transition">
                    Laporan Kinerja
                </a>
                <a href="{{ route('kemahasiswaan.ukm.index') }}"
                   class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition">
                    ← Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        <!-- Stat Overview -->
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div class="bg-white rounded-2xl p-4 border border-gray-200 shadow-sm">
                <span class="text-xs font-semibold uppercase text-gray-400 block">Kategori</span>
                <span class="text-base font-bold text-gray-900 mt-1 block">{{ $ukm->kategori }}</span>
            </div>
            <div class="bg-white rounded-2xl p-4 border border-gray-200 shadow-sm">
                <span class="text-xs font-semibold uppercase text-gray-400 block">Akun Mahasiswa</span>
                <span class="text-base font-bold text-blue-700 mt-1 block">{{ $mahasiswaUsers->count() }} Orang</span>
            </div>
            <div class="bg-white rounded-2xl p-4 border border-gray-200 shadow-sm">
                <span class="text-xs font-semibold uppercase text-gray-400 block">Total Usulan Kegiatan</span>
                <span class="text-base font-bold text-indigo-700 mt-1 block">{{ $ukm->ajuanKegiatans->count() }} Usulan ({{ $totalApproved }} Disetujui)</span>
            </div>
            <div class="bg-white rounded-2xl p-4 border border-gray-200 shadow-sm">
                <span class="text-xs font-semibold uppercase text-gray-400 block">Realisasi Dana Disetujui</span>
                <span class="text-base font-bold text-emerald-700 mt-1 block">Rp {{ number_format($totalDanaDisetujui, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Kolom Kiri: Profil & Dosen Pembina -->
            <div class="space-y-6">
                <!-- Profil Organisasi -->
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
                    <h3 class="font-bold text-gray-900 text-base mb-3 pb-2 border-b border-gray-100 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Informasi Kelembagaan
                    </h3>
                    <dl class="space-y-3 text-sm">
                        <div>
                            <dt class="text-xs font-semibold text-gray-400 uppercase">Kode UKM</dt>
                            <dd class="font-mono font-bold text-indigo-700">{{ $ukm->kode }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold text-gray-400 uppercase">Nama Lembaga</dt>
                            <dd class="font-medium text-gray-900">{{ $ukm->nama_ukm }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold text-gray-400 uppercase">Kategori</dt>
                            <dd class="font-medium text-gray-800">{{ $ukm->kategori }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold text-gray-400 uppercase">Deskripsi</dt>
                            <dd class="text-gray-600 text-xs leading-relaxed mt-1">
                                {{ $ukm->deskripsi ?: 'Belum ada deskripsi profil untuk UKM ini.' }}
                            </dd>
                        </div>
                    </dl>
                </div>

                <!-- Dosen Pembina -->
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
                    <h3 class="font-bold text-gray-900 text-base mb-3 pb-2 border-b border-gray-100 flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Dosen Pembina Terdaftar
                    </h3>
                    @if($pembinaUsers->count() > 0)
                        <div class="space-y-3">
                            @foreach($pembinaUsers as $pembina)
                                <div class="p-3 bg-emerald-50/50 rounded-xl border border-emerald-100">
                                    <div class="font-bold text-gray-900 text-sm">{{ $pembina->name }}</div>
                                    <div class="text-xs text-gray-500 mt-0.5">NIP/NIDN: {{ $pembina->nim_nip ?: '-' }}</div>
                                    <div class="text-xs text-gray-500">Email: {{ $pembina->email }}</div>
                                    @if($pembina->no_hp)
                                        <div class="text-xs text-gray-500">No. HP: {{ $pembina->no_hp }}</div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @elseif($ukm->pembina_nama)
                        <div class="p-3 bg-gray-50 rounded-xl border border-gray-200">
                            <div class="font-bold text-gray-900 text-sm">{{ $ukm->pembina_nama }}</div>
                            <div class="text-xs text-amber-600 mt-1">(Nama manual tercatat, akun pembina belum dikaitkan)</div>
                        </div>
                    @else
                        <p class="text-xs text-gray-400 italic">Belum ada dosen pembina yang ditugaskan pada UKM ini.</p>
                    @endif
                </div>

                <!-- Anggota Mahasiswa Terdaftar -->
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
                    <div class="flex items-center justify-between mb-3 pb-2 border-b border-gray-100">
                        <h3 class="font-bold text-gray-900 text-base flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            Akun Mahasiswa ({{ $mahasiswaUsers->count() }})
                        </h3>
                        <a href="{{ route('kemahasiswaan.users.create') }}" class="text-xs text-indigo-600 hover:underline font-semibold">
                            + Tambah Akun
                        </a>
                    </div>
                    @if($mahasiswaUsers->count() > 0)
                        <div class="divide-y divide-gray-100 max-h-72 overflow-y-auto pr-1">
                            @foreach($mahasiswaUsers as $mhs)
                                <div class="py-2.5">
                                    <div class="font-bold text-gray-900 text-xs">{{ $mhs->name }}</div>
                                    <div class="text-[11px] text-gray-500">NIM: {{ $mhs->nim_nip ?: '-' }} | {{ $mhs->email }}</div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-xs text-gray-400 italic">Belum ada akun mahasiswa yang terhubung dengan UKM ini.</p>
                    @endif
                </div>
            </div>

            <!-- Kolom Kanan: Riwayat Usulan Kegiatan -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="font-bold text-gray-900 text-base">Riwayat Usulan Kegiatan Mahasiswa</h3>
                        <span class="text-xs text-gray-500">Total {{ $ukm->ajuanKegiatans->count() }} Usulan</span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs text-gray-600">
                            <thead class="bg-gray-50 text-[11px] font-semibold text-gray-500 uppercase tracking-wider border-b border-gray-200">
                                <tr>
                                    <th class="px-4 py-3">Nama Kegiatan & Pengusul</th>
                                    <th class="px-4 py-3">Tanggal</th>
                                    <th class="px-4 py-3">Status Akhir</th>
                                    <th class="px-4 py-3 text-right">Anggaran</th>
                                    <th class="px-4 py-3 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($ukm->ajuanKegiatans as $ajuan)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-4 py-3">
                                            <span class="font-bold text-gray-900 block text-xs">{{ $ajuan->nama_kegiatan }}</span>
                                            <span class="text-gray-400 text-[11px]">Oleh: {{ $ajuan->user?->name ?? 'Mahasiswa' }}</span>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            {{ \Carbon\Carbon::parse($ajuan->tanggal_kegiatan)->format('d/m/Y') }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            @if($ajuan->status_akhir === 'disetujui')
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                                    ✓ Disetujui
                                                </span>
                                            @elseif($ajuan->status_akhir === 'ditolak')
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">
                                                    ✕ Ditolak
                                                </span>
                                            @elseif($ajuan->status_akhir === 'revisi')
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                                    ⚠️ Perlu Revisi
                                                </span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800">
                                                    Proses
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-right whitespace-nowrap font-mono font-medium">
                                            @if($ajuan->status_akhir === 'disetujui')
                                                <span class="text-emerald-700 font-bold">Rp {{ number_format($ajuan->total_disetujui, 0, ',', '.') }}</span>
                                            @else
                                                <span class="text-gray-600">Rp {{ number_format($ajuan->total_ajuan, 0, ',', '.') }}</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-right whitespace-nowrap">
                                            <div class="flex items-center justify-end gap-1.5">
                                                <a href="{{ route('kemahasiswaan.show', $ajuan->id) }}"
                                                   class="px-2 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold rounded text-[11px] transition">
                                                    Periksa
                                                </a>
                                                <a href="{{ route('kemahasiswaan.cetak-pengesahan', $ajuan->id) }}"
                                                   target="_blank"
                                                   class="px-2 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded text-[11px] transition">
                                                    PDF
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-8 text-center text-gray-400">
                                            Belum ada usulan kegiatan yang diajukan oleh UKM ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
