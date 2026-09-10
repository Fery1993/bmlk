<x-app-layout title="{{ $customer->nama }}" breadcrumb="Detail customer {{ $customer->kode }}">

<div class="max-w-4xl space-y-4">

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
        <div class="flex items-center gap-3 px-5 py-4 border-b border-gray-100">
            <a href="{{ route('master.customers.index') }}" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="text-sm font-semibold text-gray-700">Detail Customer</h2>
            <div class="ml-auto flex items-center gap-2">
                <a href="{{ route('master.customers.edit', $customer) }}"
                   class="inline-flex items-center gap-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 text-xs font-medium px-3 py-1.5 rounded-lg transition">
                    Edit
                </a>
                <form method="POST" action="{{ route('master.customers.destroy', $customer) }}"
                      onsubmit="return confirm('Hapus customer {{ $customer->nama }}?');">
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
                <p class="text-xs text-gray-400 mb-1">Kode</p>
                <p class="font-semibold text-gray-800">{{ $customer->kode }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Nama</p>
                <p class="font-semibold text-gray-800">{{ $customer->nama }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Telepon</p>
                <p class="text-gray-700">{{ $customer->telepon ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Email</p>
                <p class="text-gray-700">{{ $customer->email ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">NPWP</p>
                <p class="text-gray-700">{{ $customer->npwp ?? '—' }}</p>
            </div>
            <div class="col-span-2">
                <p class="text-xs text-gray-400 mb-1">Alamat</p>
                <p class="text-gray-700">{{ $customer->alamat ?? '—' }}</p>
            </div>
        </div>
    </div>

    {{-- Invoice terbaru --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-700">Invoice Terbaru</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="text-left text-xs text-gray-500 font-medium px-5 py-2.5">No. Invoice</th>
                        <th class="text-left text-xs text-gray-500 font-medium px-3 py-2.5">Tanggal</th>
                        <th class="text-right text-xs text-gray-500 font-medium px-3 py-2.5">Total</th>
                        <th class="text-center text-xs text-gray-500 font-medium px-3 py-2.5 pr-5">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($customer->invoices as $inv)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-3">
                            <a href="{{ route('transaksi.invoices.show', $inv) }}" class="font-medium text-primary-600 hover:underline">{{ $inv->nomor_invoice }}</a>
                        </td>
                        <td class="px-3 py-3 text-gray-600 text-xs">{{ $inv->tanggal->format('d M Y') }}</td>
                        <td class="px-3 py-3 text-right font-medium text-gray-800 text-xs">Rp {{ number_format($inv->jumlah, 0, ',', '.') }}</td>
                        <td class="px-3 py-3 pr-5 text-center">
                            @php
                                $badge = match($inv->status) {
                                    'lunas' => 'bg-green-100 text-green-700',
                                    'sebagian' => 'bg-amber-100 text-amber-700',
                                    'batal' => 'bg-gray-100 text-gray-500',
                                    default => 'bg-red-100 text-red-600',
                                };
                                $label = match($inv->status) {
                                    'lunas' => 'Lunas', 'sebagian' => 'Sebagian', 'batal' => 'Batal', default => 'Belum Lunas',
                                };
                            @endphp
                            <span class="inline-block text-[11px] font-medium px-2 py-0.5 rounded-full {{ $badge }}">{{ $label }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-5 py-8 text-center text-sm text-gray-400">Belum ada invoice untuk customer ini</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

</x-app-layout>
