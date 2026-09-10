<x-app-layout title="{{ $fee->nomor_fee }}" breadcrumb="Detail fee operasional">

<div class="max-w-4xl space-y-4">

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
        <div class="flex items-center gap-3 px-5 py-4 border-b border-gray-100">
            <a href="{{ route('transaksi.fee.index') }}" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h2 class="text-sm font-semibold text-gray-700">{{ $fee->nomor_fee }}</h2>
                <p class="text-xs text-gray-400">{{ $fee->tanggal->format('d M Y') }}</p>
            </div>
            <div class="ml-auto flex items-center gap-2">
                <a href="{{ route('transaksi.fee.edit', $fee) }}"
                   class="inline-flex items-center gap-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 text-xs font-medium px-3 py-1.5 rounded-lg transition">
                    Edit
                </a>
                <form method="POST" action="{{ route('transaksi.fee.destroy', $fee) }}"
                      onsubmit="return confirm('Hapus fee {{ $fee->nomor_fee }}? Saldo bank akan dikembalikan.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-1.5 bg-red-50 hover:bg-red-100 text-red-700 text-xs font-medium px-3 py-1.5 rounded-lg transition">
                        Hapus
                    </button>
                </form>
            </div>
        </div>

        <div class="p-5 grid grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-xs text-gray-400 mb-1">Karyawan</p>
                <p class="font-medium text-gray-800">{{ $fee->karyawan->nama }}</p>
                <p class="text-xs text-gray-400">{{ $fee->karyawan->jabatan ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Dicatat Oleh</p>
                <p class="text-gray-700">{{ $fee->user->nama ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Akun Beban</p>
                <p class="text-gray-700">{{ $fee->bebanAccount->kode_akun }} · {{ $fee->bebanAccount->nama_akun }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Dibayar dari Rekening</p>
                <p class="text-gray-700">{{ $fee->bankAccount->nama_bank ?? '—' }} · {{ $fee->bankAccount->nomor_rekening ?? '' }}</p>
            </div>
            @if($fee->keterangan)
            <div class="col-span-2">
                <p class="text-xs text-gray-400 mb-1">Keterangan</p>
                <p class="text-gray-700">{{ $fee->keterangan }}</p>
            </div>
            @endif
        </div>

        <div class="px-5 pb-5">
            <div class="bg-gray-50 rounded-xl p-4 flex justify-between text-primary-700">
                <span class="font-bold">Jumlah Fee</span>
                <span class="font-bold text-base">Rp {{ number_format($fee->jumlah_fee, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    {{-- Jurnal --}}
    @if($fee->journal)
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-700">Jurnal Terkait: {{ $fee->journal->nomor_jurnal }}</h2>
        </div>
        <div class="overflow-x-auto">
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
                    @foreach($fee->journal->items as $item)
                    <tr>
                        <td class="px-5 py-3 text-gray-700 text-xs">{{ $item->chartOfAccount->kode_akun }} · {{ $item->chartOfAccount->nama_akun }}</td>
                        <td class="px-3 py-3 text-gray-500 text-xs">{{ $item->keterangan ?? '—' }}</td>
                        <td class="px-3 py-3 text-right text-xs text-gray-700">{{ $item->debit > 0 ? 'Rp '.number_format($item->debit, 0, ',', '.') : '—' }}</td>
                        <td class="px-3 py-3 pr-5 text-right text-xs text-gray-700">{{ $item->kredit > 0 ? 'Rp '.number_format($item->kredit, 0, ',', '.') : '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>

</x-app-layout>
