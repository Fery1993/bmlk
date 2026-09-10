<x-app-layout title="Tagihan" breadcrumb="Daftar tagihan (bills) dari vendor">

<x-table-wrapper title="Daftar Tagihan" :createRoute="route('transaksi.bills.create')" createLabel="Tagihan Baru">

    <x-slot:filter>
        <form method="GET" class="flex flex-wrap gap-3">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari nomor / vendor..."
                   class="border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 w-64">
            <select name="status" class="border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                <option value="">Semua Status</option>
                <option value="belum_lunas" @selected(request('status')=='belum_lunas')>Belum Lunas</option>
                <option value="sebagian"    @selected(request('status')=='sebagian')>Sebagian</option>
                <option value="lunas"       @selected(request('status')=='lunas')>Lunas</option>
                <option value="batal"       @selected(request('status')=='batal')>Batal</option>
            </select>
            <button type="submit" class="bg-gray-700 hover:bg-gray-800 text-white text-sm px-4 py-1.5 rounded-lg transition">Filter</button>
            @if(request()->hasAny(['search','status']))
            <a href="{{ route('transaksi.bills.index') }}" class="text-sm text-gray-400 hover:text-gray-600 flex items-center">Reset</a>
            @endif
        </form>
    </x-slot:filter>

    <table class="w-full text-sm">
        <thead>
            <tr class="bg-gray-50">
                <th class="text-left text-xs text-gray-500 font-medium px-5 py-3">No. Bill</th>
                <th class="text-left text-xs text-gray-500 font-medium px-3 py-3">Vendor</th>
                <th class="text-left text-xs text-gray-500 font-medium px-3 py-3">Jatuh Tempo</th>
                <th class="text-right text-xs text-gray-500 font-medium px-3 py-3">Jumlah</th>
                <th class="text-center text-xs text-gray-500 font-medium px-3 py-3">Status</th>
                <th class="text-center text-xs text-gray-500 font-medium px-3 py-3 pr-5">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($bills as $bill)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-5 py-3">
                    <a href="{{ route('transaksi.bills.show', $bill) }}" class="font-medium text-primary-600 hover:underline">{{ $bill->nomor_bill }}</a>
                    <p class="text-xs text-gray-400">{{ $bill->tanggal->format('d M Y') }}</p>
                </td>
                <td class="px-3 py-3 text-gray-800">{{ $bill->vendor->nama }}</td>
                <td class="px-3 py-3 text-xs {{ $bill->jatuh_tempo->isPast() && $bill->status !== 'lunas' ? 'text-red-600 font-medium' : 'text-gray-600' }}">
                    {{ $bill->jatuh_tempo->format('d M Y') }}
                </td>
                <td class="px-3 py-3 text-right font-semibold text-gray-800 text-xs">
                    Rp {{ number_format($bill->jumlah, 0, ',', '.') }}
                </td>
                <td class="px-3 py-3 text-center">
                    @php
                        $badge = match($bill->status) {
                            'lunas'      => 'bg-green-100 text-green-700',
                            'sebagian'   => 'bg-amber-100 text-amber-700',
                            'batal'      => 'bg-gray-100 text-gray-500',
                            default      => 'bg-red-100 text-red-600',
                        };
                        $label = match($bill->status) {
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
                        <a href="{{ route('transaksi.bills.show', $bill) }}" class="text-gray-400 hover:text-primary-600" title="Detail">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </a>
                        @if($bill->status === 'belum_lunas' || $bill->status === 'sebagian')
                        <a href="{{ route('transaksi.bills.edit', $bill) }}" class="text-gray-400 hover:text-amber-600" title="Edit">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-5 py-12 text-center text-sm text-gray-400">
                    <svg class="w-10 h-10 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    Belum ada tagihan ditemukan
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <x-slot:pagination>
        {{ $bills->withQueryString()->links() }}
    </x-slot:pagination>

</x-table-wrapper>

</x-app-layout>
