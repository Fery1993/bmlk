<x-app-layout title="Karyawan Baru" breadcrumb="Tambah data karyawan">

<x-form-card title="Form Karyawan Baru" :backRoute="route('master.karyawan.index')">

<form method="POST" action="{{ route('master.karyawan.store') }}">
    @csrf

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1.5">NIK <span class="text-red-500">*</span></label>
            <input type="text" name="nik" value="{{ old('nik') }}" required
                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
            @error('nik')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1.5">Nama <span class="text-red-500">*</span></label>
            <input type="text" name="nama" value="{{ old('nama') }}" required
                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
            @error('nama')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1.5">Jabatan</label>
            <input type="text" name="jabatan" value="{{ old('jabatan') }}"
                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
            @error('jabatan')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1.5">Telepon</label>
            <input type="text" name="telepon" value="{{ old('telepon') }}"
                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
            @error('telepon')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="col-span-2">
            <label class="block text-xs font-medium text-gray-600 mb-1.5">Rekening Bank</label>
            <input type="text" name="rekening_bank" value="{{ old('rekening_bank') }}" placeholder="cth. BCA 1234567890 a.n. Budi Santoso"
                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
            @error('rekening_bank')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="col-span-2 flex items-center gap-2 pt-1">
            <input type="checkbox" name="is_active" id="is_active" value="1" @checked(old('is_active', true))
                   class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
            <label for="is_active" class="text-sm text-gray-600">Karyawan aktif</label>
        </div>
    </div>

    <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-gray-100">
        <a href="{{ route('master.karyawan.index') }}" class="text-sm text-gray-500 hover:text-gray-700 px-4 py-2">Batal</a>
        <button type="submit" class="bg-primary-700 hover:bg-primary-800 text-white text-sm font-medium px-5 py-2 rounded-lg transition">
            Simpan Karyawan
        </button>
    </div>
</form>

</x-form-card>

</x-app-layout>
