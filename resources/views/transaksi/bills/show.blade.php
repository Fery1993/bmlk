<x-app-layout title="{{ $bill->nomor_bill }}" breadcrumb="Detail tagihan dari vendor">

<div x-data="{ payModal: false }" class="max-w-5xl space-y-4">

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
        <div class="flex items-center gap-3 px-5 py-4 border-b border-gray-100">
            <a href="{{ route('transaksi.bills.index') }}" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h2 class="text-sm font-semibold text-gray-700">{{ $bill->nomor_bill }}</h2>
                <p class="text-xs text-gray-400">{{ $bill->tanggal->format('d M Y') }}</p>
            </div>
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
            <span class="text-[11px] font-medium px-2 py-0.5 rounded-full {{ $badge }}">{{ $label }}</span>

            <div class="ml-auto flex items-center gap-2">
                @if($bill->status !== 'lunas' && $bill->status !== 'batal')
                <a href="{{ route('transaksi.bills.edit', $bill) }}"
                   class="inline-flex items-center gap-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 text-xs font-medium px-3 py-1.5 rounded-lg transition">
                    Edit
                </a>
                <button @click="payModal = true"
                        class="inline-flex items-center gap-1.5 bg-primary-700 hover:bg-primary-800 text-white text-xs font-medium px-3 py-1.5 rounded-lg transition">
                    Catat Pembayaran
                </button>
                @endif
                @if(!$bill->payments->count())
                <form method="POST" action="{{ route('transaksi.bills.destroy', $bill) }}"
                      onsubmit="return confirm('Hapus tagihan {{ $bill->nomor_bill }}?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-1.5 bg-red-50 hover:bg-red-100 text-red-700 text-xs font-medium px-3 py-1.5 rounded-lg transition">
                        Hapus
                    </button>
                </form>
                @endif
            </div>
        </div>

        <div class="p-5 grid grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-xs text-gray-400 mb-1">Vendor</p>
                <p class="font-medium text-gray-800">{{ $bill->vendor->nama }}</p>
                <p class="text-xs text-gray-400">{{ $bill->vendor->npwp ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Jatuh Tempo</p>
                <p class="font-medium {{ $bill->jatuh_tempo->isPast() && $bill->status !== 'lunas' ? 'text-red-600' : 'text-gray-800' }}">
                    {{ $bill->jatuh_tempo->format('d M Y') }}
                </p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Akun Beban</p>
                <p class="text-gray-700">{{ $bill->bebanAccount->kode_akun }} · {{ $bill->bebanAccount->nama_akun }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Akun Utang</p>
                <p class="text-gray-700">{{ $bill->utangAccount->kode_akun }} · {{ $bill->utangAccount->nama_akun }}</p>
            </div>
            @if($bill->keterangan)
            <div class="col-span-2">
                <p class="text-xs text-gray-400 mb-1">Keterangan</p>
                <p class="text-gray-700">{{ $bill->keterangan }}</p>
            </div>
            @endif
        </div>

        <div class="px-5 pb-5">
            <div class="bg-gray-50 rounded-xl p-4 space-y-2 text-sm">
                <div class="flex justify-between text-primary-700">
                    <span class="font-bold">Total Tagihan</span>
                    <span class="font-bold text-base">Rp {{ number_format($bill->jumlah, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-gray-500 text-xs pt-1">
                    <span>Sudah Dibayar</span>
                    <span>Rp {{ number_format($bill->payments->sum('jumlah_bayar'), 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-red-600 font-medium text-xs">
                    <span>Sisa Utang</span>
                    <span>Rp {{ number_format($bill->sisaUtang(), 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Riwayat pembayaran --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-700">Riwayat Pembayaran</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="text-left text-xs text-gray-500 font-medium px-5 py-2.5">Tanggal</th>
                        <th class="text-left text-xs text-gray-500 font-medium px-3 py-2.5">Bank</th>
                        <th class="text-left text-xs text-gray-500 font-medium px-3 py-2.5">Keterangan</th>
                        <th class="text-right text-xs text-gray-500 font-medium px-3 py-2.5 pr-5">Jumlah</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($bill->payments as $p)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-3 text-gray-700 text-xs">{{ $p->tanggal_bayar->format('d M Y') }}</td>
                        <td class="px-3 py-3 text-gray-700 text-xs">{{ $p->bankAccount->nama_bank ?? '—' }}</td>
                        <td class="px-3 py-3 text-gray-500 text-xs">{{ $p->keterangan ?? '—' }}</td>
                        <td class="px-3 py-3 pr-5 text-right font-medium text-gray-800 text-xs">Rp {{ number_format($p->jumlah_bayar, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-5 py-8 text-center text-sm text-gray-400">Belum ada pembayaran tercatat</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Jurnal --}}
    @if($bill->journal)
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-700">Jurnal Terkait: {{ $bill->journal->nomor_jurnal }}</h2>
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
                    @foreach($bill->journal->items as $item)
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

    {{-- Modal Pembayaran --}}
    <div x-show="payModal" x-cloak
         class="fixed inset-0 bg-black/40 z-40 flex items-center justify-center p-4"
         style="display: none;">
        <div @click.outside="payModal = false" class="bg-white rounded-xl shadow-lg w-full max-w-md">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h3 class="text-sm font-semibold text-gray-700">Catat Pembayaran Tagihan</h3>
                <button @click="payModal = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form method="POST" action="{{ route('transaksi.bills.payment', $bill) }}" class="p-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Rekening Sumber <span class="text-red-500">*</span></label>
                    <select name="bank_account_id" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="">— Pilih Rekening —</option>
                        @foreach($bankAccounts as $b)
                        <option value="{{ $b->id }}">{{ $b->nama_bank }} · {{ $b->nomor_rekening }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Tanggal Bayar <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_bayar" value="{{ today()->format('Y-m-d') }}" required
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Jumlah Bayar (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" name="jumlah_bayar" min="0.01" step="1" max="{{ $bill->sisaUtang() }}"
                           value="{{ $bill->sisaUtang() }}" required
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <p class="text-xs text-gray-400 mt-1">Sisa utang: Rp {{ number_format($bill->sisaUtang(), 0, ',', '.') }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Keterangan</label>
                    <textarea name="keterangan" rows="2" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></textarea>
                </div>
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" @click="payModal = false" class="text-sm text-gray-500 hover:text-gray-700 px-4 py-2">Batal</button>
                    <button type="submit" class="bg-primary-700 hover:bg-primary-800 text-white text-sm font-medium px-5 py-2 rounded-lg transition">
                        Simpan Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

</x-app-layout>
