<x-app-layout title="Laporan Laba Rugi" breadcrumb="Income statement: pendapatan dan beban per periode">

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
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-700">Laporan Laba Rugi</h2>
            <p class="text-xs text-gray-400">Periode {{ $data['periode'] }}</p>
        </div>

        {{-- Pendapatan --}}
        <div class="px-5 pt-4">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Pendapatan</p>
            <div class="divide-y divide-gray-50">
                @forelse($data['pendapatan'] as $row)
                <div class="flex justify-between py-2 text-sm">
                    <span class="text-gray-600">{{ $row->nama_akun }}</span>
                    <span class="text-gray-800 font-medium">Rp {{ number_format($row->nilai, 0, ',', '.') }}</span>
                </div>
                @empty
                <div class="py-4 text-center text-sm text-gray-400">Tidak ada pendapatan pada periode ini</div>
                @endforelse
            </div>
            <div class="flex justify-between py-2.5 border-t border-gray-100 font-semibold text-sm">
                <span class="text-gray-700">Total Pendapatan</span>
                <span class="text-green-700">Rp {{ number_format($data['total_pendapatan'], 0, ',', '.') }}</span>
            </div>
        </div>

        {{-- Beban --}}
        <div class="px-5 pt-4">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Beban</p>
            <div class="divide-y divide-gray-50">
                @forelse($data['beban'] as $row)
                <div class="flex justify-between py-2 text-sm">
                    <span class="text-gray-600">{{ $row->nama_akun }}</span>
                    <span class="text-gray-800 font-medium">Rp {{ number_format($row->nilai, 0, ',', '.') }}</span>
                </div>
                @empty
                <div class="py-4 text-center text-sm text-gray-400">Tidak ada beban pada periode ini</div>
                @endforelse
            </div>
            <div class="flex justify-between py-2.5 border-t border-gray-100 font-semibold text-sm">
                <span class="text-gray-700">Total Beban</span>
                <span class="text-red-600">Rp {{ number_format($data['total_beban'], 0, ',', '.') }}</span>
            </div>
        </div>

        {{-- Laba bersih --}}
        <div class="px-5 py-4 mt-2">
            <div class="rounded-xl px-4 py-3 flex justify-between font-bold text-base {{ $data['laba_bersih'] >= 0 ? 'bg-green-50 text-green-800' : 'bg-red-50 text-red-700' }}">
                <span>{{ $data['laba_bersih'] >= 0 ? 'Laba Bersih' : 'Rugi Bersih' }}</span>
                <span>Rp {{ number_format(abs($data['laba_bersih']), 0, ',', '.') }}</span>
            </div>
        </div>
    </div>
    @else
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-12 text-center text-sm text-gray-400">
        <svg class="w-10 h-10 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
        Pilih periode untuk menampilkan laporan laba rugi
    </div>
    @endif

</div>

</x-app-layout>
