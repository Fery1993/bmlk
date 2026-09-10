<x-app-layout title="{{ $invoice->nomor_invoice }}" breadcrumb="Detail invoice penjualan jasa bongkar muat">

<div x-data="{ payModal: false }" class="max-w-5xl space-y-4">

    {{-- Header --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
        <div class="flex items-center gap-3 px-5 py-4 border-b border-gray-100">
            <a href="{{ route('transaksi.invoices.index') }}" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h2 class="text-sm font-semibold text-gray-700">{{ $invoice->nomor_invoice }}</h2>
                <p class="text-xs text-gray-400">{{ $invoice->tanggal->format('d M Y') }}</p>
            </div>
            @php
                $badge = match($invoice->status) {
                    'lunas' => 'bg-green-100 text-green-700',
                    'sebagian' => 'bg-amber-100 text-amber-700',
                    'batal' => 'bg-gray-100 text-gray-500',
                    default => 'bg-red-100 text-red-600',
                };
                $label = match($invoice->status) {
                    'lunas' => 'Lunas', 'sebagian' => 'Sebagian', 'batal' => 'Batal', default => 'Belum Lunas',
                };
            @endphp
            <span class="text-[11px] font-medium px-2 py-0.5 rounded-full {{ $badge }}">{{ $label }}</span>

            <div class="ml-auto flex items-center gap-2">
                @if($invoice->status !== 'lunas' && $invoice->status !== 'batal')
                <a href="{{ route('transaksi.invoices.edit', $invoice) }}"
                   class="inline-flex items-center gap-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 text-xs font-medium px-3 py-1.5 rounded-lg transition">
                    Edit
                </a>
                <button @click="payModal = true"
                        class="inline-flex items-center gap-1.5 bg-primary-700 hover:bg-primary-800 text-white text-xs font-medium px-3 py-1.5 rounded-lg transition">
                    Catat Pembayaran
                </button>
                @endif
                <a href="{{ route('transaksi.invoices.cetak', $invoice) }}" target="_blank"
                   class="inline-flex items-center gap-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium px-3 py-1.5 rounded-lg transition">
                    Cetak
                </a>
                @if(!$invoice->payments->count())
                <form method="POST" action="{{ route('transaksi.invoices.destroy', $invoice) }}"
                      onsubmit="return confirm('Hapus invoice {{ $invoice->nomor_invoice }}?');">
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
                <p class="text-xs text-gray-400 mb-1">Customer</p>
                <p class="font-medium text-gray-800">{{ $invoice->customer->nama }}</p>
                <p class="text-xs text-gray-400">{{ $invoice->customer->npwp ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Jatuh Tempo</p>
                <p class="font-medium {{ $invoice->jatuh_tempo->isPast() && $invoice->status !== 'lunas' ? 'text-red-600' : 'text-gray-800' }}">
                    {{ $invoice->jatuh_tempo->format('d M Y') }}
                </p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Volume</p>
                <p class="text-gray-700">{{ number_format($invoice->volume_ton ?? 0, 3, ',', '.') }} ton</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Tarif per Ton</p>
                <p class="text-gray-700">Rp {{ number_format($invoice->tarif_per_ton ?? 0, 0, ',', '.') }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Akun Piutang</p>
                <p class="text-gray-700">{{ $invoice->piutangAccount->kode_akun }} · {{ $invoice->piutangAccount->nama_akun }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Akun Pendapatan</p>
                <p class="text-gray-700">{{ $invoice->pendapatanAccount->kode_akun }} · {{ $invoice->pendapatanAccount->nama_akun }}</p>
            </div>
            @if($invoice->keterangan)
            <div class="col-span-2">
                <p class="text-xs text-gray-400 mb-1">Keterangan</p>
                <p class="text-gray-700">{{ $invoice->keterangan }}</p>
            </div>
            @endif
        </div>

        {{-- Checklist kegiatan & biaya --}}
        @if($invoice->items->count())
        <div class="px-5 pb-2">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Checklist Kegiatan &amp; Biaya</p>
            <div class="border border-gray-100 rounded-xl overflow-hidden">
                <table class="w-full text-sm">
                    <tbody class="divide-y divide-gray-50">
                        @foreach($invoice->items as $item)
                        <tr class="{{ $item->is_checked ? '' : 'opacity-40' }}">
                            <td class="px-4 py-2 w-6">
                                @if($item->is_checked)
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                @else
                                <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                @endif
                            </td>
                            <td class="px-2 py-2 text-gray-700 text-xs">
                                {{ $item->keterangan }}
                                @if($item->catatan)
                                <span class="block text-gray-400">{{ $item->catatan }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-right text-xs text-gray-700 whitespace-nowrap">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                        @if($invoice->jasaBongkarMuat() > 0)
                        <tr>
                            <td class="px-4 py-2 w-6">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </td>
                            <td class="px-2 py-2 text-gray-700 text-xs">
                                Jasa Bongkar Muat Rp {{ number_format($invoice->tarif_per_ton, 0, ',', '.') }},- x {{ number_format($invoice->volume_ton, 3, ',', '.') }} Ton
                            </td>
                            <td class="px-4 py-2 text-right text-xs text-gray-700 whitespace-nowrap">Rp {{ number_format($invoice->jasaBongkarMuat(), 0, ',', '.') }}</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- Rincian nilai --}}
        <div class="px-5 pb-5 pt-3">
            <div class="bg-gray-50 rounded-xl p-4 space-y-2 text-sm">
                <div class="flex justify-between text-gray-700">
                    <span class="font-medium">DPP</span>
                    <span class="font-semibold">Rp {{ number_format($invoice->dpp, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-gray-600">
                    <span>PPN</span>
                    <span>Rp {{ number_format($invoice->ppn, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-gray-600">
                    <span>Materai {{ $invoice->pakai_materai ? '' : '(tidak dipakai)' }}</span>
                    <span>Rp {{ number_format($invoice->materai, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-primary-700 border-t border-gray-200 pt-2">
                    <span class="font-bold">Total Tagihan</span>
                    <span class="font-bold text-base">Rp {{ number_format($invoice->jumlah, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-gray-500 text-xs pt-1">
                    <span>Sudah Dibayar</span>
                    <span>Rp {{ number_format($invoice->payments->sum('jumlah_bayar'), 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-red-600 font-medium text-xs">
                    <span>Sisa Tagihan</span>
                    <span>Rp {{ number_format($invoice->sisaTagihan(), 0, ',', '.') }}</span>
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
                    @forelse($invoice->payments as $p)
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
    @if($invoice->journal)
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-700">Jurnal Terkait: {{ $invoice->journal->nomor_jurnal }}</h2>
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
                    @foreach($invoice->journal->items as $item)
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
                <h3 class="text-sm font-semibold text-gray-700">Catat Pembayaran Invoice</h3>
                <button @click="payModal = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form method="POST" action="{{ route('transaksi.invoices.payment', $invoice) }}" class="p-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Rekening Tujuan <span class="text-red-500">*</span></label>
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
                    <input type="number" name="jumlah_bayar" min="0.01" step="1" max="{{ $invoice->sisaTagihan() }}"
                           value="{{ $invoice->sisaTagihan() }}" required
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <p class="text-xs text-gray-400 mt-1">Sisa tagihan: Rp {{ number_format($invoice->sisaTagihan(), 0, ',', '.') }}</p>
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
