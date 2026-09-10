<x-app-layout title="Edit Fee" breadcrumb="Ubah fee operasional {{ $fee->nomor_fee }}">

<x-form-card title="Form Edit Fee Operasional" :backRoute="route('transaksi.fee.index')">

<form method="POST" action="{{ route('transaksi.fee.update', $fee) }}">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-2 gap-4 mb-5">
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1.5">No. Fee</label>
            <input type="text" value="{{ $fee->nomor_fee }}" disabled
                   class="w-full border border-gray-200 bg-gray-50 rounded-lg px-3 py-2 text-sm text-gray-500">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1.5">Tanggal <span class="text-red-500">*</span></label>
            <input type="date" name="tanggal" value="{{ old('tanggal', $fee->tanggal->format('Y-m-d')) }}" required
                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1.5">Karyawan <span class="text-red-500">*</span></label>
            <select name="karyawan_id" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                @foreach($karyawan as $k)
                <option value="{{ $k->id }}" @selected(old('karyawan_id', $fee->karyawan_id) == $k->id)>{{ $k->nama }} @if($k->jabatan) — {{ $k->jabatan }} @endif</option>
                @endforeach
            </select>
            @error('karyawan_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1.5">Jumlah Fee (Rp) <span class="text-red-500">*</span></label>
            <input type="number" name="jumlah_fee" value="{{ old('jumlah_fee', $fee->jumlah_fee) }}" min="0.01" step="1" required
                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
            @error('jumlah_fee')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1.5">Akun Beban <span class="text-red-500">*</span></label>
            <select name="beban_coa_id" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                @foreach($coaBeban as $c)
                <option value="{{ $c->id }}" @selected(old('beban_coa_id', $fee->beban_coa_id) == $c->id)>{{ $c->kode_akun }} · {{ $c->nama_akun }}</option>
                @endforeach
            </select>
            @error('beban_coa_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1.5">Rekening Pembayar</label>
            <input type="text" value="{{ $fee->bankAccount->nama_bank ?? '—' }} · {{ $fee->bankAccount->nomor_rekening ?? '' }}" disabled
                   class="w-full border border-gray-200 bg-gray-50 rounded-lg px-3 py-2 text-sm text-gray-500">
            <p class="text-xs text-gray-400 mt-1">Rekening tidak bisa diubah di sini. Hapus &amp; catat ulang fee jika rekening salah.</p>
        </div>
    </div>

    <div>
        <label class="block text-xs font-medium text-gray-600 mb-1.5">Keterangan</label>
        <textarea name="keterangan" rows="2"
                  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">{{ old('keterangan', $fee->keterangan) }}</textarea>
    </div>

    <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-gray-100">
        <a href="{{ route('transaksi.fee.index') }}" class="text-sm text-gray-500 hover:text-gray-700 px-4 py-2">Batal</a>
        <button type="submit" class="bg-primary-700 hover:bg-primary-800 text-white text-sm font-medium px-5 py-2 rounded-lg transition">
            Simpan Perubahan
        </button>
    </div>
</form>

</x-form-card>

</x-app-layout>
