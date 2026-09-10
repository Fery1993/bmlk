<x-app-layout title="Invoice" breadcrumb="Daftar invoice penjualan jasa bongkar muat">

<x-table-wrapper title="Daftar Invoice" :createRoute="route('transaksi.invoices.create')" createLabel="Invoice Baru">

    <x-slot:filter>
        <form method="GET" class="flex flex-wrap gap-3">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari nomor / customer..."
                   class="border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 w-64">
            <select name="status" class="border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                <option value="">Semua Status</option>
                <option value="belum_lunas" @selected(request('status')=='belum_lunas')>Belum Lunas</option>
                <option value="sebagian"    @selected(request('status')=='sebagian')>Sebagian</option>
                <option value="lunas"       @selected(request('status')=='lunas')>Lunas</option>
                <option value="batal"       @selected(request('status')=='batal')>Batal</option>
            </select>
            <input type="month" name="periode" value="{{ request('periode') }}"
                   class="border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
            <button type="submit" class="bg-gray-700 hover:bg-gray-800 text-white text-sm px-4 py-1.5 rounded-lg transition">Filter</button>
            @if(request()->hasAny(['search','status','periode']))
            <a href="{{ route('transaksi.invoices.index') }}" class="text-sm text-gray-400 hover:text-gray-600 flex items-center">Reset</a>
            @endif
        </form>
    </x-slot:filter>

    <table class="w-full text-sm">
        <thead>
            <tr class="bg-gray-50">
                <th class="text-left text-xs text-gray-500 font-medium px-5 py-3">No. Invoice</th>
                <th class="text-left text-xs text-gray-500 font-medium px-3 py-3">Customer</th>
                <th class="text-left text-xs text-gray-500 font-medium px-3 py-3">Jatuh Tempo</th>
                <th class="text-right text-xs text-gray-500 font-medium px-3 py-3">DPP</th>
                <th class="text-right text-xs text-gray-500 font-medium px-3 py-3">Total</th>
                <th class="text-center text-xs text-gray-500 font-medium px-3 py-3">Status</th>
                <th class="text-center text-xs text-gray-500 font-medium px-3 py-3 pr-5">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($invoices as $inv)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-5 py-3">
                    <a href="{{ route('transaksi.invoices.show', $inv) }}" class="font-medium text-primary-600 hover:underline">
                        {{ $inv->nomor_invoice }}
                    </a>
                    <p class="text-xs text-gray-400">{{ $inv->tanggal->format('d M Y') }}</p>
                </td>
                <td class="px-3 py-3">
                    <p class="text-gray-800 font-medium">{{ $inv->customer->nama }}</p>
                    <p class="text-xs text-gray-400">{{ $inv->customer->npwp ?? '—' }}</p>
                </td>
                <td class="px-3 py-3 text-xs {{ $inv->jatuh_tempo->isPast() && $inv->status !== 'lunas' ? 'text-red-600 font-medium' : 'text-gray-600' }}">
                    {{ $inv->jatuh_tempo->format('d M Y') }}
                </td>
                <td class="px-3 py-3 text-right text-xs text-gray-600">
                    Rp {{ number_format($inv->dpp ?? 0, 0, ',', '.') }}
                </td>
                <td class="px-3 py-3 text-right font-semibold text-gray-800 text-xs">
                    Rp {{ number_format($inv->jumlah, 0, ',', '.') }}
                </td>
                <td class="px-3 py-3 text-center">
                    @php
                        $badge = match($inv->status) {
                            'lunas'      => 'bg-green-100 text-green-700',
                            'sebagian'   => 'bg-amber-100 text-amber-700',
                            'batal'      => 'bg-gray-100 text-gray-500',
                            default      => 'bg-red-100 text-red-600',
                        };
                        $label = match($inv->status) {
                            'lunas'      => 'Lunas',
                            'sebagian'   => 'Sebagian',
                            'batal'      => 'Batal',
                            default      => 'Belum Lunas',
                        };
                    @endphp
                    <span class="inline-block text-[11px] font-medium px-2 py-0.5 rounded-full {{ $badge }}">{{ $label }}</span>
                </td>
                <td class="px-3 py-3 pr-5 text-center">
                    <div class="flex items-center justify-center gap-2">
                        <a href="{{ route('transaksi.invoices.show', $inv) }}" class="text-gray-400 hover:text-primary-600" title="Detail">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </a>
                        @if($inv->status === 'belum_lunas' || $inv->status === 'sebagian')
                        <a href="{{ route('transaksi.invoices.edit', $inv) }}" class="text-gray-400 hover:text-amber-600" title="Edit">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>
                        @endif
                        <a href="{{ route('transaksi.invoices.cetak', $inv) }}" target="_blank" class="text-gray-400 hover:text-green-600" title="Cetak Invoice">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        </a>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-5 py-12 text-center text-sm text-gray-400">
                    <svg class="w-10 h-10 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Belum ada invoice ditemukan
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <x-slot:pagination>
        {{ $invoices->withQueryString()->links() }}
    </x-slot:pagination>

</x-table-wrapper>

</x-app-layout>
