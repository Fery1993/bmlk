<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use App\Models\Journal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class JournalController extends Controller
{
    public function index(Request $request)
    {
        $query = Journal::withCount('items')->latest('tanggal');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_jurnal', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        // Jurnal manual saja yang bisa diedit/dihapus lewat menu ini; jurnal otomatis
        // (dari invoice/bill/fee/closing) hanya bisa dilihat.
        $journals = $query->paginate(15)->withQueryString();

        return view('transaksi.journals.index', compact('journals'));
    }

    public function create()
    {
        $coa = ChartOfAccount::where('is_active', true)->orderBy('kode_akun')->get();
        $nomorOtomatis = $this->nomorOtomatis();

        return view('transaksi.journals.create', compact('coa', 'nomorOtomatis'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        DB::transaction(function () use ($data, $request) {
            $journal = Journal::create([
                'nomor_jurnal' => $data['nomor_jurnal'],
                'tanggal' => $data['tanggal'],
                'keterangan' => $data['keterangan'] ?? null,
                'sumber_tipe' => 'manual',
                'is_closing' => false,
                'dibuat_oleh' => $request->user()->id,
            ]);

            foreach ($data['items'] as $item) {
                $journal->items()->create([
                    'coa_id' => $item['coa_id'],
                    'debit' => $item['debit'] ?? 0,
                    'kredit' => $item['kredit'] ?? 0,
                    'keterangan' => $item['keterangan'] ?? null,
                ]);
            }
        });

        return redirect()->route('transaksi.journals.index')
            ->with('success', 'Jurnal manual berhasil disimpan.');
    }

    public function show(Journal $journal)
    {
        $journal->load('items.chartOfAccount', 'creator');

        return view('transaksi.journals.show', compact('journal'));
    }

    public function edit(Journal $journal)
    {
        if ($journal->sumber_tipe !== 'manual') {
            return back()->with('error', 'Hanya jurnal manual yang bisa diedit. Jurnal ini dibuat otomatis dari transaksi lain.');
        }

        $journal->load('items');
        $coa = ChartOfAccount::where('is_active', true)->orderBy('kode_akun')->get();

        return view('transaksi.journals.edit', compact('journal', 'coa'));
    }

    public function update(Request $request, Journal $journal)
    {
        if ($journal->sumber_tipe !== 'manual') {
            return back()->with('error', 'Hanya jurnal manual yang bisa diedit.');
        }

        $data = $this->validated($request, $journal->id);

        DB::transaction(function () use ($data, $journal) {
            $journal->update([
                'tanggal' => $data['tanggal'],
                'keterangan' => $data['keterangan'] ?? null,
            ]);

            $journal->items()->delete();

            foreach ($data['items'] as $item) {
                $journal->items()->create([
                    'coa_id' => $item['coa_id'],
                    'debit' => $item['debit'] ?? 0,
                    'kredit' => $item['kredit'] ?? 0,
                    'keterangan' => $item['keterangan'] ?? null,
                ]);
            }
        });

        return redirect()->route('transaksi.journals.index')
            ->with('success', 'Jurnal manual berhasil diperbarui.');
    }

    public function destroy(Journal $journal)
    {
        if ($journal->sumber_tipe !== 'manual') {
            return back()->with('error', 'Hanya jurnal manual yang bisa dihapus lewat menu ini.');
        }

        $journal->delete(); // journal_items ikut terhapus via cascadeOnDelete

        return back()->with('success', 'Jurnal manual berhasil dihapus.');
    }

    /**
     * Validasi input jurnal + memastikan total debit = total kredit (double-entry).
     */
    private function validated(Request $request, ?int $ignoreId = null): array
    {
        $data = $request->validate([
            'nomor_jurnal' => ['required', 'string', 'max:40', Rule::unique('journals', 'nomor_jurnal')->ignore($ignoreId)],
            'tanggal' => ['required', 'date'],
            'keterangan' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:2'],
            'items.*.coa_id' => ['required', 'exists:chart_of_accounts,id'],
            'items.*.debit' => ['nullable', 'numeric', 'min:0'],
            'items.*.kredit' => ['nullable', 'numeric', 'min:0'],
            'items.*.keterangan' => ['nullable', 'string'],
        ]);

        $totalDebit = collect($data['items'])->sum(fn ($i) => (float) ($i['debit'] ?? 0));
        $totalKredit = collect($data['items'])->sum(fn ($i) => (float) ($i['kredit'] ?? 0));

        if (round($totalDebit, 2) !== round($totalKredit, 2)) {
            throw ValidationException::withMessages([
                'items' => "Total debit (Rp " . number_format($totalDebit, 0, ',', '.') . ") harus sama dengan total kredit (Rp " . number_format($totalKredit, 0, ',', '.') . ").",
            ]);
        }

        if ($totalDebit == 0) {
            throw ValidationException::withMessages([
                'items' => 'Jurnal tidak boleh kosong, isi minimal satu baris debit dan satu baris kredit.',
            ]);
        }

        return $data;
    }

    private function nomorOtomatis(): string
    {
        $prefix = 'JV-MAN-' . now()->format('Ym') . '-';
        $last = Journal::where('nomor_jurnal', 'like', $prefix . '%')
            ->orderByDesc('nomor_jurnal')
            ->value('nomor_jurnal');

        $urut = $last ? ((int) substr($last, -4)) + 1 : 1;

        return $prefix . str_pad((string) $urut, 4, '0', STR_PAD_LEFT);
    }
}
