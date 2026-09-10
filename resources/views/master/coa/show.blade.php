<x-app-layout title="{{ $coa->nama_akun }}" breadcrumb="Detail akun {{ $coa->kode_akun }}">

<div class="max-w-3xl space-y-4">

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
        <div class="flex items-center gap-3 px-5 py-4 border-b border-gray-100">
            <a href="{{ route('master.coa.index') }}" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="text-sm font-semibold text-gray-700">Detail Akun</h2>
            <div class="ml-auto flex items-center gap-2">
                <a href="{{ route('master.coa.edit', $coa) }}"
                   class="inline-flex items-center gap-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 text-xs font-medium px-3 py-1.5 rounded-lg transition">
                    Edit
                </a>
                <form method="POST" action="{{ route('master.coa.destroy', $coa) }}"
                      onsubmit="return confirm('Hapus akun {{ $coa->kode_akun }} - {{ $coa->nama_akun }}?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-1.5 bg-red-50 hover:bg-red-100 text-red-700 text-xs font-medium px-3 py-1.5 rounded-lg transition">
                        Hapus
                    </button>
                </form>
            </div>
        </div>
        <div class="p-5 grid grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-xs text-gray-400 mb-1">Kode Akun</p>
                <p class="font-semibold text-gray-800">{{ $coa->kode_akun }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Nama Akun</p>
                <p class="font-semibold text-gray-800">{{ $coa->nama_akun }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Kelompok</p>
                <p class="text-gray-700 capitalize">{{ $coa->kelompok }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Posisi Normal</p>
                <p class="text-gray-700 capitalize">{{ $coa->posisi_normal }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Induk Akun</p>
                <p class="text-gray-700">{{ $coa->parent->nama_akun ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Status</p>
                @if($coa->is_active)
                    <span class="inline-block text-[11px] font-medium px-2 py-0.5 rounded-full bg-green-100 text-green-700">Aktif</span>
                @else
                    <span class="inline-block text-[11px] font-medium px-2 py-0.5 rounded-full bg-gray-100 text-gray-500">Nonaktif</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Sub-akun --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-700">Sub-Akun</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="text-left text-xs text-gray-500 font-medium px-5 py-2.5">Kode</th>
                        <th class="text-left text-xs text-gray-500 font-medium px-3 py-2.5">Nama Akun</th>
                        <th class="text-center text-xs text-gray-500 font-medium px-3 py-2.5 pr-5">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($coa->children as $child)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-3">
                            <a href="{{ route('master.coa.show', $child) }}" class="font-medium text-primary-600 hover:underline">{{ $child->kode_akun }}</a>
                        </td>
                        <td class="px-3 py-3 text-gray-700">{{ $child->nama_akun }}</td>
                        <td class="px-3 py-3 pr-5 text-center">
                            @if($child->is_active)
                                <span class="inline-block text-[11px] font-medium px-2 py-0.5 rounded-full bg-green-100 text-green-700">Aktif</span>
                            @else
                                <span class="inline-block text-[11px] font-medium px-2 py-0.5 rounded-full bg-gray-100 text-gray-500">Nonaktif</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-5 py-8 text-center text-sm text-gray-400">Akun ini tidak punya sub-akun</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

</x-app-layout>
