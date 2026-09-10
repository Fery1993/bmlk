<x-app-layout title="Edit Vendor" breadcrumb="Ubah data vendor {{ $vendor->kode }}">

<x-form-card title="Form Edit Vendor" :backRoute="route('master.vendors.index')">

<form method="POST" action="{{ route('master.vendors.update', $vendor) }}">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1.5">Kode <span class="text-red-500">*</span></label>
            <input type="text" name="kode" value="{{ old('kode', $vendor->kode) }}" required
                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
            @error('kode')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1.5">Nama <span class="text-red-500">*</span></label>
            <input type="text" name="nama" value="{{ old('nama', $vendor->nama) }}" required
                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
            @error('nama')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1.5">Telepon</label>
            <input type="text" name="telepon" value="{{ old('telepon', $vendor->telepon) }}"
                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
            @error('telepon')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1.5">Email</label>
            <input type="email" name="email" value="{{ old('email', $vendor->email) }}"
                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
            @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1.5">NPWP</label>
            <input type="text" name="npwp" value="{{ old('npwp', $vendor->npwp) }}"
                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
            @error('npwp')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="col-span-2">
            <label class="block text-xs font-medium text-gray-600 mb-1.5">Alamat</label>
            <textarea name="alamat" rows="2" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">{{ old('alamat', $vendor->alamat) }}</textarea>
            @error('alamat')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
    </div>

    <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-gray-100">
        <a href="{{ route('master.vendors.index') }}" class="text-sm text-gray-500 hover:text-gray-700 px-4 py-2">Batal</a>
        <button type="submit" class="bg-primary-700 hover:bg-primary-800 text-white text-sm font-medium px-5 py-2 rounded-lg transition">
            Simpan Perubahan
        </button>
    </div>
</form>

</x-form-card>

</x-app-layout>
