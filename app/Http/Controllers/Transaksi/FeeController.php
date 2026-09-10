<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\ChartOfAccount;
use App\Models\Journal;
use App\Models\Karyawan;
use App\Models\OperationalFee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FeeController extends Controller
{
    public function index(Request $request)
    {
        $query = OperationalFee::with('karyawan', 'bebanAccount')->latest('tanggal');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_fee', 'like', "%{$search}%")
                  ->orWhereHas('karyawan', fn ($k) => $k->where('nama', 'like', "%{$search}%"));
            });
        }

        $fees = $query->paginate(15)->withQueryString();

        return view('transaksi.fee.index', compact('fees'));
    }

    public function create()
    {
        $karyawan = Karyawan::where('is_active', true)->orderBy('nama')->get();
        $coaBeban = ChartOfAccount::where('kelompok', 'beban')->orderBy('kode_akun')->get();
        $bankAccounts = BankAccount::where('is_active', true)->orderBy('nama_bank')->get();
        $nomorOtomatis = $this->nomorOtomatis();

        return view('transaksi.fee.create', compact('karyawan', 'coaBeban', 'bankAccounts', 'nomorOtomatis'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nomor_fee' => ['required', 'string', 'max:40', 'unique:operational_fees,nomor_fee'],
            'karyawan_id' => ['required', 'exists:karyawan,id'],
            'beban_coa_id' => ['required', 'exists:chart_of_accounts,id'],
            'bank_account_id' => ['required', 'exists:bank_accounts,id'],
            'tanggal' => ['required', 'date'],
            'jumlah_fee' => ['required', 'numeric', 'min:0.01'],
            'keterangan' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($data, $request) {
            $bankAccount = BankAccount::findOrFail($data['bank_account_id']);

            $journal = Journal::create([
                'nomor_jurnal' => 'JV-' . $data['nomor_fee'],
                'tanggal' => $data['tanggal'],
                'keterangan' => 'Fee operasional ' . $data['nomor_fee'],
                'sumber_tipe' => 'operational_fee',
                'is_closing' => false,
                'dibuat_oleh' => $request->user()->id,
            ]);

            $journal->items()->create([
                'coa_id' => $data['beban_coa_id'],
                'debit' => $data['jumlah_fee'],
                'kredit' => 0,
                'keterangan' => 'Beban fee ' . $data['nomor_fee'],
            ]);

            $journal->items()->create([
                'coa_id' => $bankAccount->coa_id,
                'debit' => 0,
                'kredit' => $data['jumlah_fee'],
                'keterangan' => 'Pembayaran fee ' . $data['nomor_fee'],
            ]);

            OperationalFee::create(array_merge($data, [
                'user_id' => $request->user()->id,
                'journal_id' => $journal->id,
            ]));

            $bankAccount->decrement('saldo_berjalan', $data['jumlah_fee']);
        });

        return redirect()->route('transaksi.fee.index')
            ->with('success', 'Fee operasional berhasil dicatat.');
    }

    public function show(OperationalFee $fee)
    {
        $fee->load('karyawan', 'bebanAccount', 'bankAccount', 'user', 'journal.items');

        return view('transaksi.fee.show', compact('fee'));
    }

    public function edit(OperationalFee $fee)
    {
        $karyawan = Karyawan::where('is_active', true)->orderBy('nama')->get();
        $coaBeban = ChartOfAccount::where('kelompok', 'beban')->orderBy('kode_akun')->get();
        $bankAccounts = BankAccount::where('is_active', true)->orderBy('nama_bank')->get();

        return view('transaksi.fee.edit', compact('fee', 'karyawan', 'coaBeban', 'bankAccounts'));
    }

    public function update(Request $request, OperationalFee $fee)
    {
        $data = $request->validate([
            'karyawan_id' => ['required', 'exists:karyawan,id'],
            'beban_coa_id' => ['required', 'exists:chart_of_accounts,id'],
            'tanggal' => ['required', 'date'],
            'jumlah_fee' => ['required', 'numeric', 'min:0.01'],
            'keterangan' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($data, $fee) {
            $selisih = $data['jumlah_fee'] - (float) $fee->jumlah_fee;

            $fee->update($data);

            if ($fee->journal_id) {
                $fee->journal->items()->where('coa_id', $data['beban_coa_id'])
                    ->update(['debit' => $data['jumlah_fee']]);
                $fee->journal->items()->where('coa_id', $fee->bankAccount->coa_id)
                    ->update(['kredit' => $data['jumlah_fee']]);
            }

            if ($selisih !== 0.0) {
                $fee->bankAccount->decrement('saldo_berjalan', $selisih);
            }
        });

        return redirect()->route('transaksi.fee.index')
            ->with('success', 'Fee operasional berhasil diperbarui.');
    }

    public function destroy(OperationalFee $fee)
    {
        DB::transaction(function () use ($fee) {
            $fee->bankAccount->increment('saldo_berjalan', $fee->jumlah_fee);
            $journal = $fee->journal;
            $fee->delete();
            $journal?->delete();
        });

        return back()->with('success', 'Fee operasional berhasil dihapus.');
    }

    private function nomorOtomatis(): string
    {
        $prefix = 'FEE-' . now()->format('Ym') . '-';
        $last = OperationalFee::where('nomor_fee', 'like', $prefix . '%')
            ->orderByDesc('nomor_fee')
            ->value('nomor_fee');

        $urut = $last ? ((int) substr($last, -4)) + 1 : 1;

        return $prefix . str_pad((string) $urut, 4, '0', STR_PAD_LEFT);
    }
}
