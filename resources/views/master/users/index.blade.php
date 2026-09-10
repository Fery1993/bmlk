<x-app-layout title="User" breadcrumb="Master data pengguna sistem">

<x-table-wrapper title="Daftar User" :createRoute="route('master.users.create')" createLabel="Tambah User">

    <x-slot:filter>
        <form method="GET" class="flex flex-wrap gap-3">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari nama / email..."
                   class="border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 w-64">
            <button type="submit" class="bg-gray-700 hover:bg-gray-800 text-white text-sm px-4 py-1.5 rounded-lg transition">Filter</button>
            @if(request()->hasAny(['search']))
            <a href="{{ route('master.users.index') }}" class="text-sm text-gray-400 hover:text-gray-600 flex items-center">Reset</a>
            @endif
        </form>
    </x-slot:filter>

    <table class="w-full text-sm">
        <thead>
            <tr class="bg-gray-50">
                <th class="text-left text-xs text-gray-500 font-medium px-5 py-3">Nama</th>
                <th class="text-left text-xs text-gray-500 font-medium px-3 py-3">Email</th>
                <th class="text-center text-xs text-gray-500 font-medium px-3 py-3">Role</th>
                <th class="text-center text-xs text-gray-500 font-medium px-3 py-3">Status</th>
                <th class="text-center text-xs text-gray-500 font-medium px-3 py-3 pr-5">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($users as $u)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-5 py-3">
                    <a href="{{ route('master.users.show', $u) }}" class="font-medium text-primary-600 hover:underline">{{ $u->nama }}</a>
                </td>
                <td class="px-3 py-3 text-gray-600 text-xs">{{ $u->email }}</td>
                <td class="px-3 py-3 text-center">
                    @php
                        $rBadge = match($u->role) {
                            'admin'    => 'bg-purple-100 text-purple-700',
                            'keuangan' => 'bg-blue-100 text-blue-700',
                            default    => 'bg-gray-100 text-gray-600',
                        };
                    @endphp
                    <span class="inline-block text-[11px] font-medium px-2 py-0.5 rounded-full capitalize {{ $rBadge }}">{{ $u->role }}</span>
                </td>
                <td class="px-3 py-3 text-center">
                    @if($u->is_active)
                        <span class="inline-block text-[11px] font-medium px-2 py-0.5 rounded-full bg-green-100 text-green-700">Aktif</span>
                    @else
                        <span class="inline-block text-[11px] font-medium px-2 py-0.5 rounded-full bg-gray-100 text-gray-500">Nonaktif</span>
                    @endif
                </td>
                <td class="px-3 py-3 pr-5 text-center">
                    <div class="flex items-center justify-center gap-2">
                        <a href="{{ route('master.users.show', $u) }}" class="text-gray-400 hover:text-primary-600" title="Detail">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </a>
                        <a href="{{ route('master.users.edit', $u) }}" class="text-gray-400 hover:text-amber-600" title="Edit">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>
                        @if($u->id !== auth()->id())
                        <form method="POST" action="{{ route('master.users.destroy', $u) }}"
                              onsubmit="return confirm('Hapus user {{ $u->nama }}?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-gray-400 hover:text-red-600" title="Hapus">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-5 py-12 text-center text-sm text-gray-400">
                    <svg class="w-10 h-10 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Belum ada user ditemukan
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <x-slot:pagination>
        {{ $users->withQueryString()->links() }}
    </x-slot:pagination>

</x-table-wrapper>

</x-app-layout>
