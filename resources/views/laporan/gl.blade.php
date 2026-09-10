<x-app-layout title="Buku Besar" breadcrumb="General ledger / buku besar per akun">

<div class="space-y-4">

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <form method="GET" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Periode</label>
                <select name="periode" class="border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <option value="">— Pilih Periode —</option>
                    @foreach(\App\Models\FiscalPeriod::orderByDesc('tanggal_mulai')->get() as $fp)
                    <option value="{{ $fp->kode_periode }}" @selected($periode == $fp->kode_periode)>
                        {{ $fp->kode_periode }} @if($fp->status === 'closed') (Ditutup) @endif
                    </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Akun</label>
                <select name="coa_id" class="border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 w-64">
                    <option value="">— Pilih Akun —</option>
                    @foreach($coaList as $c)
                    <option value="{{ $c->id }}" @selected((string) $coaId === (string) $c->id)>{{ $c->kode_akun }} · {{ $c->nama_akun }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="bg-gray-700 hover:bg-gray-800 text-white text-sm px-4 py-1.5 rounded-lg transition">Tampilkan</button>
            @if($data)
            <button type="button" onclick="window.print()" class="inline-flex items-center gap-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium px-3 py-2 rounded-lg transition">
                Cetak
            </button>
            @endif
        </form>
    </div>

    @if($data)
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-700">{{ $data['coa']->kode_akun }} · {{ $data['coa']->nama_akun }}</h2>
            <p class="text-xs text-gray-400">Periode {{ $data['periode'] }}</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="text-left text-xs text-gray-500 font-medium px-5 py-2.5">Tanggal</th>
                        <th class="text-left text-xs text-gray-500 font-medium px-3 py-2.5">No. Jurnal</th>
                        <th class="text-left text-xs text-gray-500 font-medium px-3 py-2.5">Keterangan</th>
                        <th class="text-right text-xs text-gray-500 font-medium px-3 py-2.5">Debit</th>
                        <th class="text-right text-xs text-gray-500 font-medium px-3 py-2.5">Kredit</th>
                        <th class="text-right text-xs text-gray-500 font-medium px-3 py-2.5 pr-5">Saldo</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <tr class="bg-gray-50/60">
                        <td class="px-5 py-2.5 text-xs text-gray-500 italic" colspan="5">Saldo Awal</td>
                        <td class="px-3 py-2.5 pr-5 text-right text-xs font-medium text-gray-700">Rp {{ number_format($data['saldo_awal'], 0, ',', '.') }}</td>
                    </tr>
                    @forelse($data['mutasi'] as $row)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-3 text-gray-600 text-xs">{{ \Illuminate\Support\Carbon::parse($row['tanggal'])->format('d M Y') }}</td>
                        <td class="px-3 py-3 text-xs text-primary-600">{{ $row['nomor_jurnal'] }}</td>
                        <td class="px-3 py-3 text-gray-500 text-xs">{{ $row['keterangan'] ?? '—' }}</td>
                        <td class="px-3 py-3 text-right text-xs text-gray-700">{{ $row['debit'] > 0 ? 'Rp '.number_format($row['debit'], 0, ',', '.') : '—' }}</td>
                        <td class="px-3 py-3 text-right text-xs text-gray-700">{{ $row['kredit'] > 0 ? 'Rp '.number_format($row['kredit'], 0, ',', '.') : '—' }}</td>
                        <td class="px-3 py-3 pr-5 text-right text-xs font-medium text-gray-800">Rp {{ number_format($row['saldo_berjalan'], 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-8 text-center text-sm text-gray-400">Tidak ada mutasi pada periode ini</td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="bg-gray-50 font-semibold">
                        <td class="px-5 py-3 text-xs text-gray-600" colspan="5">Saldo Akhir</td>
                        <td class="px-3 py-3 pr-5 text-right text-sm text-gray-800">Rp {{ number_format($data['saldo_akhir'], 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    @else
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-12 text-center text-sm text-gray-400">
        <svg class="w-10 h-10 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/></svg>
        Pilih periode dan akun untuk menampilkan buku besar
    </div>
    @endif

</div>

</x-app-layout>
