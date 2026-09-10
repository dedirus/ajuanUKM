<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'AjuanUKM') }} - Sistem Pengajuan Kegiatan Mahasiswa</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-slate-50 text-slate-800">
        <div class="min-h-screen">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white border-b border-gray-200">
                    <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Flash Notifications -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">
                @if (session('success'))
                    <div class="flex items-center p-4 mb-4 text-emerald-800 border border-emerald-300 rounded-xl bg-emerald-50 shadow-sm" role="alert">
                        <svg class="flex-shrink-0 w-5 h-5 me-3 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                        <div class="text-sm font-medium">{{ session('success') }}</div>
                    </div>
                @endif

                @if (session('error'))
                    <div class="flex items-center p-4 mb-4 text-rose-800 border border-rose-300 rounded-xl bg-rose-50 shadow-sm" role="alert">
                        <svg class="flex-shrink-0 w-5 h-5 me-3 text-rose-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                        <div class="text-sm font-medium">{{ session('error') }}</div>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="p-4 mb-4 text-rose-800 border border-rose-300 rounded-xl bg-rose-50 shadow-sm" role="alert">
                        <div class="flex items-center mb-2 font-semibold text-sm">
                            <svg class="w-5 h-5 me-2 text-rose-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                            Terdapat beberapa kesalahan pengisian form:
                        </div>
                        <ul class="list-disc list-inside text-sm space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            <!-- Page Content -->
            <main class="py-6">
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
