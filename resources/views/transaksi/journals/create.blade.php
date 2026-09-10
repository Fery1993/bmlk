<x-app-layout title="Jurnal Manual Baru" breadcrumb="Input jurnal manual (double-entry)">

<div class="max-w-4xl">
<div class="bg-white rounded-xl border border-gray-100 shadow-sm">
    <div class="flex items-center gap-3 px-5 py-4 border-b border-gray-100">
        <a href="{{ route('transaksi.journals.index') }}" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h2 class="text-sm font-semibold text-gray-700">Form Jurnal Manual Baru</h2>
    </div>

    <div class="p-5"
         x-data="{
            rows: [
                { coa_id: '', debit: 0, kredit: 0, keterangan: '' },
                { coa_id: '', debit: 0, kredit: 0, keterangan: '' },
            ],
            addRow() { this.rows.push({ coa_id: '', debit: 0, kredit: 0, keterangan: '' }) },
            removeRow(i) { if (this.rows.length > 2) this.rows.splice(i, 1) },
            get totalDebit()  { return this.rows.reduce((s, r) => s + (Number(r.debit)  || 0), 0) },
            get totalKredit() { return this.rows.reduce((s, r) => s + (Number(r.kredit) || 0), 0) },
            get balanced() { return Math.round(this.totalDebit * 100) === Math.round(this.totalKredit * 100) },
            fmt(n) { return 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(n || 0)) }
         }">

        <form method="POST" action="{{ route('transaksi.journals.store') }}">
            @csrf

            <div class="grid grid-cols-2 gap-4 mb-5">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">No. Jurnal <span class="text-red-500">*</span></label>
                    <input type="text" name="nomor_jurnal" value="{{ old('nomor_jurnal', $nomorOtomatis ?? '') }}" required
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                    @error('nomor_jurnal')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Tanggal <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal" value="{{ old('tanggal', today()->format('Y-m-d')) }}" required
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Keterangan Umum</label>
                    <textarea name="keterangan" rows="2" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">{{ old('keterangan') }}</textarea>
                </div>
            </div>

            @error('items')<p class="text-red-500 text-xs mb-3 bg-red-50 border border-red-100 rounded-lg px-3 py-2">{{ $message }}</p>@enderror

            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Baris Jurnal (Debit = Kredit)</p>

            <div class="space-y-2">
                <template x-for="(row, index) in rows" :key="index">
                    <div class="flex items-start gap-2 bg-gray-50 rounded-lg p-3">
                        <div class="flex-1 grid grid-cols-4 gap-2">
                            <div class="col-span-2">
                                <select :name="'items[' + index + '][coa_id]'" x-model="row.coa_id" required
                                        class="w-full border border-gray-200 rounded-lg px-2 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-primary-500">
                                    <option value="">— Pilih Akun —</option>
                                    @foreach($coa as $c)
                                    <option value="{{ $c->id }}">{{ $c->kode_akun }} · {{ $c->nama_akun }}</option>
                                    @endforeach
                                </select>
                                <input type="text" :name="'items[' + index + '][keterangan]'" x-model="row.keterangan"
                                       placeholder="Keterangan baris (opsional)"
                                       class="mt-1 w-full border border-gray-200 rounded-lg px-2 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-primary-500">
                            </div>
                            <div>
                                <label class="block text-[10px] text-gray-400 mb-0.5">Debit</label>
                                <input type="number" :name="'items[' + index + '][debit]'" x-model.number="row.debit" min="0" step="1"
                                       class="w-full border border-gray-200 rounded-lg px-2 py-1.5 text-xs text-right focus:outline-none focus:ring-2 focus:ring-primary-500">
                            </div>
                            <div>
                                <label class="block text-[10px] text-gray-400 mb-0.5">Kredit</label>
                                <input type="number" :name="'items[' + index + '][kredit]'" x-model.number="row.kredit" min="0" step="1"
                                       class="w-full border border-gray-200 rounded-lg px-2 py-1.5 text-xs text-right focus:outline-none focus:ring-2 focus:ring-primary-500">
                            </div>
                        </div>
                        <button type="button" @click="removeRow(index)" x-show="rows.length > 2"
                                class="text-gray-400 hover:text-red-600 mt-1.5" title="Hapus baris">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </template>
            </div>

            <button type="button" @click="addRow()"
                    class="mt-3 inline-flex items-center gap-1.5 text-primary-600 hover:text-primary-800 text-xs font-medium">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Baris
            </button>

            {{-- Ringkasan total --}}
            <div class="mt-4 bg-gray-50 rounded-xl p-4 flex items-center justify-between text-sm">
                <div class="flex gap-6">
                    <div>
                        <p class="text-xs text-gray-400">Total Debit</p>
                        <p class="font-semibold text-gray-800" x-text="fmt(totalDebit)">Rp 0</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Total Kredit</p>
                        <p class="font-semibold text-gray-800" x-text="fmt(totalKredit)">Rp 0</p>
                    </div>
                </div>
                <span class="text-[11px] font-medium px-2.5 py-1 rounded-full"
                      :class="balanced ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600'"
                      x-text="balanced ? 'Balance' : 'Belum Balance'">
                </span>
            </div>

            <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-gray-100">
                <a href="{{ route('transaksi.journals.index') }}" class="text-sm text-gray-500 hover:text-gray-700 px-4 py-2">Batal</a>
                <button type="submit" :disabled="!balanced"
                        :class="balanced ? 'bg-primary-700 hover:bg-primary-800' : 'bg-gray-300 cursor-not-allowed'"
                        class="text-white text-sm font-medium px-5 py-2 rounded-lg transition">
                    Simpan Jurnal
                </button>
            </div>
        </form>
    </div>
</div>
</div>

</x-app-layout>
