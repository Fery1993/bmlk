<x-app-layout title="{{ $vendor->nama }}" breadcrumb="Detail vendor {{ $vendor->kode }}">

<div class="max-w-4xl space-y-4">

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
        <div class="flex items-center gap-3 px-5 py-4 border-b border-gray-100">
            <a href="{{ route('master.vendors.index') }}" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="text-sm font-semibold text-gray-700">Detail Vendor</h2>
            <div class="ml-auto flex items-center gap-2">
                <a href="{{ route('master.vendors.edit', $vendor) }}"
                   class="inline-flex items-center gap-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 text-xs font-medium px-3 py-1.5 rounded-lg transition">
                    Edit
                </a>
                <form method="POST" action="{{ route('master.vendors.destroy', $vendor) }}"
                      onsubmit="return confirm('Hapus vendor {{ $vendor->nama }}?');">
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
                <p class="font-semibold text-gray-800">{{ $vendor->kode }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Nama</p>
                <p class="font-semibold text-gray-800">{{ $vendor->nama }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Telepon</p>
                <p class="text-gray-700">{{ $vendor->telepon ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Email</p>
                <p class="text-gray-700">{{ $vendor->email ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">NPWP</p>
                <p class="text-gray-700">{{ $vendor->npwp ?? '—' }}</p>
            </div>
            <div class="col-span-2">
                <p class="text-xs text-gray-400 mb-1">Alamat</p>
                <p class="text-gray-700">{{ $vendor->alamat ?? '—' }}</p>
            </div>
        </div>
    </div>

    {{-- Tagihan terbaru --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-700">Tagihan Terbaru</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="text-left text-xs text-gray-500 font-medium px-5 py-2.5">No. Bill</th>
                        <th class="text-left text-xs text-gray-500 font-medium px-3 py-2.5">Tanggal</th>
                        <th class="text-right text-xs text-gray-500 font-medium px-3 py-2.5">Jumlah</th>
                        <th class="text-center text-xs text-gray-500 font-medium px-3 py-2.5 pr-5">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($vendor->bills as $bill)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-3">
                            <a href="{{ route('transaksi.bills.show', $bill) }}" class="font-medium text-primary-600 hover:underline">{{ $bill->nomor_bill }}</a>
                        </td>
                        <td class="px-3 py-3 text-gray-600 text-xs">{{ $bill->tanggal->format('d M Y') }}</td>
                        <td class="px-3 py-3 text-right font-medium text-gray-800 text-xs">Rp {{ number_format($bill->jumlah, 0, ',', '.') }}</td>
                        <td class="px-3 py-3 pr-5 text-center">
                            @php
                                $badge = match($bill->status) {
                                    'lunas' => 'bg-green-100 text-green-700',
                                    'sebagian' => 'bg-amber-100 text-amber-700',
                                    'batal' => 'bg-gray-100 text-gray-500',
                                    default => 'bg-red-100 text-red-600',
                                };
                                $label = match($bill->status) {
                                    'lunas' => 'Lunas', 'sebagian' => 'Sebagian', 'batal' => 'Batal', default => 'Belum Lunas',
                                };
                            @endphp
                            <span class="inline-block text-[11px] font-medium px-2 py-0.5 rounded-full {{ $badge }}">{{ $label }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-5 py-8 text-center text-sm text-gray-400">Belum ada tagihan untuk vendor ini</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

</x-app-layout>
