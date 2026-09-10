<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} — BMLK Keuangan</title>

    {{-- Tailwind CDN (ganti dengan Vite + npm di produksi) --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { 50:'#eef2ff',100:'#e0e7ff',500:'#6366f1',600:'#4f46e5',700:'#4338ca',800:'#3730a3',900:'#312e81' }
                    }
                }
            }
        }
    </script>

    {{-- Alpine.js --}}
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        .scrollbar-thin::-webkit-scrollbar { width: 4px; }
        .scrollbar-thin::-webkit-scrollbar-track { background: transparent; }
        .scrollbar-thin::-webkit-scrollbar-thumb { background: #4338ca; border-radius: 99px; }
        .nav-active { background: rgba(255,255,255,0.12); color: #fff; }
        .nav-item { transition: background 0.15s, color 0.15s; }
    </style>
</head>

<body class="h-full bg-gray-50 font-sans antialiased" x-data="{ sidebarOpen: true }">

<div class="flex h-screen overflow-hidden">

    {{-- ===== SIDEBAR ===== --}}
    <aside
        class="flex flex-col bg-primary-800 text-primary-100 transition-all duration-300 z-30 flex-shrink-0"
        :class="sidebarOpen ? 'w-56' : 'w-16'"
    >
        {{-- Logo --}}
        <div class="flex items-center gap-3 px-4 py-4 border-b border-primary-700 min-h-[56px]">
            <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <div x-show="sidebarOpen" x-cloak class="overflow-hidden">
                <p class="text-white font-semibold text-sm leading-tight">BMLK</p>
                <p class="text-primary-300 text-xs leading-tight">Sistem Keuangan</p>
            </div>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 overflow-y-auto scrollbar-thin py-3 space-y-0.5 px-2">

            {{-- Dashboard --}}
            <a href="{{ route('dashboard') }}"
               class="nav-item flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('dashboard') ? 'nav-active' : 'hover:bg-white/10' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span x-show="sidebarOpen" x-cloak class="truncate">Dashboard</span>
            </a>

            {{-- Grup: Master Data --}}
            <div x-show="sidebarOpen" x-cloak class="pt-3 pb-1 px-3">
                <p class="text-primary-400 text-[10px] font-semibold uppercase tracking-wider">Master Data</p>
            </div>
            <div x-show="!sidebarOpen" class="my-2 border-t border-primary-700"></div>

            <a href="{{ route('master.coa.index') }}"
               class="nav-item flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('master.coa.*') ? 'nav-active' : 'hover:bg-white/10' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                <span x-show="sidebarOpen" x-cloak class="truncate">Master COA</span>
            </a>

            <a href="{{ route('master.customers.index') }}"
               class="nav-item flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('master.customers.*') ? 'nav-active' : 'hover:bg-white/10' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span x-show="sidebarOpen" x-cloak class="truncate">Customer</span>
            </a>

            <a href="{{ route('master.vendors.index') }}"
               class="nav-item flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('master.vendors.*') ? 'nav-active' : 'hover:bg-white/10' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                <span x-show="sidebarOpen" x-cloak class="truncate">Vendor</span>
            </a>

            <a href="{{ route('master.karyawan.index') }}"
               class="nav-item flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('master.karyawan.*') ? 'nav-active' : 'hover:bg-white/10' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                <span x-show="sidebarOpen" x-cloak class="truncate">Karyawan</span>
            </a>

            <a href="{{ route('master.users.index') }}"
               class="nav-item flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('master.users.*') ? 'nav-active' : 'hover:bg-white/10' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span x-show="sidebarOpen" x-cloak class="truncate">User</span>
            </a>

            {{-- Grup: Transaksi --}}
            <div x-show="sidebarOpen" x-cloak class="pt-3 pb-1 px-3">
                <p class="text-primary-400 text-[10px] font-semibold uppercase tracking-wider">Transaksi</p>
            </div>
            <div x-show="!sidebarOpen" class="my-2 border-t border-primary-700"></div>

            <a href="{{ route('transaksi.invoices.index') }}"
               class="nav-item flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('transaksi.invoices.*') ? 'nav-active' : 'hover:bg-white/10' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span x-show="sidebarOpen" x-cloak class="truncate">Invoice</span>
            </a>

            <a href="{{ route('transaksi.bills.index') }}"
               class="nav-item flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('transaksi.bills.*') ? 'nav-active' : 'hover:bg-white/10' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                <span x-show="sidebarOpen" x-cloak class="truncate">Tagihan</span>
            </a>

            <a href="{{ route('transaksi.fee.index') }}"
               class="nav-item flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('transaksi.fee.*') ? 'nav-active' : 'hover:bg-white/10' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <span x-show="sidebarOpen" x-cloak class="truncate">Fee Operasional</span>
            </a>

            <a href="{{ route('transaksi.journals.index') }}"
               class="nav-item flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('transaksi.journals.*') ? 'nav-active' : 'hover:bg-white/10' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                <span x-show="sidebarOpen" x-cloak class="truncate">Jurnal Manual</span>
            </a>

            {{-- Grup: Laporan --}}
            <div x-show="sidebarOpen" x-cloak class="pt-3 pb-1 px-3">
                <p class="text-primary-400 text-[10px] font-semibold uppercase tracking-wider">Laporan</p>
            </div>
            <div x-show="!sidebarOpen" class="my-2 border-t border-primary-700"></div>

            <a href="{{ route('laporan.piutang') }}"
               class="nav-item flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('laporan.piutang') ? 'nav-active' : 'hover:bg-white/10' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                <span x-show="sidebarOpen" x-cloak class="truncate">Lap. Piutang</span>
            </a>

            <a href="{{ route('laporan.hutang') }}"
               class="nav-item flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('laporan.hutang') ? 'nav-active' : 'hover:bg-white/10' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/></svg>
                <span x-show="sidebarOpen" x-cloak class="truncate">Lap. Hutang</span>
            </a>

            <a href="{{ route('laporan.gl') }}"
               class="nav-item flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('laporan.gl') ? 'nav-active' : 'hover:bg-white/10' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                <span x-show="sidebarOpen" x-cloak class="truncate">General Ledger</span>
            </a>

            <a href="{{ route('laporan.neraca-saldo') }}"
               class="nav-item flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('laporan.neraca-saldo') ? 'nav-active' : 'hover:bg-white/10' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M3 14h18M10 6l-3 12M14 6l3 12"/></svg>
                <span x-show="sidebarOpen" x-cloak class="truncate">Neraca Saldo</span>
            </a>

            <a href="{{ route('laporan.neraca') }}"
               class="nav-item flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('laporan.neraca') ? 'nav-active' : 'hover:bg-white/10' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                <span x-show="sidebarOpen" x-cloak class="truncate">Neraca</span>
            </a>

            <a href="{{ route('laporan.laba-rugi') }}"
               class="nav-item flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('laporan.laba-rugi') ? 'nav-active' : 'hover:bg-white/10' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <span x-show="sidebarOpen" x-cloak class="truncate">Laba Rugi</span>
            </a>

        </nav>

        {{-- User info bawah --}}
        <div class="border-t border-primary-700 p-3">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-primary-600 flex items-center justify-center flex-shrink-0 text-xs font-semibold text-white">
                    {{ strtoupper(substr(auth()->user()->nama ?? 'A', 0, 2)) }}
                </div>
                <div x-show="sidebarOpen" x-cloak class="flex-1 min-w-0">
                    <p class="text-white text-xs font-medium truncate">{{ auth()->user()->nama ?? 'Admin' }}</p>
                    <p class="text-primary-300 text-[10px] truncate capitalize">{{ auth()->user()->role ?? 'admin' }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}" x-show="sidebarOpen" x-cloak>
                    @csrf
                    <button type="submit" class="text-primary-300 hover:text-white" title="Logout">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- ===== MAIN AREA ===== --}}
    <div class="flex flex-col flex-1 min-w-0 overflow-hidden">

        {{-- Topbar --}}
        <header class="h-14 bg-white border-b border-gray-200 flex items-center gap-4 px-4 flex-shrink-0">
            {{-- Toggle sidebar --}}
            <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 hover:text-gray-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>

            {{-- Breadcrumb / page title --}}
            <div class="flex-1">
                <h1 class="text-sm font-semibold text-gray-800">{{ $title ?? 'Dashboard' }}</h1>
                @isset($breadcrumb)
                    <p class="text-xs text-gray-400">{{ $breadcrumb }}</p>
                @endisset
            </div>

            {{-- Periode aktif --}}
            @php
                $periodeAktif = \App\Models\FiscalPeriod::where('status','open')->latest()->first();
            @endphp
            @if($periodeAktif)
            <span class="hidden sm:inline-flex items-center gap-1.5 bg-green-50 text-green-700 text-xs px-2.5 py-1 rounded-full border border-green-200">
                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                Periode {{ $periodeAktif->kode_periode }}
            </span>
            @endif

            {{-- Notif --}}
            <button class="relative text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            </button>
        </header>

        {{-- Flash messages --}}
        @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             class="mx-4 mt-3 flex items-center gap-2 bg-green-50 border border-green-200 text-green-800 text-sm px-4 py-2.5 rounded-lg">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
             class="mx-4 mt-3 flex items-center gap-2 bg-red-50 border border-red-200 text-red-800 text-sm px-4 py-2.5 rounded-lg">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('error') }}
        </div>
        @endif

        {{-- Page content --}}
        <main class="flex-1 overflow-y-auto p-4 sm:p-6">
            {{ $slot }}
        </main>

    </div>
</div>

</body>
</html>
