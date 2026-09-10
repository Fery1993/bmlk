<x-app-layout title="Neraca" breadcrumb="Balance sheet: aset, kewajiban, dan ekuitas">

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
    <div class="flex items-center justify-between">
        <p class="text-xs text-gray-400">Periode {{ $data['periode'] }}</p>
        <span class="text-[11px] font-medium px-2.5 py-1 rounded-full {{ $data['balanced'] ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
            {{ $data['balanced'] ? 'Balance' : 'Tidak Balance' }}
        </span>
    </div>

    <div class="grid grid-cols-2 gap-4">
        {{-- Aset --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-700">Aset</h2>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($data['aset'] as $row)
                <div class="flex justify-between px-5 py-2.5 text-sm">
                    <span class="text-gray-600">{{ $row->nama_akun }}</span>
                    <span class="text-gray-800 font-medium">Rp {{ number_format($row->saldo_akhir, 0, ',', '.') }}</span>
                </div>
                @empty
                <div class="px-5 py-6 text-center text-sm text-gray-400">Tidak ada data aset</div>
                @endforelse
            </div>
            <div class="flex justify-between px-5 py-3 bg-gray-50 rounded-b-xl font-semibold text-sm">
                <span class="text-gray-700">Total Aset</span>
                <span class="text-gray-900">Rp {{ number_format($data['total_aset'], 0, ',', '.') }}</span>
            </div>
        </div>

        {{-- Kewajiban + Ekuitas --}}
        <div class="space-y-4">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h2 class="text-sm font-semibold text-gray-700">Kewajiban</h2>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse($data['kewajiban'] as $row)
                    <div class="flex justify-between px-5 py-2.5 text-sm">
                        <span class="text-gray-600">{{ $row->nama_akun }}</span>
                        <span class="text-gray-800 font-medium">Rp {{ number_format($row->saldo_akhir, 0, ',', '.') }}</span>
                    </div>
                    @empty
                    <div class="px-5 py-6 text-center text-sm text-gray-400">Tidak ada data kewajiban</div>
                    @endforelse
                </div>
                <div class="flex justify-between px-5 py-3 bg-gray-50 rounded-b-xl font-semibold text-sm">
                    <span class="text-gray-700">Total Kewajiban</span>
                    <span class="text-gray-900">Rp {{ number_format($data['total_kewajiban'], 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h2 class="text-sm font-semibold text-gray-700">Ekuitas</h2>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse($data['ekuitas'] as $row)
                    <div class="flex justify-between px-5 py-2.5 text-sm">
                        <span class="text-gray-600">{{ $row->nama_akun }}</span>
                        <span class="text-gray-800 font-medium">Rp {{ number_format($row->saldo_akhir, 0, ',', '.') }}</span>
                    </div>
                    @empty
                    <div class="px-5 py-6 text-center text-sm text-gray-400">Tidak ada data ekuitas</div>
                    @endforelse
                </div>
                <div class="flex justify-between px-5 py-3 bg-gray-50 rounded-b-xl font-semibold text-sm">
                    <span class="text-gray-700">Total Ekuitas</span>
                    <span class="text-gray-900">Rp {{ number_format($data['total_ekuitas'], 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="bg-primary-50 rounded-xl px-5 py-3 flex justify-between font-semibold text-sm">
                <span class="text-primary-800">Total Kewajiban + Ekuitas</span>
                <span class="text-primary-900">Rp {{ number_format($data['total_kewajiban'] + $data['total_ekuitas'], 0, ',', '.') }}</span>
            </div>
        </div>
    </div>
    @else
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-12 text-center text-sm text-gray-400">
        <svg class="w-10 h-10 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 21h18M5 21V7l8-4v18M13 21V11l6 2v8M9 9h.01M9 12h.01M9 15h.01"/></svg>
        Pilih periode untuk menampilkan neraca
    </div>
    @endif

</div>

</x-app-layout>
