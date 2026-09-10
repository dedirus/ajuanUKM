<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="text-center mb-6">
        <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Login Ajuan<span class="text-indigo-600">UKM</span></h1>
        <p class="text-xs text-gray-500 mt-1">Sistem Pengajuan & Verifikasi Kegiatan Mahasiswa</p>
    </div>

    <form method="POST" action="{{ route('login') }}" id="loginForm">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email Kampus')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="nama@univ.ac.id" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Ingat Saya') }}</span>
            </label>
        </div>

        <div class="mt-5">
            <button type="submit" class="w-full justify-center inline-flex items-center px-4 py-2.5 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-md shadow-indigo-200">
                Masuk ke Sistem
            </button>
        </div>
    </form>

    <!-- Quick Demo Accounts Switcher -->
    <div class="mt-8 pt-6 border-t border-gray-200" x-data="{
        fill(email) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = 'password';
        }
    }">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-bold uppercase tracking-wider text-gray-500">Akun Uji Coba Cepat (1-Klik)</span>
            <span class="text-[11px] text-gray-400">Password: password</span>
        </div>

        <div class="space-y-2">
            <!-- Kemahasiswaan -->
            <button type="button" @click="fill('kemahasiswaan@univ.ac.id')" class="w-full text-left p-2.5 rounded-lg border border-amber-200 bg-amber-50/70 hover:bg-amber-100 transition flex items-center justify-between group">
                <div>
                    <div class="text-xs font-bold text-amber-900 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-amber-500 inline-block"></span>
                        Kemahasiswaan Universitas
                    </div>
                    <div class="text-[11px] text-amber-700">kemahasiswaan@univ.ac.id</div>
                </div>
                <span class="text-[10px] bg-amber-200/80 text-amber-900 px-2 py-0.5 rounded font-medium group-hover:bg-amber-300">Pilih</span>
            </button>

            <!-- Pembina -->
            <div class="grid grid-cols-2 gap-2">
                <button type="button" @click="fill('pembina.robotika@univ.ac.id')" class="text-left p-2 rounded-lg border border-purple-200 bg-purple-50/70 hover:bg-purple-100 transition group">
                    <div class="text-xs font-bold text-purple-900 flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-purple-500 inline-block"></span>
                        Pembina Robotika
                    </div>
                    <div class="text-[10px] text-purple-700 truncate">pembina.robotika@...</div>
                </button>

                <button type="button" @click="fill('pembina.musik@univ.ac.id')" class="text-left p-2 rounded-lg border border-purple-200 bg-purple-50/70 hover:bg-purple-100 transition group">
                    <div class="text-xs font-bold text-purple-900 flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-purple-500 inline-block"></span>
                        Pembina Musik
                    </div>
                    <div class="text-[10px] text-purple-700 truncate">pembina.musik@...</div>
                </button>
            </div>

            <!-- Mahasiswa -->
            <div class="grid grid-cols-2 gap-2">
                <button type="button" @click="fill('mahasiswa.robotika@univ.ac.id')" class="text-left p-2 rounded-lg border border-emerald-200 bg-emerald-50/70 hover:bg-emerald-100 transition group">
                    <div class="text-xs font-bold text-emerald-900 flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block"></span>
                        Mhs Robotika
                    </div>
                    <div class="text-[10px] text-emerald-700 truncate">mahasiswa.robotika@...</div>
                </button>

                <button type="button" @click="fill('mahasiswa.musik@univ.ac.id')" class="text-left p-2 rounded-lg border border-emerald-200 bg-emerald-50/70 hover:bg-emerald-100 transition group">
                    <div class="text-xs font-bold text-emerald-900 flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block"></span>
                        Mhs Musik
                    </div>
                    <div class="text-[10px] text-emerald-700 truncate">mahasiswa.musik@...</div>
                </button>
            </div>
        </div>
    </div>
</x-guest-layout>
