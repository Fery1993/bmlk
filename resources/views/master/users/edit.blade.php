<x-app-layout title="Edit User" breadcrumb="Ubah data user {{ $user->nama }}">

<x-form-card title="Form Edit User" :backRoute="route('master.users.index')">

<form method="POST" action="{{ route('master.users.update', $user) }}">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1.5">Nama <span class="text-red-500">*</span></label>
            <input type="text" name="nama" value="{{ old('nama', $user->nama) }}" required
                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
            @error('nama')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1.5">Email <span class="text-red-500">*</span></label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
            @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1.5">Password Baru</label>
            <input type="password" name="password" minlength="8"
                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
            <p class="text-xs text-gray-400 mt-1">Kosongkan jika tidak ingin mengubah password.</p>
            @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1.5">Role <span class="text-red-500">*</span></label>
            <select name="role" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                <option value="admin"    @selected(old('role', $user->role)=='admin')>Admin</option>
                <option value="keuangan" @selected(old('role', $user->role)=='keuangan')>Keuangan</option>
                <option value="manajer"  @selected(old('role', $user->role)=='manajer')>Manajer</option>
            </select>
            @error('role')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="col-span-2 flex items-center gap-2 pt-1">
            <input type="checkbox" name="is_active" id="is_active" value="1" @checked(old('is_active', $user->is_active))
                   class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
            <label for="is_active" class="text-sm text-gray-600">User aktif</label>
        </div>
    </div>

    <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-gray-100">
        <a href="{{ route('master.users.index') }}" class="text-sm text-gray-500 hover:text-gray-700 px-4 py-2">Batal</a>
        <button type="submit" class="bg-primary-700 hover:bg-primary-800 text-white text-sm font-medium px-5 py-2 rounded-lg transition">
            Simpan Perubahan
        </button>
    </div>
</form>

</x-form-card>

</x-app-layout>
