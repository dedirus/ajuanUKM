<nav x-data="{ open: false }" class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-50">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo & Brand -->
                <div class="shrink-0 flex items-center space-x-3">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-blue-500 flex items-center justify-center text-white font-bold shadow-md shadow-indigo-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div class="leading-tight">
                            <span class="font-bold text-lg text-gray-900 tracking-tight block">Ajuan<span class="text-indigo-600">UKM</span></span>
                            <span class="text-xs text-gray-400 font-medium block">Sistem Usulan Kegiatan</span>
                        </div>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-6 sm:-my-px sm:ms-10 sm:flex">
                    @if(Auth::user()->role === 'mahasiswa')
                        <x-nav-link :href="route('mahasiswa.dashboard')" :active="request()->routeIs('mahasiswa.dashboard')">
                            <svg class="w-4 h-4 me-1.5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            Daftar Ajuan UKM
                        </x-nav-link>
                        <x-nav-link :href="route('mahasiswa.create')" :active="request()->routeIs('mahasiswa.create')">
                            <svg class="w-4 h-4 me-1.5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Ajukan Kegiatan Baru
                        </x-nav-link>
                    @elseif(Auth::user()->role === 'pembina')
                        <x-nav-link :href="route('pembina.dashboard')" :active="request()->routeIs('pembina.dashboard', 'pembina.show')">
                            <svg class="w-4 h-4 me-1.5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                            Verifikasi Ajuan UKM
                        </x-nav-link>
                    @elseif(Auth::user()->role === 'kemahasiswaan')
                        <x-nav-link :href="route('kemahasiswaan.dashboard')" :active="request()->routeIs('kemahasiswaan.dashboard', 'kemahasiswaan.show')">
                            <svg class="w-4 h-4 me-1.5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            Kelola Ajuan
                        </x-nav-link>
                        <x-nav-link :href="route('kemahasiswaan.laporan')" :active="request()->routeIs('kemahasiswaan.laporan*')">
                            <svg class="w-4 h-4 me-1.5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            Laporan & Statistik
                        </x-nav-link>

                        <!-- Menu Setting (Dropdown Master & User Management) -->
                        <div class="inline-flex items-center">
                            <x-dropdown align="left" width="64">
                                <x-slot name="trigger">
                                    <button type="button" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('kemahasiswaan.ukm.*', 'kemahasiswaan.satuan.*', 'kemahasiswaan.users.*') ? 'border-indigo-500 text-gray-900 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium leading-5 transition duration-150 ease-in-out h-16 focus:outline-none cursor-pointer">
                                        <svg class="w-4 h-4 me-1.5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <span>Setting</span>
                                        <svg class="ms-1.5 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    <!-- Header Group 1: Master Data -->
                                    <div class="px-3.5 py-1.5 text-[10px] font-bold uppercase tracking-wider text-gray-400 bg-gray-50 border-b border-gray-100">
                                        Master Data
                                    </div>
                                    <x-dropdown-link :href="route('kemahasiswaan.ukm.index')" class="flex items-center gap-2.5 py-2.5 {{ request()->routeIs('kemahasiswaan.ukm.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : '' }}">
                                        <div class="w-7 h-7 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center shrink-0">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                        </div>
                                        <div>
                                            <div class="text-xs font-bold text-gray-800">Master UKM</div>
                                            <div class="text-[11px] text-gray-400 font-normal">Daftar Unit Kegiatan Mahasiswa</div>
                                        </div>
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('kemahasiswaan.satuan.index')" class="flex items-center gap-2.5 py-2.5 {{ request()->routeIs('kemahasiswaan.satuan.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : '' }}">
                                        <div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                        </div>
                                        <div>
                                            <div class="text-xs font-bold text-gray-800">Master Satuan (RAB)</div>
                                            <div class="text-[11px] text-gray-400 font-normal">Pilihan Satuan Kebutuhan Anggaran</div>
                                        </div>
                                    </x-dropdown-link>

                                    <!-- Header Group 2: User Management -->
                                    <div class="px-3.5 py-1.5 text-[10px] font-bold uppercase tracking-wider text-gray-400 bg-gray-50 border-t border-b border-gray-100">
                                        User Management
                                    </div>
                                    <x-dropdown-link :href="route('kemahasiswaan.users.index')" class="flex items-center gap-2.5 py-2.5 {{ request()->routeIs('kemahasiswaan.users.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : '' }}">
                                        <div class="w-7 h-7 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center shrink-0">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                        </div>
                                        <div>
                                            <div class="text-xs font-bold text-gray-800">User Management</div>
                                            <div class="text-[11px] text-gray-400 font-normal">Data Pengguna & Hak Akses Akun</div>
                                        </div>
                                    </x-dropdown-link>
                                </x-slot>
                            </x-dropdown>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Settings & Profile -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 space-x-3">
                <!-- Role & UKM Badge -->
                <div class="flex items-center space-x-2">
                    @if(Auth::user()->role === 'mahasiswa')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                            Mahasiswa
                        </span>
                        @if(Auth::user()->ukm)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 max-w-xs truncate">
                                {{ Auth::user()->ukm->nama_ukm }}
                            </span>
                        @endif
                    @elseif(Auth::user()->role === 'pembina')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">
                            Pembina UKM
                        </span>
                        @if(Auth::user()->ukm)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800 max-w-xs truncate">
                                {{ Auth::user()->ukm->nama_ukm }}
                            </span>
                        @endif
                    @elseif(Auth::user()->role === 'kemahasiswaan')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                            Kemahasiswaan Universitas
                        </span>
                    @endif
                </div>

                <!-- User Dropdown -->
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-gray-200 text-sm leading-4 font-medium rounded-lg text-gray-700 bg-gray-50 hover:bg-gray-100 focus:outline-none transition ease-in-out duration-150">
                            <div class="font-semibold">{{ Auth::user()->name }}</div>
                            <div class="ms-2">
                                <svg class="fill-current h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profil Saya') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();" class="text-rose-600 hover:text-rose-700">
                                {{ __('Keluar (Log Out)') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t border-gray-200">
        <div class="pt-2 pb-3 space-y-1">
            @if(Auth::user()->role === 'mahasiswa')
                <x-responsive-nav-link :href="route('mahasiswa.dashboard')" :active="request()->routeIs('mahasiswa.dashboard')">
                    Daftar Ajuan UKM
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('mahasiswa.create')" :active="request()->routeIs('mahasiswa.create')">
                    Ajukan Kegiatan Baru
                </x-responsive-nav-link>
            @elseif(Auth::user()->role === 'pembina')
                <x-responsive-nav-link :href="route('pembina.dashboard')" :active="request()->routeIs('pembina.dashboard')">
                    Verifikasi Ajuan UKM
                </x-responsive-nav-link>
            @elseif(Auth::user()->role === 'kemahasiswaan')
                <x-responsive-nav-link :href="route('kemahasiswaan.dashboard')" :active="request()->routeIs('kemahasiswaan.dashboard')">
                    Kelola Ajuan Seluruh UKM
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('kemahasiswaan.laporan')" :active="request()->routeIs('kemahasiswaan.laporan*')">
                    Laporan & Statistik UKM
                </x-responsive-nav-link>

                <!-- Group Setting (Master & User Management) -->
                <div class="pt-2 pb-1 border-t border-gray-100 mt-2">
                    <div class="px-4 py-1.5 text-[11px] font-bold uppercase tracking-wider text-gray-400 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>Setting</span>
                    </div>
                    <div class="ps-2">
                        <x-responsive-nav-link :href="route('kemahasiswaan.ukm.index')" :active="request()->routeIs('kemahasiswaan.ukm.*')">
                            • Master Data UKM
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('kemahasiswaan.satuan.index')" :active="request()->routeIs('kemahasiswaan.satuan.*')">
                            • Master Satuan (RAB)
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('kemahasiswaan.users.index')" :active="request()->routeIs('kemahasiswaan.users.*')">
                            • User Management
                        </x-responsive-nav-link>
                    </div>
                </div>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                <div class="mt-1 text-xs text-indigo-600 font-semibold uppercase tracking-wider">
                    Role: {{ Auth::user()->role }} {{ Auth::user()->ukm ? '('.Auth::user()->ukm->nama_ukm.')' : '' }}
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profil') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();" class="text-rose-600">
                        {{ __('Keluar (Log Out)') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
