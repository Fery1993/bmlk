<x-app-layout title="Tagihan Baru" breadcrumb="Input tagihan dari vendor">

<x-form-card title="Form Tagihan Baru" :backRoute="route('transaksi.bills.index')">

<form method="POST" action="{{ route('transaksi.bills.store') }}">
    @csrf

    <div class="grid grid-cols-2 gap-4 mb-5">
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1.5">No. Bill <span class="text-red-500">*</span></label>
            <input type="text" name="nomor_bill" value="{{ old('nomor_bill', $nomorOtomatis ?? '') }}" required
                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
            @error('nomor_bill')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1.5">Vendor <span class="text-red-500">*</span></label>
            <select name="vendor_id" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                <option value="">— Pilih Vendor —</option>
                @foreach($vendors as $v)
                <option value="{{ $v->id }}" @selected(old('vendor_id') == $v->id)>{{ $v->nama }}</option>
                @endforeach
            </select>
            @error('vendor_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1.5">Tanggal <span class="text-red-500">*</span></label>
            <input type="date" name="tanggal" value="{{ old('tanggal', today()->format('Y-m-d')) }}" required
                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1.5">Jatuh Tempo <span class="text-red-500">*</span></label>
            <input type="date" name="jatuh_tempo" value="{{ old('jatuh_tempo', today()->addDays(30)->format('Y-m-d')) }}" required
                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1.5">Jumlah Tagihan (Rp) <span class="text-red-500">*</span></label>
            <input type="number" name="jumlah" value="{{ old('jumlah') }}" min="0.01" step="1" required
                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
            @error('jumlah')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
    </div>

    <div class="border-t border-gray-100 my-5"></div>

    <div class="mb-5">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Akun Jurnal</p>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Akun Beban <span class="text-red-500">*</span></label>
                <select name="beban_coa_id" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <option value="">— Pilih Akun Beban —</option>
                    @foreach($coaBeban as $c)
                    <option value="{{ $c->id }}" @selected(old('beban_coa_id') == $c->id)>{{ $c->kode_akun }} · {{ $c->nama_akun }}</option>
                    @endforeach
                </select>
                @error('beban_coa_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Akun Utang <span class="text-red-500">*</span></label>
                <select name="utang_coa_id" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <option value="">— Pilih Akun Utang —</option>
                    @foreach($coaUtang as $c)
                    <option value="{{ $c->id }}" @selected(old('utang_coa_id') == $c->id)>{{ $c->kode_akun }} · {{ $c->nama_akun }}</option>
                    @endforeach
                </select>
                @error('utang_coa_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>

    <div>
        <label class="block text-xs font-medium text-gray-600 mb-1.5">Keterangan</label>
        <textarea name="keterangan" rows="2" placeholder="Keterangan tambahan..."
                  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">{{ old('keterangan') }}</textarea>
    </div>

    <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-gray-100">
        <a href="{{ route('transaksi.bills.index') }}" class="text-sm text-gray-500 hover:text-gray-700 px-4 py-2">Batal</a>
        <button type="submit" class="bg-primary-700 hover:bg-primary-800 text-white text-sm font-medium px-5 py-2 rounded-lg transition">
            Simpan Tagihan
        </button>
    </div>
</form>

</x-form-card>

</x-app-layout>
