<x-app-layout title="Laporan Piutang" breadcrumb="Daftar piutang customer yang belum lunas">

<div class="space-y-4">

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <form method="GET" class="flex flex-wrap gap-3">
                <select name="customer_id" onchange="this.form.submit()"
                        class="border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <option value="">Semua Customer</option>
                    @foreach(\App\Models\Customer::orderBy('nama')->get() as $c)
                    <option value="{{ $c->id }}" @selected(request('customer_id') == $c->id)>{{ $c->nama }}</option>
                    @endforeach
                </select>
                @if(request('customer_id'))
                <a href="{{ route('laporan.piutang') }}" class="text-sm text-gray-400 hover:text-gray-600 flex items-center">Reset</a>
                @endif
            </form>
            <button onclick="window.print()" class="inline-flex items-center gap-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium px-3 py-1.5 rounded-lg transition">
                Cetak
            </button>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-gray-700">Rincian Piutang Belum Lunas</h2>
            <span class="text-xs text-gray-400">{{ $invoices->count() }} invoice</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="text-left text-xs text-gray-500 font-medium px-5 py-2.5">No. Invoice</th>
                        <th class="text-left text-xs text-gray-500 font-medium px-3 py-2.5">Customer</th>
                        <th class="text-left text-xs text-gray-500 font-medium px-3 py-2.5">Jatuh Tempo</th>
                        <th class="text-right text-xs text-gray-500 font-medium px-3 py-2.5">Total Tagihan</th>
                        <th class="text-right text-xs text-gray-500 font-medium px-3 py-2.5">Sudah Dibayar</th>
                        <th class="text-right text-xs text-gray-500 font-medium px-3 py-2.5 pr-5">Sisa Piutang</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($invoices as $inv)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-3">
                            <a href="{{ route('transaksi.invoices.show', $inv) }}" class="font-medium text-primary-600 hover:underline">{{ $inv->nomor_invoice }}</a>
                        </td>
                        <td class="px-3 py-3 text-gray-700">{{ $inv->customer->nama }}</td>
                        <td class="px-3 py-3 text-xs {{ $inv->jatuh_tempo->isPast() ? 'text-red-600 font-medium' : 'text-gray-600' }}">
                            {{ $inv->jatuh_tempo->format('d M Y') }}
                            @if($inv->jatuh_tempo->isPast())
                                <span class="block text-[10px]">Terlambat {{ $inv->jatuh_tempo->diffInDays(today()) }} hari</span>
                            @endif
                        </td>
                        <td class="px-3 py-3 text-right text-xs text-gray-700">Rp {{ number_format($inv->jumlah, 0, ',', '.') }}</td>
                        <td class="px-3 py-3 text-right text-xs text-gray-500">Rp {{ number_format($inv->payments->sum('jumlah_bayar'), 0, ',', '.') }}</td>
                        <td class="px-3 py-3 pr-5 text-right text-xs font-semibold text-red-600">Rp {{ number_format($inv->sisaTagihan(), 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center text-sm text-gray-400">Tidak ada piutang yang belum lunas 🎉</td>
                    </tr>
                    @endforelse
                </tbody>
                @if($invoices->count())
                <tfoot>
                    <tr class="bg-gray-50 font-semibold">
                        <td class="px-5 py-3 text-xs text-gray-600" colspan="5">Total Sisa Piutang</td>
                        <td class="px-3 py-3 pr-5 text-right text-sm text-red-600">Rp {{ number_format($totalPiutang, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

</div>

</x-app-layout>
