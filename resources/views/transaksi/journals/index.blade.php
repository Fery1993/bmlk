<x-app-layout title="Jurnal Manual" breadcrumb="Daftar seluruh jurnal (manual & otomatis)">

<x-table-wrapper title="Daftar Jurnal" :createRoute="route('transaksi.journals.create')" createLabel="Jurnal Manual Baru">

    <x-slot:filter>
        <form method="GET" class="flex flex-wrap gap-3">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari nomor jurnal / keterangan..."
                   class="border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 w-72">
            <button type="submit" class="bg-gray-700 hover:bg-gray-800 text-white text-sm px-4 py-1.5 rounded-lg transition">Filter</button>
            @if(request()->hasAny(['search']))
            <a href="{{ route('transaksi.journals.index') }}" class="text-sm text-gray-400 hover:text-gray-600 flex items-center">Reset</a>
            @endif
        </form>
    </x-slot:filter>

    <table class="w-full text-sm">
        <thead>
            <tr class="bg-gray-50">
                <th class="text-left text-xs text-gray-500 font-medium px-5 py-3">No. Jurnal</th>
                <th class="text-left text-xs text-gray-500 font-medium px-3 py-3">Tanggal</th>
                <th class="text-left text-xs text-gray-500 font-medium px-3 py-3">Keterangan</th>
                <th class="text-center text-xs text-gray-500 font-medium px-3 py-3">Sumber</th>
                <th class="text-center text-xs text-gray-500 font-medium px-3 py-3">Jml. Baris</th>
                <th class="text-center text-xs text-gray-500 font-medium px-3 py-3 pr-5">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($journals as $j)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-5 py-3">
                    <a href="{{ route('transaksi.journals.show', $j) }}" class="font-medium text-primary-600 hover:underline">{{ $j->nomor_jurnal }}</a>
                    @if($j->is_closing)
                    <span class="ml-1 inline-block text-[10px] font-medium px-1.5 py-0.5 rounded-full bg-purple-100 text-purple-700">Penutup</span>
                    @endif
                </td>
                <td class="px-3 py-3 text-gray-600 text-xs">{{ $j->tanggal->format('d M Y') }}</td>
                <td class="px-3 py-3 text-gray-700 text-xs">{{ $j->keterangan ?? '—' }}</td>
                <td class="px-3 py-3 text-center">
                    @php
                        $sBadge = match($j->sumber_tipe) {
                            'manual' => 'bg-blue-100 text-blue-700',
                            'invoice_payment' => 'bg-green-100 text-green-700',
                            'bill_payment' => 'bg-red-100 text-red-600',
                            'operational_fee' => 'bg-amber-100 text-amber-700',
                            'closing' => 'bg-purple-100 text-purple-700',
                            default => 'bg-gray-100 text-gray-500',
                        };
                        $sLabel = match($j->sumber_tipe) {
                            'manual' => 'Manual',
                            'invoice_payment' => 'Bayar Invoice',
                            'bill_payment' => 'Bayar Tagihan',
                            'operational_fee' => 'Fee',
                            'closing' => 'Penutup',
                            default => $j->sumber_tipe,
                        };
                    @endphp
                    <span class="inline-block text-[11px] font-medium px-2 py-0.5 rounded-full {{ $sBadge }}">{{ $sLabel }}</span>
                </td>
                <td class="px-3 py-3 text-center text-gray-600 text-xs">{{ $j->items_count }}</td>
                <td class="px-3 py-3 pr-5 text-center">
                    <div class="flex items-center justify-center gap-2">
                        <a href="{{ route('transaksi.journals.show', $j) }}" class="text-gray-400 hover:text-primary-600" title="Detail">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </a>
                        @if($j->sumber_tipe === 'manual')
                        <a href="{{ route('transaksi.journals.edit', $j) }}" class="text-gray-400 hover:text-amber-600" title="Edit">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>
                        <form method="POST" action="{{ route('transaksi.journals.destroy', $j) }}"
                              onsubmit="return confirm('Hapus jurnal {{ $j->nomor_jurnal }}?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-gray-400 hover:text-red-600" title="Hapus">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-5 py-12 text-center text-sm text-gray-400">
                    <svg class="w-10 h-10 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Belum ada jurnal ditemukan
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <x-slot:pagination>
        {{ $journals->withQueryString()->links() }}
    </x-slot:pagination>

</x-table-wrapper>

</x-app-layout>
