<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-black text-2xl text-gray-900 leading-tight flex items-center gap-2">
                    <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                    Master Data Satuan Barang & Jasa (RAB)
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">
                    Kelola daftar pilihan satuan dinamis yang dapat dipilih mahasiswa saat menyusun Rencana Anggaran Biaya (RAB).
                </p>
            </div>
            <div class="flex items-center gap-3">
                <button type="button" @click="$dispatch('open-modal', 'modal-tambah-satuan')"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-md shadow-indigo-200 transition">
                    <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Satuan Baru
                </button>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6" x-data="{ editModal: false, editData: { id: null, nama_satuan: '', keterangan: '', is_aktif: true } }">

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center gap-3 text-emerald-800 text-sm">
                <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl flex items-center gap-3 text-rose-800 text-sm">
                <svg class="w-5 h-5 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Summary Metric Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            <div class="bg-white rounded-2xl p-5 border border-gray-200 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 block">Total Satuan</span>
                    <span class="text-2xl font-black text-gray-900">{{ $totalSatuan }} <span class="text-xs font-medium text-gray-500">Item</span></span>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-gray-200 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 block">Satuan Aktif</span>
                    <span class="text-2xl font-black text-emerald-600">{{ $totalAktif }} <span class="text-xs font-medium text-gray-500">Ditampilkan</span></span>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-gray-200 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                    </svg>
                </div>
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 block">Non-Aktif</span>
                    <span class="text-2xl font-black text-amber-600">{{ $totalNonAktif }} <span class="text-xs font-medium text-gray-500">Disembunyikan</span></span>
                </div>
            </div>
        </div>

        <!-- Filter & Search Bar -->
        <div class="bg-white rounded-2xl p-5 border border-gray-200 shadow-sm">
            <form action="{{ route('kemahasiswaan.satuan.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4 items-center justify-between">
                <div class="flex-1 w-full flex flex-col sm:flex-row gap-3">
                    <div class="relative flex-1">
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Cari nama satuan atau keterangan..."
                               class="w-full pl-10 pr-4 py-2 rounded-xl border border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>

                    <select name="status" class="rounded-xl border border-gray-200 text-sm py-2 px-3 focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Semua Status</option>
                        <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif Saja</option>
                        <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Non-Aktif</option>
                    </select>
                </div>

                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-indigo-50 text-indigo-700 font-semibold rounded-xl text-sm hover:bg-indigo-100 transition">
                        Filter
                    </button>
                    @if(request()->hasAny(['search', 'status']))
                        <a href="{{ route('kemahasiswaan.satuan.index') }}" class="w-full sm:w-auto px-4 py-2 bg-gray-100 text-gray-600 font-semibold rounded-xl text-sm hover:bg-gray-200 transition text-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tabel Master Satuan -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-slate-50 text-slate-700 font-bold uppercase tracking-wider text-xs">
                        <tr>
                            <th class="px-5 py-3.5 text-center w-14">No</th>
                            <th class="px-5 py-3.5 text-left">Nama Satuan</th>
                            <th class="px-5 py-3.5 text-left">Keterangan / Deskripsi</th>
                            <th class="px-5 py-3.5 text-center">Status Form</th>
                            <th class="px-5 py-3.5 text-center">Pemakaian di RAB</th>
                            <th class="px-5 py-3.5 text-center w-36">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($satuans as $index => $item)
                            <tr class="hover:bg-indigo-50/20 transition">
                                <td class="px-5 py-3.5 text-center font-bold text-gray-400">
                                    {{ $satuans->firstItem() + $index }}
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-bold bg-indigo-50 text-indigo-800 border border-indigo-200">
                                        {{ $item->nama_satuan }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-gray-600">
                                    {{ $item->keterangan ?? '-' }}
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    @if($item->is_aktif)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                            ✓ Aktif (Tampil)
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                                            ✕ Non-Aktif
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    <span class="font-mono font-bold text-xs {{ $item->usage_count > 0 ? 'text-indigo-600' : 'text-gray-400' }}">
                                        {{ $item->usage_count }} item
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    <div class="flex items-center justify-center space-x-2">
                                        <!-- Tombol Edit -->
                                        <button type="button"
                                                @click="editData = { id: {{ $item->id }}, nama_satuan: '{{ addslashes($item->nama_satuan) }}', keterangan: '{{ addslashes($item->keterangan ?? '') }}', is_aktif: {{ $item->is_aktif ? 'true' : 'false' }} }; editModal = true;"
                                                class="px-2.5 py-1 text-xs font-semibold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition">
                                            Edit
                                        </button>

                                        <!-- Tombol Hapus -->
                                        <form action="{{ route('kemahasiswaan.satuan.destroy', $item->id) }}" method="POST"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus satuan {{ $item->nama_satuan }}?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="px-2.5 py-1 text-xs font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 rounded-lg transition">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-gray-500 italic">
                                    Belum ada data satuan yang cocok dengan filter pencarian.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($satuans->hasPages())
                <div class="p-4 border-t border-gray-100">
                    {{ $satuans->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Modal Tambah Satuan -->
    <x-modal name="modal-tambah-satuan" focusable>
        <form action="{{ route('kemahasiswaan.satuan.store') }}" method="POST" class="p-6">
            @csrf
            <h2 class="text-lg font-bold text-gray-900 mb-1 flex items-center gap-2">
                <span class="w-7 h-7 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-sm">+</span>
                Tambah Master Satuan Baru
            </h2>
            <p class="text-xs text-gray-500 mb-5">
                Satuan yang ditambahkan akan langsung muncul pada pilihan dropdown formulir RAB mahasiswa.
            </p>

            <div class="space-y-4">
                <div>
                    <label for="create_nama_satuan" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                        Nama Satuan <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="create_nama_satuan" name="nama_satuan" required placeholder="Misal: Rim, Botol, Lusin, Rim, dsb."
                           class="w-full rounded-xl border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="create_keterangan" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                        Keterangan (Opsional)
                    </label>
                    <input type="text" id="create_keterangan" name="keterangan" placeholder="Penjelasan pemakaian satuan"
                           class="w-full rounded-xl border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div class="pt-2">
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_aktif" value="1" checked class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                        <span class="ms-2 text-xs font-semibold text-gray-700">Aktifkan satuan (Tampilkan pada dropdown formulir mahasiswa)</span>
                    </label>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" @click="$dispatch('close')" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-xl transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow transition">
                    Simpan Satuan
                </button>
            </div>
        </form>
    </x-modal>

    <!-- Modal Edit Satuan -->
    <div x-data x-show="editModal" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="editModal = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full p-6">
                <form :action="'{{ url('kemahasiswaan/master-satuan') }}/' + editData.id" method="POST">
                    @csrf
                    @method('PUT')

                    <h2 class="text-lg font-bold text-gray-900 mb-1 flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-sm">✎</span>
                        Edit Master Satuan
                    </h2>
                    <p class="text-xs text-gray-500 mb-5">
                        Perbarui nama atau status aktif satuan.
                    </p>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                                Nama Satuan <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="nama_satuan" x-model="editData.nama_satuan" required
                                   class="w-full rounded-xl border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                                Keterangan (Opsional)
                            </label>
                            <input type="text" name="keterangan" x-model="editData.keterangan"
                                   class="w-full rounded-xl border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div class="pt-2">
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_aktif" value="1" :checked="editData.is_aktif"
                                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                <span class="ms-2 text-xs font-semibold text-gray-700">Aktifkan satuan (Tampilkan pada dropdown formulir mahasiswa)</span>
                            </label>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" @click="editModal = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-xl transition">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow transition">
                            Perbarui Satuan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
