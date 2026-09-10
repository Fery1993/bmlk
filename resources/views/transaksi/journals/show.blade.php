<x-app-layout title="{{ $journal->nomor_jurnal }}" breadcrumb="Detail jurnal">

<div class="max-w-4xl space-y-4">

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
        <div class="flex items-center gap-3 px-5 py-4 border-b border-gray-100">
            <a href="{{ route('transaksi.journals.index') }}" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h2 class="text-sm font-semibold text-gray-700">{{ $journal->nomor_jurnal }}</h2>
                <p class="text-xs text-gray-400">{{ $journal->tanggal->format('d M Y') }}</p>
            </div>
            @php
                $sBadge = match($journal->sumber_tipe) {
                    'manual' => 'bg-blue-100 text-blue-700',
                    'invoice_payment' => 'bg-green-100 text-green-700',
                    'bill_payment' => 'bg-red-100 text-red-600',
                    'operational_fee' => 'bg-amber-100 text-amber-700',
                    'closing' => 'bg-purple-100 text-purple-700',
                    default => 'bg-gray-100 text-gray-500',
                };
                $sLabel = match($journal->sumber_tipe) {
                    'manual' => 'Manual',
                    'invoice_payment' => 'Bayar Invoice',
                    'bill_payment' => 'Bayar Tagihan',
                    'operational_fee' => 'Fee Operasional',
                    'closing' => 'Jurnal Penutup',
                    default => $journal->sumber_tipe,
                };
            @endphp
            <span class="text-[11px] font-medium px-2 py-0.5 rounded-full {{ $sBadge }}">{{ $sLabel }}</span>

            @if($journal->sumber_tipe === 'manual')
            <div class="ml-auto flex items-center gap-2">
                <a href="{{ route('transaksi.journals.edit', $journal) }}"
                   class="inline-flex items-center gap-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 text-xs font-medium px-3 py-1.5 rounded-lg transition">
                    Edit
                </a>
                <form method="POST" action="{{ route('transaksi.journals.destroy', $journal) }}"
                      onsubmit="return confirm('Hapus jurnal {{ $journal->nomor_jurnal }}?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-1.5 bg-red-50 hover:bg-red-100 text-red-700 text-xs font-medium px-3 py-1.5 rounded-lg transition">
                        Hapus
                    </button>
                </form>
            </div>
            @endif
        </div>

        @if($journal->keterangan)
        <div class="px-5 pt-4 text-sm">
            <p class="text-xs text-gray-400 mb-1">Keterangan</p>
            <p class="text-gray-700">{{ $journal->keterangan }}</p>
        </div>
        @endif

        <div class="overflow-x-auto mt-4">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="text-left text-xs text-gray-500 font-medium px-5 py-2.5">Akun</th>
                        <th class="text-left text-xs text-gray-500 font-medium px-3 py-2.5">Keterangan</th>
                        <th class="text-right text-xs text-gray-500 font-medium px-3 py-2.5">Debit</th>
                        <th class="text-right text-xs text-gray-500 font-medium px-3 py-2.5 pr-5">Kredit</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($journal->items as $item)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-3 text-gray-700 text-xs">{{ $item->chartOfAccount->kode_akun }} · {{ $item->chartOfAccount->nama_akun }}</td>
                        <td class="px-3 py-3 text-gray-500 text-xs">{{ $item->keterangan ?? '—' }}</td>
                        <td class="px-3 py-3 text-right text-xs text-gray-700">{{ $item->debit > 0 ? 'Rp '.number_format($item->debit, 0, ',', '.') : '—' }}</td>
                        <td class="px-3 py-3 pr-5 text-right text-xs text-gray-700">{{ $item->kredit > 0 ? 'Rp '.number_format($item->kredit, 0, ',', '.') : '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-gray-50 font-semibold">
                        <td class="px-5 py-2.5 text-xs text-gray-600" colspan="2">Total</td>
                        <td class="px-3 py-2.5 text-right text-xs text-gray-800">Rp {{ number_format($journal->items->sum('debit'), 0, ',', '.') }}</td>
                        <td class="px-3 py-2.5 pr-5 text-right text-xs text-gray-800">Rp {{ number_format($journal->items->sum('kredit'), 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</div>

</x-app-layout>
