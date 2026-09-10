<x-app-layout title="Edit Invoice" breadcrumb="Ubah invoice {{ $invoice->nomor_invoice }}">

<div class="max-w-4xl">
<div class="bg-white rounded-xl border border-gray-100 shadow-sm">
    <div class="flex items-center gap-3 px-5 py-4 border-b border-gray-100">
        <a href="{{ route('transaksi.invoices.index') }}" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h2 class="text-sm font-semibold text-gray-700">Form Edit Invoice</h2>
    </div>

    <div class="p-5"
         x-data="{
            items: {{ Illuminate\Support\Js::from(
                old('items', $invoice->items->map(fn ($i) => [
                    'is_checked' => (bool) $i->is_checked,
                    'keterangan' => $i->keterangan,
                    'catatan' => $i->catatan,
                    'harga' => (float) $i->harga,
                ])->values()->all() ?: [
                    ['is_checked' => true, 'keterangan' => '', 'catatan' => '', 'harga' => 0],
                ])
            ) }},
            volumeTon: {{ (float) old('volume_ton', $invoice->volume_ton ?? 0) }},
            tarifPerTon: {{ (float) old('tarif_per_ton', $invoice->tarif_per_ton ?? 0) }},
            pakaiMaterai: {{ old('pakai_materai', $invoice->pakai_materai) ? 'true' : 'false' }},
            addItem() { this.items.push({ is_checked: true, keterangan: '', catatan: '', harga: 0 }) },
            removeItem(i) { if (this.items.length > 1) this.items.splice(i, 1) },
            get jasaBongkarMuat() { return this.volumeTon * this.tarifPerTon },
            get subtotalItems() { return this.items.filter(i => i.is_checked).reduce((s, i) => s + (Number(i.harga) || 0), 0) },
            get dpp() { return this.subtotalItems + this.jasaBongkarMuat },
            get ppn() { return this.dpp * 0.11 },
            get materaiNilai() { return this.pakaiMaterai ? 10000 : 0 },
            get total() { return this.dpp + this.ppn + this.materaiNilai },
            fmt(n) { return 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(n || 0)) }
         }">

        <form method="POST" action="{{ route('transaksi.invoices.update', $invoice) }}">
            @csrf
            @method('PUT')

            {{-- Bagian 1: Info Dasar --}}
            <div class="mb-5">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Informasi Invoice</p>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">No. Invoice</label>
                        <input type="text" value="{{ $invoice->nomor_invoice }}" disabled
                               class="w-full border border-gray-200 bg-gray-50 rounded-lg px-3 py-2 text-sm text-gray-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Tanggal <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal" value="{{ old('tanggal', $invoice->tanggal->format('Y-m-d')) }}" required
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Customer <span class="text-red-500">*</span></label>
                        <select name="customer_id" required
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                            @foreach($customers as $c)
                            <option value="{{ $c->id }}" @selected(old('customer_id', $invoice->customer_id) == $c->id)>{{ $c->nama }}</option>
                            @endforeach
                        </select>
                        @error('customer_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Jatuh Tempo <span class="text-red-500">*</span></label>
                        <input type="date" name="jatuh_tempo" value="{{ old('jatuh_tempo', $invoice->jatuh_tempo->format('Y-m-d')) }}" required
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-100 my-5"></div>

            {{-- Bagian 2: Checklist Kegiatan / Biaya --}}
            <div class="mb-5">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Checklist Kegiatan &amp; Biaya</p>
                    <button type="button" @click="addItem()"
                            class="inline-flex items-center gap-1.5 text-primary-600 hover:text-primary-800 text-xs font-medium">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Tambah Checklist
                    </button>
                </div>

                <div class="space-y-2">
                    <template x-for="(item, index) in items" :key="index">
                        <div class="flex items-start gap-2 bg-gray-50 rounded-lg p-3">
                            <input type="checkbox" x-model="item.is_checked" class="mt-2.5 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                            <input type="hidden" :name="'items[' + index + '][is_checked]'" :value="item.is_checked ? 1 : 0">
                            <div class="flex-1 grid grid-cols-3 gap-2">
                                <div>
                                    <label class="block text-[10px] text-gray-400 mb-0.5">Kegiatan / Keterangan</label>
                                    <input type="text" :name="'items[' + index + '][keterangan]'" x-model="item.keterangan"
                                           placeholder="cth. Dermaga Pelabuhan Umum Gresik"
                                           class="w-full border border-gray-200 rounded-lg px-2 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-primary-500">
                                </div>
                                <div>
                                    <label class="block text-[10px] text-gray-400 mb-0.5">Catatan (opsional)</label>
                                    <input type="text" :name="'items[' + index + '][catatan]'" x-model="item.catatan"
                                           placeholder="cth. Tiang pancang beton"
                                           class="w-full border border-gray-200 rounded-lg px-2 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-primary-500">
                                </div>
                                <div>
                                    <label class="block text-[10px] text-gray-400 mb-0.5">Harga (Rp)</label>
                                    <input type="number" :name="'items[' + index + '][harga]'" x-model.number="item.harga" min="0" step="1"
                                           class="w-full border border-gray-200 rounded-lg px-2 py-1.5 text-xs text-right focus:outline-none focus:ring-2 focus:ring-primary-500">
                                </div>
                            </div>
                            <button type="button" @click="removeItem(index)" x-show="items.length > 1"
                                    class="text-gray-400 hover:text-red-600 mt-6" title="Hapus checklist">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </template>
                </div>
                <p class="text-[11px] text-gray-400 mt-2">Hanya baris yang dicentang yang ikut dihitung ke DPP.</p>
                @error('items')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="border-t border-gray-100 my-5"></div>

            {{-- Bagian 3: Jasa Bongkar Muat (Volume x Tarif) --}}
            <div class="mb-5">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Jasa Bongkar Muat</p>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Volume (Ton)</label>
                        <input type="number" name="volume_ton" min="0" step="0.001"
                               x-model.number="volumeTon"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Tarif per Ton (Rp)</label>
                        <input type="number" name="tarif_per_ton" min="0" step="1"
                               x-model.number="tarifPerTon"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                </div>
                <p class="text-[11px] text-gray-400 mt-2">
                    Dicetak sebagai baris "Jasa Bongkar Muat Rp <span x-text="new Intl.NumberFormat('id-ID').format(tarifPerTon)">0</span>,- x <span x-text="volumeTon">0</span> Ton" = <span x-text="fmt(jasaBongkarMuat)">Rp 0</span>. Kosongkan jika tidak ada.
                </p>
            </div>

            <div class="border-t border-gray-100 my-5"></div>

            {{-- Bagian 4: Ringkasan & Materai --}}
            <div class="mb-5">
                <div class="bg-gray-50 rounded-xl p-4 space-y-2 text-sm">
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal Checklist (yang dicentang)</span>
                        <span class="font-medium" x-text="fmt(subtotalItems)">Rp 0</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Jasa Bongkar Muat</span>
                        <span class="font-medium" x-text="fmt(jasaBongkarMuat)">Rp 0</span>
                    </div>
                    <div class="flex justify-between text-gray-700 border-t border-gray-200 pt-2">
                        <span class="font-medium">DPP</span>
                        <span class="font-semibold" x-text="fmt(dpp)">Rp 0</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>PPN 11%</span>
                        <span class="font-medium" x-text="fmt(ppn)">Rp 0</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" x-model="pakaiMaterai" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                            Materai
                        </label>
                        <span class="font-medium" x-text="fmt(materaiNilai)">Rp 10.000</span>
                    </div>
                    <div class="flex justify-between text-primary-700 border-t border-gray-200 pt-2">
                        <span class="font-bold">Jumlah yang Dibayar</span>
                        <span class="font-bold text-base" x-text="fmt(total)">Rp 0</span>
                    </div>
                </div>
                <input type="hidden" name="pakai_materai" :value="pakaiMaterai ? 1 : 0">
            </div>

            <div class="border-t border-gray-100 my-5"></div>

            {{-- Bagian 5: Akun --}}
            <div class="mb-5">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Akun Jurnal</p>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Akun Piutang <span class="text-red-500">*</span></label>
                        <select name="piutang_coa_id" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                            @foreach($coaPiutang as $c)
                            <option value="{{ $c->id }}" @selected(old('piutang_coa_id', $invoice->piutang_coa_id) == $c->id)>{{ $c->kode_akun }} · {{ $c->nama_akun }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Akun Pendapatan <span class="text-red-500">*</span></label>
                        <select name="pendapatan_coa_id" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                            @foreach($coaPendapatan as $c)
                            <option value="{{ $c->id }}" @selected(old('pendapatan_coa_id', $invoice->pendapatan_coa_id) == $c->id)>{{ $c->kode_akun }} · {{ $c->nama_akun }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Keterangan</label>
                <textarea name="keterangan" rows="2"
                          class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">{{ old('keterangan', $invoice->keterangan) }}</textarea>
            </div>

            <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-gray-100">
                <a href="{{ route('transaksi.invoices.index') }}" class="text-sm text-gray-500 hover:text-gray-700 px-4 py-2">Batal</a>
                <button type="submit" class="bg-primary-700 hover:bg-primary-800 text-white text-sm font-medium px-5 py-2 rounded-lg transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
</div>

</x-app-layout>
