<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\Bill;
use App\Models\BillPayment;
use App\Models\ChartOfAccount;
use App\Models\Journal;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BillController extends Controller
{
    public function index(Request $request)
    {
        $query = Bill::with('vendor')->latest('tanggal');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_bill', 'like', "%{$search}%")
                  ->orWhereHas('vendor', fn ($v) => $v->where('nama', 'like', "%{$search}%"));
            });
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $bills = $query->paginate(15)->withQueryString();

        return view('transaksi.bills.index', compact('bills'));
    }

    public function create()
    {
        $vendors = Vendor::orderBy('nama')->get();
        $coaBeban = ChartOfAccount::where('kelompok', 'beban')->orderBy('kode_akun')->get();
        $coaUtang = ChartOfAccount::where('kelompok', 'kewajiban')->orderBy('kode_akun')->get();
        $nomorOtomatis = $this->nomorOtomatis();

        return view('transaksi.bills.create', compact('vendors', 'coaBeban', 'coaUtang', 'nomorOtomatis'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nomor_bill' => ['required', 'string', 'max:40', 'unique:bills,nomor_bill'],
            'vendor_id' => ['required', 'exists:vendors,id'],
            'tanggal' => ['required', 'date'],
            'jatuh_tempo' => ['required', 'date', 'after_or_equal:tanggal'],
            'beban_coa_id' => ['required', 'exists:chart_of_accounts,id'],
            'utang_coa_id' => ['required', 'exists:chart_of_accounts,id'],
            'jumlah' => ['required', 'numeric', 'min:0.01'],
            'keterangan' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($data, $request) {
            $journal = Journal::create([
                'nomor_jurnal' => 'JV-' . $data['nomor_bill'],
                'tanggal' => $data['tanggal'],
                'keterangan' => 'Tagihan ' . $data['nomor_bill'],
                'sumber_tipe' => 'manual',
                'is_closing' => false,
                'dibuat_oleh' => $request->user()->id,
            ]);

            $journal->items()->create([
                'coa_id' => $data['beban_coa_id'],
                'debit' => $data['jumlah'],
                'kredit' => 0,
                'keterangan' => 'Beban dari tagihan ' . $data['nomor_bill'],
            ]);

            $journal->items()->create([
                'coa_id' => $data['utang_coa_id'],
                'debit' => 0,
                'kredit' => $data['jumlah'],
                'keterangan' => 'Utang dari tagihan ' . $data['nomor_bill'],
            ]);

            Bill::create(array_merge($data, [
                'status' => 'belum_lunas',
                'dibuat_oleh' => $request->user()->id,
                'journal_id' => $journal->id,
            ]));
        });

        return redirect()->route('transaksi.bills.index')
            ->with('success', 'Tagihan berhasil dibuat.');
    }

    public function show(Bill $bill)
    {
        $bill->load('vendor', 'bebanAccount', 'utangAccount', 'payments.bankAccount', 'journal.items');
        $bankAccounts = BankAccount::where('is_active', true)->orderBy('nama_bank')->get();

        return view('transaksi.bills.show', compact('bill', 'bankAccounts'));
    }

    public function edit(Bill $bill)
    {
        if (in_array($bill->status, ['lunas', 'batal'])) {
            return back()->with('error', 'Tagihan yang sudah lunas/batal tidak bisa diedit.');
        }

        $vendors = Vendor::orderBy('nama')->get();
        $coaBeban = ChartOfAccount::where('kelompok', 'beban')->orderBy('kode_akun')->get();
        $coaUtang = ChartOfAccount::where('kelompok', 'kewajiban')->orderBy('kode_akun')->get();

        return view('transaksi.bills.edit', compact('bill', 'vendors', 'coaBeban', 'coaUtang'));
    }

    public function update(Request $request, Bill $bill)
    {
        if (in_array($bill->status, ['lunas', 'batal'])) {
            return back()->with('error', 'Tagihan yang sudah lunas/batal tidak bisa diedit.');
        }

        $data = $request->validate([
            'vendor_id' => ['required', 'exists:vendors,id'],
            'tanggal' => ['required', 'date'],
            'jatuh_tempo' => ['required', 'date', 'after_or_equal:tanggal'],
            'beban_coa_id' => ['required', 'exists:chart_of_accounts,id'],
            'utang_coa_id' => ['required', 'exists:chart_of_accounts,id'],
            'jumlah' => ['required', 'numeric', 'min:0.01'],
            'keterangan' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($data, $bill) {
            $bill->update($data);

            if ($bill->journal_id) {
                $bill->journal->items()->where('coa_id', $data['beban_coa_id'])
                    ->update(['debit' => $data['jumlah']]);
                $bill->journal->items()->where('coa_id', $data['utang_coa_id'])
                    ->update(['kredit' => $data['jumlah']]);
            }
        });

        return redirect()->route('transaksi.bills.index')
            ->with('success', 'Tagihan berhasil diperbarui.');
    }

    public function destroy(Bill $bill)
    {
        if ($bill->payments()->exists()) {
            return back()->with('error', 'Tagihan ini sudah ada pembayarannya dan tidak bisa dihapus.');
        }

        DB::transaction(function () use ($bill) {
            $journal = $bill->journal;
            $bill->delete();
            $journal?->delete();
        });

        return back()->with('success', 'Tagihan berhasil dihapus.');
    }

    public function payment(Request $request, Bill $bill)
    {
        $data = $request->validate([
            'bank_account_id' => ['required', 'exists:bank_accounts,id'],
            'tanggal_bayar' => ['required', 'date'],
            'jumlah_bayar' => ['required', 'numeric', 'min:0.01'],
            'keterangan' => ['nullable', 'string'],
        ]);

        if ($data['jumlah_bayar'] > $bill->sisaUtang()) {
            return back()->with('error', 'Jumlah bayar melebihi sisa utang tagihan ini.');
        }

        DB::transaction(function () use ($data, $bill) {
            $bankAccount = BankAccount::findOrFail($data['bank_account_id']);

            $journal = Journal::create([
                'nomor_jurnal' => 'PMT-BILL-' . $bill->nomor_bill . '-' . now()->format('His'),
                'tanggal' => $data['tanggal_bayar'],
                'keterangan' => 'Pembayaran tagihan ' . $bill->nomor_bill,
                'sumber_tipe' => 'bill_payment',
                'is_closing' => false,
                'dibuat_oleh' => auth()->id(),
            ]);

            $journal->items()->create([
                'coa_id' => $bill->utang_coa_id,
                'debit' => $data['jumlah_bayar'],
                'kredit' => 0,
                'keterangan' => 'Pelunasan utang tagihan ' . $bill->nomor_bill,
            ]);

            $journal->items()->create([
                'coa_id' => $bankAccount->coa_id,
                'debit' => 0,
                'kredit' => $data['jumlah_bayar'],
                'keterangan' => 'Pembayaran tagihan ' . $bill->nomor_bill,
            ]);

            BillPayment::create([
                'bill_id' => $bill->id,
                'bank_account_id' => $bankAccount->id,
                'tanggal_bayar' => $data['tanggal_bayar'],
                'jumlah_bayar' => $data['jumlah_bayar'],
                'keterangan' => $data['keterangan'] ?? null,
                'journal_id' => $journal->id,
            ]);

            $bankAccount->decrement('saldo_berjalan', $data['jumlah_bayar']);

            $bill->refresh();
            $sisa = $bill->sisaUtang();
            $bill->update([
                'status' => $sisa <= 0 ? 'lunas' : 'sebagian',
            ]);
        });

        return back()->with('success', 'Pembayaran tagihan berhasil dicatat.');
    }

    private function nomorOtomatis(): string
    {
        $prefix = 'BILL-' . now()->format('Ym') . '-';
        $last = Bill::where('nomor_bill', 'like', $prefix . '%')
            ->orderByDesc('nomor_bill')
            ->value('nomor_bill');

        $urut = $last ? ((int) substr($last, -4)) + 1 : 1;

        return $prefix . str_pad((string) $urut, 4, '0', STR_PAD_LEFT);
    }
}
