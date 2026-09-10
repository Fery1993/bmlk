<x-app-layout title="Chart of Account" breadcrumb="Master data akun / bagan akun perusahaan">

<x-table-wrapper title="Daftar Akun (COA)" :createRoute="route('master.coa.create')" createLabel="Tambah Akun">

    <x-slot:filter>
        <form method="GET" class="flex flex-wrap gap-3">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari kode / nama akun..."
                   class="border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 w-64">
            <select name="kelompok" class="border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                <option value="">Semua Kelompok</option>
                <option value="aset"        @selected(request('kelompok')=='aset')>Aset</option>
                <option value="kewajiban"   @selected(request('kelompok')=='kewajiban')>Kewajiban</option>
                <option value="ekuitas"     @selected(request('kelompok')=='ekuitas')>Ekuitas</option>
                <option value="pendapatan"  @selected(request('kelompok')=='pendapatan')>Pendapatan</option>
                <option value="beban"       @selected(request('kelompok')=='beban')>Beban</option>
            </select>
            <button type="submit" class="bg-gray-700 hover:bg-gray-800 text-white text-sm px-4 py-1.5 rounded-lg transition">Filter</button>
            @if(request()->hasAny(['search','kelompok']))
            <a href="{{ route('master.coa.index') }}" class="text-sm text-gray-400 hover:text-gray-600 flex items-center">Reset</a>
            @endif
        </form>
    </x-slot:filter>

    <table class="w-full text-sm">
        <thead>
            <tr class="bg-gray-50">
                <th class="text-left text-xs text-gray-500 font-medium px-5 py-3">Kode</th>
                <th class="text-left text-xs text-gray-500 font-medium px-3 py-3">Nama Akun</th>
                <th class="text-left text-xs text-gray-500 font-medium px-3 py-3">Induk Akun</th>
                <th class="text-center text-xs text-gray-500 font-medium px-3 py-3">Kelompok</th>
                <th class="text-center text-xs text-gray-500 font-medium px-3 py-3">Posisi Normal</th>
                <th class="text-center text-xs text-gray-500 font-medium px-3 py-3">Status</th>
                <th class="text-center text-xs text-gray-500 font-medium px-3 py-3 pr-5">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($coa as $akun)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-5 py-3">
                    <a href="{{ route('master.coa.show', $akun) }}" class="font-medium text-primary-600 hover:underline">
                        {{ $akun->kode_akun }}
                    </a>
                </td>
                <td class="px-3 py-3 text-gray-800">{{ $akun->nama_akun }}</td>
                <td class="px-3 py-3 text-xs text-gray-500">{{ $akun->parent->nama_akun ?? '—' }}</td>
                <td class="px-3 py-3 text-center">
                    @php
                        $kBadge = match($akun->kelompok) {
                            'aset'       => 'bg-blue-100 text-blue-700',
                            'kewajiban'  => 'bg-red-100 text-red-600',
                            'ekuitas'    => 'bg-purple-100 text-purple-700',
                            'pendapatan' => 'bg-green-100 text-green-700',
                            default      => 'bg-amber-100 text-amber-700',
                        };
                    @endphp
                    <span class="inline-block text-[11px] font-medium px-2 py-0.5 rounded-full capitalize {{ $kBadge }}">{{ $akun->kelompok }}</span>
                </td>
                <td class="px-3 py-3 text-center text-xs text-gray-600 capitalize">{{ $akun->posisi_normal }}</td>
                <td class="px-3 py-3 text-center">
                    @if($akun->is_active)
                        <span class="inline-block text-[11px] font-medium px-2 py-0.5 rounded-full bg-green-100 text-green-700">Aktif</span>
                    @else
                        <span class="inline-block text-[11px] font-medium px-2 py-0.5 rounded-full bg-gray-100 text-gray-500">Nonaktif</span>
                    @endif
                </td>
                <td class="px-3 py-3 pr-5 text-center">
                    <div class="flex items-center justify-center gap-2">
                        <a href="{{ route('master.coa.show', $akun) }}" class="text-gray-400 hover:text-primary-600" title="Detail">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </a>
                        <a href="{{ route('master.coa.edit', $akun) }}" class="text-gray-400 hover:text-amber-600" title="Edit">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>
                        <form method="POST" action="{{ route('master.coa.destroy', $akun) }}"
                              onsubmit="return confirm('Hapus akun {{ $akun->kode_akun }} - {{ $akun->nama_akun }}?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-gray-400 hover:text-red-600" title="Hapus">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-5 py-12 text-center text-sm text-gray-400">
                    <svg class="w-10 h-10 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                    Belum ada akun ditemukan
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <x-slot:pagination>
        {{ $coa->withQueryString()->links() }}
    </x-slot:pagination>

</x-table-wrapper>

</x-app-layout>
