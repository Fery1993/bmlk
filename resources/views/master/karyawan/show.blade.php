<x-app-layout title="{{ $karyawan->nama }}" breadcrumb="Detail karyawan {{ $karyawan->nik }}">

<div class="max-w-4xl space-y-4">

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
        <div class="flex items-center gap-3 px-5 py-4 border-b border-gray-100">
            <a href="{{ route('master.karyawan.index') }}" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="text-sm font-semibold text-gray-700">Detail Karyawan</h2>
            <div class="ml-auto flex items-center gap-2">
                <a href="{{ route('master.karyawan.edit', $karyawan) }}"
                   class="inline-flex items-center gap-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 text-xs font-medium px-3 py-1.5 rounded-lg transition">
                    Edit
                </a>
                <form method="POST" action="{{ route('master.karyawan.destroy', $karyawan) }}"
                      onsubmit="return confirm('Hapus karyawan {{ $karyawan->nama }}?');">
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
                <p class="text-xs text-gray-400 mb-1">NIK</p>
                <p class="font-semibold text-gray-800">{{ $karyawan->nik }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Nama</p>
                <p class="font-semibold text-gray-800">{{ $karyawan->nama }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Jabatan</p>
                <p class="text-gray-700">{{ $karyawan->jabatan ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Telepon</p>
                <p class="text-gray-700">{{ $karyawan->telepon ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Rekening Bank</p>
                <p class="text-gray-700">{{ $karyawan->rekening_bank ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Status</p>
                @if($karyawan->is_active)
                    <span class="inline-block text-[11px] font-medium px-2 py-0.5 rounded-full bg-green-100 text-green-700">Aktif</span>
                @else
                    <span class="inline-block text-[11px] font-medium px-2 py-0.5 rounded-full bg-gray-100 text-gray-500">Nonaktif</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Riwayat fee operasional --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-700">Riwayat Fee Operasional</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="text-left text-xs text-gray-500 font-medium px-5 py-2.5">No. Fee</th>
                        <th class="text-left text-xs text-gray-500 font-medium px-3 py-2.5">Tanggal</th>
                        <th class="text-right text-xs text-gray-500 font-medium px-3 py-2.5 pr-5">Jumlah</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($karyawan->operationalFees as $fee)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-3">
                            <a href="{{ route('transaksi.fee.show', $fee) }}" class="font-medium text-primary-600 hover:underline">{{ $fee->nomor_fee }}</a>
                        </td>
                        <td class="px-3 py-3 text-gray-600 text-xs">{{ $fee->tanggal->format('d M Y') }}</td>
                        <td class="px-3 py-3 pr-5 text-right font-medium text-gray-800 text-xs">Rp {{ number_format($fee->jumlah_fee, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-5 py-8 text-center text-sm text-gray-400">Belum ada riwayat fee untuk karyawan ini</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

</x-app-layout>
