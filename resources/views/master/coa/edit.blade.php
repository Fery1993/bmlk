<x-app-layout title="Edit Akun" breadcrumb="Ubah data akun {{ $coa->kode_akun }}">

<x-form-card title="Form Edit Akun" :backRoute="route('master.coa.index')">

<form method="POST" action="{{ route('master.coa.update', $coa) }}">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-2 gap-4 mb-5">
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1.5">Kode Akun <span class="text-red-500">*</span></label>
            <input type="text" name="kode_akun" value="{{ old('kode_akun', $coa->kode_akun) }}" required
                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
            @error('kode_akun')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1.5">Nama Akun <span class="text-red-500">*</span></label>
            <input type="text" name="nama_akun" value="{{ old('nama_akun', $coa->nama_akun) }}" required
                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
            @error('nama_akun')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1.5">Kelompok <span class="text-red-500">*</span></label>
            <select name="kelompok" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                @foreach(['aset','kewajiban','ekuitas','pendapatan','beban'] as $k)
                <option value="{{ $k }}" @selected(old('kelompok', $coa->kelompok) == $k)>{{ ucfirst($k) }}</option>
                @endforeach
            </select>
            @error('kelompok')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1.5">Posisi Normal <span class="text-red-500">*</span></label>
            <select name="posisi_normal" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                <option value="debit"  @selected(old('posisi_normal', $coa->posisi_normal) == 'debit')>Debit</option>
                <option value="kredit" @selected(old('posisi_normal', $coa->posisi_normal) == 'kredit')>Kredit</option>
            </select>
            @error('posisi_normal')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="col-span-2">
            <label class="block text-xs font-medium text-gray-600 mb-1.5">Induk Akun (opsional)</label>
            <select name="parent_id" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                <option value="">— Tidak ada induk —</option>
                @foreach($parents as $p)
                <option value="{{ $p->id }}" @selected(old('parent_id', $coa->parent_id) == $p->id)>{{ $p->kode_akun }} · {{ $p->nama_akun }}</option>
                @endforeach
            </select>
            @error('parent_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="col-span-2 flex items-center gap-2 pt-1">
            <input type="checkbox" name="is_active" id="is_active" value="1" @checked(old('is_active', $coa->is_active))
                   class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
            <label for="is_active" class="text-sm text-gray-600">Akun aktif</label>
        </div>
    </div>

    <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-gray-100">
        <a href="{{ route('master.coa.index') }}" class="text-sm text-gray-500 hover:text-gray-700 px-4 py-2">Batal</a>
        <button type="submit" class="bg-primary-700 hover:bg-primary-800 text-white text-sm font-medium px-5 py-2 rounded-lg transition">
            Simpan Perubahan
        </button>
    </div>
</form>

</x-form-card>

</x-app-layout>
