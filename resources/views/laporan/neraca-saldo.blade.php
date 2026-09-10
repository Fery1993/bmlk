<x-app-layout title="Neraca Saldo" breadcrumb="Trial balance semua akun per periode">

<div class="space-y-4">

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <form method="GET" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Periode</label>
                <select name="periode" onchange="this.form.submit()"
                        class="border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 w-56">
                    <option value="">— Pilih Periode —</option>
                    @foreach(\App\Models\FiscalPeriod::orderByDesc('tanggal_mulai')->get() as $fp)
                    <option value="{{ $fp->kode_periode }}" @selected($periode == $fp->kode_periode)>
                        {{ $fp->kode_periode }} @if($fp->status === 'closed') (Ditutup) @endif
                    </option>
                    @endforeach
                </select>
            </div>
            @if($data)
            <button type="button" onclick="window.print()" class="inline-flex items-center gap-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium px-3 py-2 rounded-lg transition">
                Cetak
            </button>
            @endif
        </form>
    </div>

    @if($data)
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h2 class="text-sm font-semibold text-gray-700">Neraca Saldo</h2>
                <p class="text-xs text-gray-400">Periode {{ $data['periode'] }}</p>
            </div>
            <span class="text-[11px] font-medium px-2.5 py-1 rounded-full {{ $data['balanced'] ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                {{ $data['balanced'] ? 'Balance' : 'Tidak Balance' }}
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="text-left text-xs text-gray-500 font-medium px-5 py-2.5">Kode</th>
                        <th class="text-left text-xs text-gray-500 font-medium px-3 py-2.5">Nama Akun</th>
                        <th class="text-right text-xs text-gray-500 font-medium px-3 py-2.5">Debit</th>
                        <th class="text-right text-xs text-gray-500 font-medium px-3 py-2.5 pr-5">Kredit</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($data['rows'] as $row)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-3 text-gray-700 text-xs">{{ $row->kode_akun }}</td>
                        <td class="px-3 py-3 text-gray-700 text-xs">{{ $row->nama_akun }}</td>
                        <td class="px-3 py-3 text-right text-xs text-gray-700">{{ $row->total_debit > 0 ? 'Rp '.number_format($row->total_debit, 0, ',', '.') : '—' }}</td>
                        <td class="px-3 py-3 pr-5 text-right text-xs text-gray-700">{{ $row->total_kredit > 0 ? 'Rp '.number_format($row->total_kredit, 0, ',', '.') : '—' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-5 py-8 text-center text-sm text-gray-400">Tidak ada data akun untuk periode ini</td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="bg-gray-50 font-semibold">
                        <td class="px-5 py-3 text-xs text-gray-600" colspan="2">Total</td>
                        <td class="px-3 py-3 text-right text-sm text-gray-800">Rp {{ number_format($data['total_debit'], 0, ',', '.') }}</td>
                        <td class="px-3 py-3 pr-5 text-right text-sm text-gray-800">Rp {{ number_format($data['total_kredit'], 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    @else
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-12 text-center text-sm text-gray-400">
        <svg class="w-10 h-10 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/></svg>
        Pilih periode untuk menampilkan neraca saldo
    </div>
    @endif

</div>

</x-app-layout>
