<x-app-layout title="Dashboard" breadcrumb="Ringkasan keuangan periode berjalan">

{{-- Stat cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-medium text-gray-500">Total Piutang</span>
            <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center">
                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            </div>
        </div>
        <p class="text-xl font-bold text-gray-800">Rp {{ number_format($totalPiutang ?? 0, 0, ',', '.') }}</p>
        @if(($piutangJatuhTempo ?? 0) > 0)
        <p class="text-xs text-red-500 mt-1">{{ $piutangJatuhTempo }} invoice melewati jatuh tempo</p>
        @else
        <p class="text-xs text-gray-400 mt-1">Semua lancar</p>
        @endif
    </div>

    <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-medium text-gray-500">Total Hutang</span>
            <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center">
                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/></svg>
            </div>
        </div>
        <p class="text-xl font-bold text-gray-800">Rp {{ number_format($totalHutang ?? 0, 0, ',', '.') }}</p>
        @if(($hutangJatuhTempo ?? 0) > 0)
        <p class="text-xs text-red-500 mt-1">{{ $hutangJatuhTempo }} tagihan segera jatuh tempo</p>
        @else
        <p class="text-xs text-gray-400 mt-1">Tidak ada yang mendesak</p>
        @endif
    </div>

    <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-medium text-gray-500">Pendapatan Bulan Ini</span>
            <div class="w-8 h-8 rounded-lg bg-green-50 flex items-center justify-center">
                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
        <p class="text-xl font-bold text-gray-800">Rp {{ number_format($pendapatanBulanIni ?? 0, 0, ',', '.') }}</p>
        <p class="text-xs text-gray-400 mt-1">Periode {{ $periodeAktif->kode_periode ?? '-' }}</p>
    </div>

    <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-medium text-gray-500">Invoice Bulan Ini</span>
            <div class="w-8 h-8 rounded-lg bg-primary-50 flex items-center justify-center">
                <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
        </div>
        <p class="text-xl font-bold text-gray-800">{{ $jumlahInvoice ?? 0 }}</p>
        <p class="text-xs text-gray-400 mt-1">{{ $invoiceBelumLunas ?? 0 }} belum lunas</p>
    </div>

</div>

{{-- Content row --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

    {{-- Invoice terbaru --}}
    <div class="lg:col-span-2 bg-white rounded-xl border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-700">Invoice Terbaru</h2>
            <a href="{{ route('transaksi.invoices.index') }}" class="text-xs text-primary-600 hover:underline">Lihat semua</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="text-left text-xs text-gray-500 font-medium px-5 py-2.5">No. Invoice</th>
                        <th class="text-left text-xs text-gray-500 font-medium px-3 py-2.5">Customer</th>
                        <th class="text-left text-xs text-gray-500 font-medium px-3 py-2.5">Jatuh Tempo</th>
                        <th class="text-right text-xs text-gray-500 font-medium px-3 py-2.5">Total</th>
                        <th class="text-center text-xs text-gray-500 font-medium px-3 py-2.5 pr-5">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($invoiceTerbaru ?? [] as $inv)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-3">
                            <a href="{{ route('transaksi.invoices.show', $inv) }}" class="font-medium text-primary-600 hover:underline">
                                {{ $inv->nomor_invoice }}
                            </a>
                            <p class="text-xs text-gray-400">{{ $inv->tanggal->format('d M Y') }}</p>
                        </td>
                        <td class="px-3 py-3 text-gray-700 text-xs">{{ $inv->customer->nama }}</td>
                        <td class="px-3 py-3 text-gray-500 text-xs">{{ $inv->jatuh_tempo->format('d M Y') }}</td>
                        <td class="px-3 py-3 text-right font-medium text-gray-800 text-xs">
                            Rp {{ number_format($inv->jumlah, 0, ',', '.') }}
                        </td>
                        <td class="px-3 py-3 pr-5 text-center">
                            @php
                                $badge = match($inv->status) {
                                    'lunas' => 'bg-green-100 text-green-700',
                                    'sebagian' => 'bg-amber-100 text-amber-700',
                                    'batal' => 'bg-gray-100 text-gray-500',
                                    default => 'bg-red-100 text-red-600',
                                };
                                $label = match($inv->status) {
                                    'lunas' => 'Lunas',
                                    'sebagian' => 'Sebagian',
                                    'batal' => 'Batal',
                                    default => 'Belum Lunas',
                                };
                            @endphp
                            <span class="inline-block text-[11px] font-medium px-2 py-0.5 rounded-full {{ $badge }}">{{ $label }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-8 text-center text-sm text-gray-400">Belum ada invoice</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Panel kanan --}}
    <div class="space-y-4">

        {{-- Piutang jatuh tempo --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
            <div class="px-4 py-3 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-700">Piutang Jatuh Tempo</h2>
            </div>
            <div class="p-4 space-y-3">
                @forelse($piutangMendesak ?? [] as $inv)
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-xs font-medium text-gray-700 truncate">{{ $inv->customer->nama }}</p>
                        <p class="text-[11px] text-gray-400">{{ $inv->nomor_invoice }} · {{ $inv->jatuh_tempo->format('d M') }}</p>
                    </div>
                    <span class="text-xs font-semibold text-red-600 flex-shrink-0">
                        Rp {{ number_format($inv->sisaTagihan(), 0, ',', '.') }}
                    </span>
                </div>
                @empty
                <p class="text-xs text-gray-400 text-center py-4">Tidak ada piutang mendesak</p>
                @endforelse
            </div>
        </div>

        {{-- Aksi cepat --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
            <div class="px-4 py-3 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-700">Aksi Cepat</h2>
            </div>
            <div class="p-3 grid grid-cols-2 gap-2">
                <a href="{{ route('transaksi.invoices.create') }}"
                   class="flex flex-col items-center gap-1.5 p-3 rounded-lg border border-gray-100 hover:border-primary-200 hover:bg-primary-50 transition text-center">
                    <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"/></svg>
                    <span class="text-[11px] font-medium text-gray-600">Invoice Baru</span>
                </a>
                <a href="{{ route('transaksi.fee.create') }}"
                   class="flex flex-col items-center gap-1.5 p-3 rounded-lg border border-gray-100 hover:border-primary-200 hover:bg-primary-50 transition text-center">
                    <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span class="text-[11px] font-medium text-gray-600">Input Fee</span>
                </a>
                <a href="{{ route('laporan.piutang') }}"
                   class="flex flex-col items-center gap-1.5 p-3 rounded-lg border border-gray-100 hover:border-primary-200 hover:bg-primary-50 transition text-center">
                    <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span class="text-[11px] font-medium text-gray-600">Lap. Piutang</span>
                </a>
                <a href="{{ route('laporan.laba-rugi') }}"
                   class="flex flex-col items-center gap-1.5 p-3 rounded-lg border border-gray-100 hover:border-primary-200 hover:bg-primary-50 transition text-center">
                    <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <span class="text-[11px] font-medium text-gray-600">Laba Rugi</span>
                </a>
            </div>
        </div>

    </div>
</div>

</x-app-layout>
