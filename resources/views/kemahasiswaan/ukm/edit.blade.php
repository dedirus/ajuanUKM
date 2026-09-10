<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center gap-2 text-xs text-gray-500 mb-1">
                    <a href="{{ route('kemahasiswaan.ukm.index') }}" class="hover:text-indigo-600 transition">Master UKM</a>
                    <span>/</span>
                    <a href="{{ route('kemahasiswaan.ukm.show', $ukm->id) }}" class="hover:text-indigo-600 transition">{{ $ukm->nama_ukm }}</a>
                    <span>/</span>
                    <span class="text-gray-900 font-medium">Edit UKM</span>
                </div>
                <h2 class="font-black text-2xl text-gray-900 leading-tight">
                    Edit Data UKM: {{ $ukm->nama_ukm }}
                </h2>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('kemahasiswaan.ukm.show', $ukm->id) }}"
                   class="px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-sm font-semibold rounded-xl transition">
                    Lihat Profil UKM
                </a>
                <a href="{{ route('kemahasiswaan.ukm.index') }}"
                   class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition">
                    ← Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 sm:p-8">
            <form action="{{ route('kemahasiswaan.ukm.update', $ukm->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Kode UKM & Nama UKM -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <div>
                        <label for="kode" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                            Kode UKM <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="kode" id="kode" value="{{ old('kode', $ukm->kode) }}"
                               required
                               class="w-full rounded-xl border-gray-300 text-sm font-mono uppercase focus:ring-indigo-500 focus:border-indigo-500 shadow-sm @error('kode') border-rose-500 @enderror">
                        <p class="text-xs text-gray-400 mt-1">Kode identitas unik huruf kapital.</p>
                        @error('kode')
                            <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label for="nama_ukm" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                            Nama Lengkap UKM <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="nama_ukm" id="nama_ukm" value="{{ old('nama_ukm', $ukm->nama_ukm) }}"
                               required
                               class="w-full rounded-xl border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500 shadow-sm @error('nama_ukm') border-rose-500 @enderror">
                        @error('nama_ukm')
                            <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Kategori Organisasi -->
                <div>
                    <label for="kategori" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                        Kategori Bidang Organisasi <span class="text-rose-500">*</span>
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <select name="kategori" id="kategori" required
                                class="w-full rounded-xl border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500 shadow-sm @error('kategori') border-rose-500 @enderror">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($commonCategories as $cat)
                                <option value="{{ $cat }}" {{ old('kategori', $ukm->kategori) == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                            @if(!in_array($ukm->kategori, $commonCategories))
                                <option value="{{ $ukm->kategori }}" selected>{{ $ukm->kategori }} (Custom)</option>
                            @endif
                        </select>
                        <input type="text" name="kategori_custom" id="kategori_custom"
                               placeholder="Atau ubah kategori custom..."
                               class="w-full rounded-xl border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500 shadow-sm"
                               oninput="if(this.value){ document.getElementById('kategori').value = this.value; }">
                    </div>
                    @error('kategori')
                        <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Penugasan Dosen Pembina -->
                <div class="bg-gray-50/70 p-5 rounded-2xl border border-gray-200/80 space-y-4">
                    <div>
                        <h4 class="text-xs font-bold text-gray-800 uppercase tracking-wider">Penugasan Dosen Pembina UKM</h4>
                        <p class="text-xs text-gray-500 mt-0.5">Pilih akun pembina terdaftar di sistem atau ubah nama pembina.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="pembina_user_id" class="block text-xs font-semibold text-gray-700 mb-1">
                                Akun Pembina Terdaftar
                            </label>
                            <select name="pembina_user_id" id="pembina_user_id"
                                    class="w-full rounded-xl border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
                                <option value="">-- Tetap Gunakan Nama Manual / Lepaskan --</option>
                                @foreach($pembinas as $pembina)
                                    <option value="{{ $pembina->id }}" {{ old('pembina_user_id', $assignedPembina?->id) == $pembina->id ? 'selected' : '' }}>
                                        {{ $pembina->name }} ({{ $pembina->email }}) {{ $pembina->ukm_id == $ukm->id ? '✓ Saat Ini' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="pembina_nama_manual" class="block text-xs font-semibold text-gray-700 mb-1">
                                Nama & Gelar Pembina
                            </label>
                            <input type="text" name="pembina_nama_manual" id="pembina_nama_manual"
                                   value="{{ old('pembina_nama_manual', $ukm->pembina_nama) }}"
                                   placeholder="Contoh: Dr. Ir. Hendra Setiawan, M.T."
                                   class="w-full rounded-xl border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
                        </div>
                    </div>
                </div>

                <!-- Deskripsi UKM -->
                <div>
                    <label for="deskripsi" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                        Deskripsi & Profil Singkat UKM
                    </label>
                    <textarea name="deskripsi" id="deskripsi" rows="3"
                              placeholder="Uraian visi, misi, atau fokus kegiatan UKM..."
                              class="w-full rounded-xl border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">{{ old('deskripsi', $ukm->deskripsi) }}</textarea>
                    @error('deskripsi')
                        <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tombol Submit -->
                <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                    <a href="{{ route('kemahasiswaan.ukm.index') }}"
                       class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition">
                        Batal
                    </a>
                    <button type="submit"
                            class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-md shadow-indigo-200 transition">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
