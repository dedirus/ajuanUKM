<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center gap-2 text-xs text-gray-500 mb-1">
                    <a href="{{ route('kemahasiswaan.users.index') }}" class="hover:text-indigo-600 transition">Data Pengguna</a>
                    <span>/</span>
                    <span class="text-gray-900 font-medium">Edit Akun: {{ $user->name }}</span>
                </div>
                <h2 class="font-black text-2xl text-gray-900 leading-tight">
                    Edit Akun Pengguna: {{ $user->name }}
                </h2>
            </div>
            <a href="{{ route('kemahasiswaan.users.index') }}"
               class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition">
                ← Kembali
            </a>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 sm:p-8"
             x-data="{ role: '{{ old('role', $user->role) }}' }">

            @if($user->id === Auth::id())
                <div class="mb-6 p-4 bg-indigo-50 border border-indigo-200 rounded-xl text-xs text-indigo-800 flex items-center gap-2">
                    <svg class="w-4 h-4 text-indigo-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span><strong>Catatan:</strong> Ini adalah akun Anda yang sedang aktif digunakan saat ini. Pastikan tidak mengganti peran secara tidak sengaja agar tidak kehilangan akses Kemahasiswaan.</span>
                </div>
            @endif

            <form action="{{ route('kemahasiswaan.users.update', $user->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Role / Peran Akun -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                        Peran / Hak Akses Pengguna <span class="text-rose-500">*</span>
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <label class="relative flex flex-col p-4 border rounded-xl cursor-pointer transition"
                               :class="role === 'mahasiswa' ? 'border-emerald-500 bg-emerald-50/40 ring-2 ring-emerald-500/20' : 'border-gray-200 hover:bg-gray-50'">
                            <input type="radio" name="role" value="mahasiswa" x-model="role" class="sr-only">
                            <span class="font-bold text-sm text-gray-900 flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                Mahasiswa
                            </span>
                            <span class="text-xs text-gray-500 mt-1">Perwakilan pengurus UKM untuk membuat usulan kegiatan.</span>
                        </label>

                        <label class="relative flex flex-col p-4 border rounded-xl cursor-pointer transition"
                               :class="role === 'pembina' ? 'border-purple-500 bg-purple-50/40 ring-2 ring-purple-500/20' : 'border-gray-200 hover:bg-gray-50'">
                            <input type="radio" name="role" value="pembina" x-model="role" class="sr-only">
                            <span class="font-bold text-sm text-gray-900 flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-purple-500"></span>
                                Dosen Pembina
                            </span>
                            <span class="text-xs text-gray-500 mt-1">Dosen yang memverifikasi ajuan dari UKM binaannya.</span>
                        </label>

                        <label class="relative flex flex-col p-4 border rounded-xl cursor-pointer transition"
                               :class="role === 'kemahasiswaan' ? 'border-amber-500 bg-amber-50/40 ring-2 ring-amber-500/20' : 'border-gray-200 hover:bg-gray-50'">
                            <input type="radio" name="role" value="kemahasiswaan" x-model="role" class="sr-only">
                            <span class="font-bold text-sm text-gray-900 flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                                Kemahasiswaan
                            </span>
                            <span class="text-xs text-gray-500 mt-1">Staf/Biro kemahasiswaan dengan akses verifikasi seluruh kampus.</span>
                        </label>
                    </div>
                    @error('role')
                        <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- UKM Terkait (Khusus Mahasiswa dan Pembina) -->
                <div x-show="role === 'mahasiswa' || role === 'pembina'" x-transition>
                    <label for="ukm_id" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                        Pilih Unit Kegiatan Mahasiswa (UKM) <span class="text-rose-500">*</span>
                    </label>
                    <select name="ukm_id" id="ukm_id"
                            class="w-full rounded-xl border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500 shadow-sm @error('ukm_id') border-rose-500 @enderror">
                        <option value="">-- Pilih Organisasi UKM --</option>
                        @foreach($ukms as $ukm)
                            <option value="{{ $ukm->id }}" {{ old('ukm_id', $user->ukm_id) == $ukm->id ? 'selected' : '' }}>
                                [{{ $ukm->kode }}] {{ $ukm->nama_ukm }} ({{ $ukm->kategori }})
                            </option>
                        @endforeach
                    </select>
                    @error('ukm_id')
                        <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nama Lengkap & Email -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                            Nama Lengkap & Gelar <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}"
                               required
                               class="w-full rounded-xl border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500 shadow-sm @error('name') border-rose-500 @enderror">
                        @error('name')
                            <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                            Alamat Email (Login) <span class="text-rose-500">*</span>
                        </label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                               required
                               class="w-full rounded-xl border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500 shadow-sm @error('email') border-rose-500 @enderror">
                        @error('email')
                            <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- NIM / NIP & No HP -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="nim_nip" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                            <span x-text="role === 'mahasiswa' ? 'Nomor Induk Mahasiswa (NIM)' : 'Nomor Induk Pegawai (NIP / NIDN)'"></span>
                        </label>
                        <input type="text" name="nim_nip" id="nim_nip" value="{{ old('nim_nip', $user->nim_nip) }}"
                               class="w-full rounded-xl border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500 shadow-sm @error('nim_nip') border-rose-500 @enderror">
                        @error('nim_nip')
                            <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="no_hp" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                            Nomor WhatsApp / HP
                        </label>
                        <input type="text" name="no_hp" id="no_hp" value="{{ old('no_hp', $user->no_hp) }}"
                               class="w-full rounded-xl border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500 shadow-sm @error('no_hp') border-rose-500 @enderror">
                        @error('no_hp')
                            <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Reset Kata Sandi Baru (Opsional) -->
                <div class="bg-gray-50/70 p-5 rounded-2xl border border-gray-200/80 space-y-4">
                    <div>
                        <h4 class="text-xs font-bold text-gray-800 uppercase tracking-wider">Ubah Kata Sandi Baru (Opsional)</h4>
                        <p class="text-xs text-gray-500 mt-0.5">Biarkan kolom kata sandi kosong jika tidak ingin mengubah password akun ini.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="block text-xs font-semibold text-gray-700 mb-1">
                                Kata Sandi Baru
                            </label>
                            <input type="password" name="password" id="password"
                                   placeholder="Kosongkan jika tidak diubah"
                                   class="w-full rounded-xl border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500 shadow-sm @error('password') border-rose-500 @enderror">
                            @error('password')
                                <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-xs font-semibold text-gray-700 mb-1">
                                Ulangi Kata Sandi Baru
                            </label>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                   placeholder="Ulangi kata sandi baru"
                                   class="w-full rounded-xl border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
                        </div>
                    </div>
                </div>

                <!-- Tombol Submit -->
                <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                    <a href="{{ route('kemahasiswaan.users.index') }}"
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
