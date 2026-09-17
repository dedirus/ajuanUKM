@props(['riwayats'])

@php
    $statusLabels = [
        'draft' => 'Draft',
        'menunggu_pembina' => 'Menunggu Pembina',
        'revisi_pembina' => 'Revisi Pembina',
        'menunggu_kemahasiswaan' => 'Menunggu Kemahasiswaan',
        'revisi_kemahasiswaan' => 'Revisi Kemahasiswaan',
        'disetujui' => 'Disetujui',
        'ditolak' => 'Ditolak',
        'menunggu_verifikasi_lpj' => 'Verifikasi LPJ',
        'revisi_lpj' => 'Revisi LPJ',
        'sudah_dilaksanakan' => 'Selesai Dilaksanakan',
    ];
@endphp

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <!-- Header Kartu Timeline -->
    <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-gradient-to-r from-slate-50 to-white">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 text-lg shadow-sm">
                ⏱️
            </div>
            <div>
                <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
                    Riwayat Jejak &amp; Aktivitas Usulan
                    <span class="text-[11px] font-semibold bg-indigo-100/80 text-indigo-800 px-2.5 py-0.5 rounded-full">
                        {{ $riwayats->count() }} Aktivitas
                    </span>
                </h3>
                <p class="text-xs text-gray-500 mt-0.5">Catatan jejak kronologis proses pengajuan, verifikasi, hingga pelaporan LPJ</p>
            </div>
        </div>
        <div class="text-xs text-gray-400 font-medium">
            Urutan: <span class="font-semibold text-gray-600">Awal &rarr; Terkini</span>
        </div>
    </div>

    <!-- Isi Timeline -->
    <div class="p-6 sm:p-8">
        @if($riwayats->isEmpty())
            <div class="text-center py-10 text-gray-400">
                <div class="text-3xl mb-2">📋</div>
                <p class="text-sm font-medium">Belum ada riwayat aktivitas yang tercatat untuk usulan ini.</p>
            </div>
        @else
            <div class="flow-root">
                <ul role="list" class="-mb-8">
                    @foreach($riwayats as $index => $riwayat)
                        @php
                            $isLast = $loop->last;
                            $statusLabelSebelumnya = $riwayat->status_sebelumnya ? ($statusLabels[$riwayat->status_sebelumnya] ?? ucfirst($riwayat->status_sebelumnya)) : null;
                            $statusLabelSesudah = $riwayat->status_sesudah ? ($statusLabels[$riwayat->status_sesudah] ?? ucfirst($riwayat->status_sesudah)) : null;
                        @endphp
                        <li>
                            <div class="relative pb-8">
                                @if(! $isLast)
                                    <!-- Garis Penghubung Antar Node -->
                                    <span class="absolute left-5 top-5 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                @endif

                                <div class="relative flex items-start space-x-3.5">
                                    <!-- Lingkaran Ikon Status -->
                                    <div class="relative">
                                        <span class="flex h-10 w-10 items-center justify-center rounded-xl border text-base shadow-sm ring-4 ring-white {{ $riwayat->icon_bg_class }}">
                                            {{ $riwayat->icon }}
                                        </span>
                                    </div>

                                    <!-- Konten Detail Riwayat -->
                                    <div class="min-w-0 flex-1 pt-1.5">
                                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <span class="text-sm font-bold text-gray-900">
                                                    {{ $riwayat->aksi }}
                                                </span>

                                                <!-- Badge Role & Pelaku -->
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold border {{ $riwayat->role_badge_class }}">
                                                    {{ $riwayat->role_label }}
                                                </span>

                                                @if($isLast)
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200 animate-pulse">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                        Status Terkini
                                                    </span>
                                                @endif
                                            </div>

                                            <!-- Waktu Aktivitas -->
                                            <div class="text-[11px] text-gray-400 flex items-center gap-1 shrink-0">
                                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <span>{{ $riwayat->created_at->isoFormat('D MMM Y, HH:mm') }} WIB</span>
                                                <span class="text-gray-300">({{ $riwayat->created_at->diffForHumans() }})</span>
                                            </div>
                                        </div>

                                        <!-- Informasi Pelaku & Transisi Status -->
                                        <div class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-gray-600">
                                            <div>
                                                Diproses oleh:
                                                <span class="font-semibold text-gray-800">
                                                    {{ $riwayat->user?->name ?? 'Sistem Otomatis' }}
                                                </span>
                                            </div>

                                            @if($statusLabelSesudah)
                                                <div class="flex items-center gap-1 text-[11px]">
                                                    <span class="text-gray-400">&bull;</span>
                                                    <span>Status:</span>
                                                    @if($statusLabelSebelumnya && $statusLabelSebelumnya !== $statusLabelSesudah)
                                                        <span class="text-gray-500 line-through">{{ $statusLabelSebelumnya }}</span>
                                                        <span class="text-gray-400">&rarr;</span>
                                                    @endif
                                                    <span class="font-bold text-indigo-700 bg-indigo-50 px-1.5 py-0.5 rounded border border-indigo-100">
                                                        {{ $statusLabelSesudah }}
                                                    </span>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Kotak Pesan / Catatan Resmi Jika Ada -->
                                        @if($riwayat->catatan)
                                            <div class="mt-2 text-xs rounded-xl p-3 border {{ str_contains(strtolower($riwayat->aksi), 'revisi') ? 'bg-amber-50/70 border-amber-200 text-amber-950' : (str_contains(strtolower($riwayat->aksi), 'penolakan') ? 'bg-rose-50/70 border-rose-200 text-rose-950' : 'bg-slate-50 border-gray-200/80 text-gray-800') }}">
                                                <div class="flex items-start gap-2">
                                                    <span class="text-sm shrink-0">💬</span>
                                                    <div class="italic">
                                                        "{{ $riwayat->catatan }}"
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</div>
