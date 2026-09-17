<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-black text-2xl text-gray-900 leading-tight">
                    Verifikasi Ajuan Kegiatan Pembina UKM
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">
                    UKM Binaan: <span class="font-bold text-purple-700">{{ $ukm->nama_ukm ?? 'UKM Belum Terhubung' }}</span>
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-bold">
                    Role: Pembina UKM
                </span>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        <!-- Welcome Banner Pembina -->
        <div class="bg-gradient-to-r from-purple-700 to-indigo-700 rounded-2xl p-6 text-white shadow-lg flex flex-col md:flex-row items-center justify-between gap-4">
            <div>
                <div class="text-xs uppercase tracking-widest text-purple-200 font-bold mb-1">Selamat Datang, Bapak/Ibu Pembina</div>
                <h3 class="text-2xl font-black">{{ $user->name }}</h3>
                <p class="text-purple-100 text-sm mt-1 max-w-2xl">
                    Berikut daftar ajuan kegiatan mahasiswa dari <strong>{{ $ukm->nama_ukm }}</strong> yang memerlukan peninjauan dan verifikasi dari Anda sebagai Pembina UKM.
                </p>
            </div>
            <div class="bg-white/10 backdrop-blur-md rounded-xl p-4 border border-white/20 text-center min-w-[180px]">
                <div class="text-xs text-purple-200 uppercase font-semibold">Total Ajuan Masuk</div>
                <div class="text-3xl font-black text-white mt-1">{{ $ajuans->total() }}</div>
            </div>
        </div>

        <!-- Tabel Ajuan Kegiatan Pembina -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h4 class="font-bold text-gray-800 text-lg">Tabel Ajuan Kegiatan UKM</h4>
                    <p class="text-xs text-gray-500">Daftar usulan kegiatan yang diajukan oleh mahasiswa pengurus UKM.</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-slate-50 text-gray-600 text-xs uppercase font-bold tracking-wider">
                        <tr>
                            <th class="px-6 py-3.5 text-left w-12">No</th>
                            <th class="px-6 py-3.5 text-left">Nama Kegiatan</th>
                            <th class="px-6 py-3.5 text-left">Tanggal Kegiatan</th>
                            <th class="px-6 py-3.5 text-left">Total Jumlah Ajuan</th>
                            <th class="px-6 py-3.5 text-left">Keterangan</th>
                            <th class="px-6 py-3.5 text-left">Catatan</th>
                            <th class="px-6 py-3.5 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($ajuans as $index => $ajuan)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="px-6 py-4 font-bold text-gray-400">
                                    {{ $ajuans->firstItem() + $index }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900 text-base">
                                        {{ $ajuan->nama_kegiatan }}
                                    </div>
                                    <div class="text-xs text-gray-400 mt-0.5 flex items-center gap-1.5">
                                        <span class="font-semibold text-purple-700 bg-purple-50 px-1.5 py-0.5 rounded border border-purple-200 text-[10px]">{{ $ajuan->jenis_kegiatan ?? 'Lainnya' }}</span>
                                        <span>• Oleh: <span class="text-gray-600 font-medium">{{ $ajuan->user->name ?? 'Mahasiswa' }}</span></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-700 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($ajuan->tanggal_kegiatan)->isoFormat('D MMMM Y') }}
                                </td>
                                <td class="px-6 py-4 font-mono font-bold text-indigo-700 whitespace-nowrap">
                                    Rp {{ number_format($ajuan->total_anggaran, 0, ',', '.') }}
                                </td>
                                <!-- Kolom Keterangan dengan Status dan Link Detail sesuai ketentuan soal -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="space-y-1.5">
                                        <div>
                                            @if($ajuan->status_pembina === 'disetujui')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                                    ✓ Disetujui
                                                </span>
                                            @elseif($ajuan->status_pembina === 'revisi')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                                                    ⚠️ Perlu Revisi
                                                </span>
                                            @elseif($ajuan->status_pembina === 'ditolak')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800">
                                                    ✗ Ditolak
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                                                    ⏳ Belum Disetujui
                                                </span>
                                            @endif
                                        </div>
                                        <div>
                                            <a href="{{ route('pembina.show', $ajuan->id) }}" class="inline-flex items-center text-xs font-bold text-indigo-600 hover:text-indigo-800 underline gap-1">
                                                <span>Detail Ajuan Kegiatan</span>
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                            </a>
                                        </div>
                                        <div>
                                            <a href="{{ route('pembina.cetak-pengesahan', $ajuan->id) }}" target="_blank" class="inline-flex items-center text-xs font-semibold text-emerald-600 hover:text-emerald-800 gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                                <span>Cetak PDF</span>
                                            </a>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-600 max-w-xs">
                                    {{ $ajuan->catatan_pembina ?? 'Belum ada catatan' }}
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <a href="{{ route('pembina.show', $ajuan->id) }}" class="inline-flex items-center px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-sm transition gap-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <span>Periksa & Verifikasi</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    <p class="font-medium text-gray-600">Belum ada ajuan kegiatan dari mahasiswa untuk UKM ini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($ajuans->hasPages())
                <div class="p-4 border-t border-gray-100">
                    {{ $ajuans->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
