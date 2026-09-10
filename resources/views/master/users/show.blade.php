<x-app-layout title="{{ $user->nama }}" breadcrumb="Detail user">

<div class="max-w-3xl">
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
        <div class="flex items-center gap-3 px-5 py-4 border-b border-gray-100">
            <a href="{{ route('master.users.index') }}" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="text-sm font-semibold text-gray-700">Detail User</h2>
            <div class="ml-auto flex items-center gap-2">
                <a href="{{ route('master.users.edit', $user) }}"
                   class="inline-flex items-center gap-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 text-xs font-medium px-3 py-1.5 rounded-lg transition">
                    Edit
                </a>
                @if($user->id !== auth()->id())
                <form method="POST" action="{{ route('master.users.destroy', $user) }}"
                      onsubmit="return confirm('Hapus user {{ $user->nama }}?');">
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
                <p class="text-xs text-gray-400 mb-1">Nama</p>
                <p class="font-semibold text-gray-800">{{ $user->nama }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Email</p>
                <p class="text-gray-700">{{ $user->email }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Role</p>
                <p class="text-gray-700 capitalize">{{ $user->role }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Status</p>
                @if($user->is_active)
                    <span class="inline-block text-[11px] font-medium px-2 py-0.5 rounded-full bg-green-100 text-green-700">Aktif</span>
                @else
                    <span class="inline-block text-[11px] font-medium px-2 py-0.5 rounded-full bg-gray-100 text-gray-500">Nonaktif</span>
                @endif
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Terdaftar Sejak</p>
                <p class="text-gray-700">{{ $user->created_at?->format('d M Y') ?? '—' }}</p>
            </div>
        </div>
    </div>
</div>

</x-app-layout>
