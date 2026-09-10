<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\ChartOfAccount;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Journal;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with('customer')->latest('tanggal');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_invoice', 'like', "%{$search}%")
                  ->orWhereHas('customer', fn ($c) => $c->where('nama', 'like', "%{$search}%"));
            });
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($periode = $request->get('periode')) {
            // format input[type=month]: YYYY-MM
            $query->whereYear('tanggal', substr($periode, 0, 4))
                  ->whereMonth('tanggal', substr($periode, 5, 2));
        }

        $invoices = $query->paginate(15)->withQueryString();

        return view('transaksi.invoices.index', compact('invoices'));
    }

    public function create()
    {
        $customers = Customer::orderBy('nama')->get();
        $coaPiutang = ChartOfAccount::where('kelompok', 'aset')->orderBy('kode_akun')->get();
        $coaPendapatan = ChartOfAccount::where('kelompok', 'pendapatan')->orderBy('kode_akun')->get();
        $nomorOtomatis = $this->nomorOtomatis();

        return view('transaksi.invoices.create', compact('customers', 'coaPiutang', 'coaPendapatan', 'nomorOtomatis'));
    }

    public function store(Request $request)
    {
        $data = $this->validasi($request);

        DB::transaction(function () use ($data, $request) {
            $totals = $this->hitungTotal($data);

            // Jurnal pengakuan piutang & pendapatan saat invoice diterbitkan.
            $journal = Journal::create([
                'nomor_jurnal' => 'JV-' . str_replace('/', '-', $data['nomor_invoice']),
                'tanggal' => $data['tanggal'],
                'keterangan' => 'Invoice ' . $data['nomor_invoice'],
                'sumber_tipe' => 'manual',
                'is_closing' => false,
                'dibuat_oleh' => $request->user()->id,
            ]);

            $journal->items()->create([
                'coa_id' => $data['piutang_coa_id'],
                'debit' => $totals['jumlah'],
                'kredit' => 0,
                'keterangan' => 'Piutang invoice ' . $data['nomor_invoice'],
            ]);

            $journal->items()->create([
                'coa_id' => $data['pendapatan_coa_id'],
                'debit' => 0,
                'kredit' => $totals['jumlah'],
                'keterangan' => 'Pendapatan invoice ' . $data['nomor_invoice'],
            ]);

            $invoice = Invoice::create([
                'nomor_invoice' => $data['nomor_invoice'],
                'customer_id' => $data['customer_id'],
                'tanggal' => $data['tanggal'],
                'jatuh_tempo' => $data['jatuh_tempo'],
                'pendapatan_coa_id' => $data['pendapatan_coa_id'],
                'piutang_coa_id' => $data['piutang_coa_id'],
                'volume_ton' => $data['volume_ton'] ?? null,
                'tarif_per_ton' => $data['tarif_per_ton'] ?? null,
                'pakai_materai' => $data['pakai_materai'] ?? false,
                'dpp' => $totals['dpp'],
                'ppn' => $totals['ppn'],
                'materai' => $totals['materai'],
                'jumlah' => $totals['jumlah'],
                'status' => 'belum_lunas',
                'keterangan' => $data['keterangan'] ?? null,
                'dibuat_oleh' => $request->user()->id,
                'journal_id' => $journal->id,
            ]);

            $this->simpanItems($invoice, $data['items'] ?? []);
        });

        return redirect()->route('transaksi.invoices.index')
            ->with('success', 'Invoice berhasil dibuat.');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load('customer', 'pendapatanAccount', 'piutangAccount', 'items', 'payments.bankAccount', 'journal.items');
        $bankAccounts = BankAccount::where('is_active', true)->orderBy('nama_bank')->get();

        return view('transaksi.invoices.show', compact('invoice', 'bankAccounts'));
    }

    public function edit(Invoice $invoice)
    {
        if ($invoice->status === 'lunas' || $invoice->status === 'batal') {
            return back()->with('error', 'Invoice yang sudah lunas/batal tidak bisa diedit.');
        }

        $invoice->load('items');
        $customers = Customer::orderBy('nama')->get();
        $coaPiutang = ChartOfAccount::where('kelompok', 'aset')->orderBy('kode_akun')->get();
        $coaPendapatan = ChartOfAccount::where('kelompok', 'pendapatan')->orderBy('kode_akun')->get();

        return view('transaksi.invoices.edit', compact('invoice', 'customers', 'coaPiutang', 'coaPendapatan'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        if ($invoice->status === 'lunas' || $invoice->status === 'batal') {
            return back()->with('error', 'Invoice yang sudah lunas/batal tidak bisa diedit.');
        }

        $data = $this->validasi($request, $invoice);

        DB::transaction(function () use ($data, $invoice) {
            $totals = $this->hitungTotal($data);

            $invoice->update([
                'customer_id' => $data['customer_id'],
                'tanggal' => $data['tanggal'],
                'jatuh_tempo' => $data['jatuh_tempo'],
                'pendapatan_coa_id' => $data['pendapatan_coa_id'],
                'piutang_coa_id' => $data['piutang_coa_id'],
                'volume_ton' => $data['volume_ton'] ?? null,
                'tarif_per_ton' => $data['tarif_per_ton'] ?? null,
                'pakai_materai' => $data['pakai_materai'] ?? false,
                'dpp' => $totals['dpp'],
                'ppn' => $totals['ppn'],
                'materai' => $totals['materai'],
                'jumlah' => $totals['jumlah'],
                'keterangan' => $data['keterangan'] ?? null,
            ]);

            $invoice->items()->delete();
            $this->simpanItems($invoice, $data['items'] ?? []);

            // Sinkronkan ulang jurnal supaya tetap balance dengan jumlah terbaru.
            // Dihapus lalu dibuat ulang (bukan di-update ke 0 dulu) supaya tidak
            // pernah melewati kondisi debit=0 DAN kredit=0 sekaligus, yang akan
            // melanggar CHECK constraint chk_debit_xor_kredit. Pendekatan ini juga
            // otomatis menangani kasus akun COA piutang/pendapatan diganti saat edit.
            if ($invoice->journal_id) {
                $invoice->journal->items()->delete();
                $invoice->journal->items()->createMany([
                    [
                        'coa_id' => $data['piutang_coa_id'],
                        'debit' => $totals['jumlah'],
                        'kredit' => 0,
                        'keterangan' => 'Piutang invoice ' . $invoice->nomor_invoice,
                    ],
                    [
                        'coa_id' => $data['pendapatan_coa_id'],
                        'debit' => 0,
                        'kredit' => $totals['jumlah'],
                        'keterangan' => 'Pendapatan invoice ' . $invoice->nomor_invoice,
                    ],
                ]);
                $invoice->journal->update(['tanggal' => $data['tanggal']]);
            }
        });

        return redirect()->route('transaksi.invoices.index')
            ->with('success', 'Invoice berhasil diperbarui.');
    }

    public function destroy(Invoice $invoice)
    {
        if ($invoice->payments()->exists()) {
            return back()->with('error', 'Invoice ini sudah ada pembayarannya dan tidak bisa dihapus.');
        }

        DB::transaction(function () use ($invoice) {
            $journal = $invoice->journal;
            $invoice->delete(); // invoice_items ikut terhapus via cascadeOnDelete
            $journal?->delete(); // journal_items ikut terhapus via cascadeOnDelete
        });

        return back()->with('success', 'Invoice berhasil dihapus.');
    }

    /**
     * Catat penerimaan pembayaran atas invoice.
     */
    public function payment(Request $request, Invoice $invoice)
    {
        $data = $request->validate([
            'bank_account_id' => ['required', 'exists:bank_accounts,id'],
            'tanggal_bayar' => ['required', 'date'],
            'jumlah_bayar' => ['required', 'numeric', 'min:0.01'],
            'keterangan' => ['nullable', 'string'],
        ]);

        if ($data['jumlah_bayar'] > $invoice->sisaTagihan()) {
            return back()->with('error', 'Jumlah bayar melebihi sisa tagihan invoice ini.');
        }

        DB::transaction(function () use ($data, $invoice) {
            $bankAccount = BankAccount::findOrFail($data['bank_account_id']);

            $journal = Journal::create([
                'nomor_jurnal' => 'PMT-INV-' . str_replace('/', '-', $invoice->nomor_invoice) . '-' . now()->format('His'),
                'tanggal' => $data['tanggal_bayar'],
                'keterangan' => 'Pembayaran invoice ' . $invoice->nomor_invoice,
                'sumber_tipe' => 'invoice_payment',
                'is_closing' => false,
                'dibuat_oleh' => $data['dibuat_oleh'] ?? auth()->id(),
            ]);

            $journal->items()->create([
                'coa_id' => $bankAccount->coa_id,
                'debit' => $data['jumlah_bayar'],
                'kredit' => 0,
                'keterangan' => 'Penerimaan pembayaran invoice ' . $invoice->nomor_invoice,
            ]);

            $journal->items()->create([
                'coa_id' => $invoice->piutang_coa_id,
                'debit' => 0,
                'kredit' => $data['jumlah_bayar'],
                'keterangan' => 'Pelunasan piutang invoice ' . $invoice->nomor_invoice,
            ]);

            InvoicePayment::create([
                'invoice_id' => $invoice->id,
                'bank_account_id' => $bankAccount->id,
                'tanggal_bayar' => $data['tanggal_bayar'],
                'jumlah_bayar' => $data['jumlah_bayar'],
                'keterangan' => $data['keterangan'] ?? null,
                'journal_id' => $journal->id,
            ]);

            $bankAccount->increment('saldo_berjalan', $data['jumlah_bayar']);

            $invoice->refresh();
            $sisa = $invoice->sisaTagihan();
            $invoice->update([
                'status' => $sisa <= 0 ? 'lunas' : 'sebagian',
            ]);
        });

        return back()->with('success', 'Pembayaran invoice berhasil dicatat.');
    }

    /**
     * Halaman cetak invoice, layout kop surat terpisah dari dashboard
     * (tanpa sidebar) supaya hasil print rapi sesuai contoh fisik.
     */
    public function cetak(Invoice $invoice)
    {
        $invoice->load('customer', 'pendapatanAccount', 'piutangAccount', 'items');
        $bankAccounts = BankAccount::where('is_active', true)->orderBy('nama_bank')->get();

        return view('transaksi.invoices.cetak', compact('invoice', 'bankAccounts'));
    }

    /**
     * Validasi input form create/update. Total (dpp/ppn/materai/jumlah)
     * TIDAK divalidasi dari input klien — semua dihitung ulang di server
     * lewat hitungTotal() supaya tidak bisa dimanipulasi dari luar.
     */
    private function validasi(Request $request, ?Invoice $invoice = null): array
    {
        $rules = [
            'tanggal' => ['required', 'date'],
            'customer_id' => ['required', 'exists:customers,id'],
            'jatuh_tempo' => ['required', 'date', 'after_or_equal:tanggal'],
            'volume_ton' => ['nullable', 'numeric', 'min:0'],
            'tarif_per_ton' => ['nullable', 'numeric', 'min:0'],
            'pakai_materai' => ['nullable', 'boolean'],
            'piutang_coa_id' => ['required', 'exists:chart_of_accounts,id'],
            'pendapatan_coa_id' => ['required', 'exists:chart_of_accounts,id'],
            'keterangan' => ['nullable', 'string'],
            'items' => ['nullable', 'array'],
            // Hanya wajibkan keterangan/harga jika baris tsb memang dicentang (is_checked)
            // DAN keterangannya benar-benar diisi. Baris checklist default yang
            // sengaja dibiarkan kosong (tidak dipakai) tidak boleh membuat form gagal submit.
            'items.*.keterangan' => ['nullable', 'string', 'max:255'],
            'items.*.catatan' => ['nullable', 'string', 'max:255'],
            'items.*.harga' => ['nullable', 'numeric', 'min:0'],
            'items.*.is_checked' => ['nullable', 'boolean'],
        ];

        if ($invoice === null) {
            $rules['nomor_invoice'] = ['required', 'string', 'max:40', 'unique:invoices,nomor_invoice'];
        }

        $validator = validator($request->all(), $rules);

        // Validasi tambahan: baris checklist yang DICENTANG wajib punya keterangan.
        // Baris yang tidak dicentang / tidak dipakai boleh kosong sepenuhnya.
        $validator->after(function ($validator) use ($request) {
            foreach ((array) $request->input('items', []) as $index => $item) {
                $checked = ! empty($item['is_checked']);
                $kosong = blank($item['keterangan'] ?? null);

                if ($checked && $kosong) {
                    $validator->errors()->add(
                        "items.$index.keterangan",
                        'Baris checklist yang dicentang wajib diisi keterangannya (atau hilangkan centangnya).'
                    );
                }
            }
        });

        $data = $validator->validate();
        $data['pakai_materai'] = $request->boolean('pakai_materai');

        return $data;
    }

    /**
     * Hitung ulang DPP, PPN, materai, dan jumlah total dari baris
     * checklist + jasa bongkar muat (volume x tarif) yang dikirim.
     */
    private function hitungTotal(array $data): array
    {
        $items = collect($data['items'] ?? []);

        $subtotalItems = $items
            ->filter(fn ($item) => ! empty($item['is_checked']))
            ->sum(fn ($item) => (float) ($item['harga'] ?? 0));

        $jasaBongkarMuat = (float) ($data['volume_ton'] ?? 0) * (float) ($data['tarif_per_ton'] ?? 0);

        $dpp = $subtotalItems + $jasaBongkarMuat;
        $ppn = round($dpp * 0.11, 2);
        $materai = ! empty($data['pakai_materai']) ? 10000 : 0;
        $jumlah = $dpp + $ppn + $materai;

        return compact('dpp', 'ppn', 'materai', 'jumlah');
    }

    /**
     * Simpan ulang baris checklist invoice_items dari input array.
     */
    private function simpanItems(Invoice $invoice, array $items): void
    {
        foreach (array_values($items) as $urutan => $item) {
            if (blank($item['keterangan'] ?? null)) {
                continue;
            }

            $invoice->items()->create([
                'is_checked' => ! empty($item['is_checked']),
                'keterangan' => $item['keterangan'],
                'catatan' => $item['catatan'] ?? null,
                'harga' => $item['harga'] ?? 0,
                'urutan' => $urutan,
            ]);
        }
    }

    /**
     * Format nomor invoice: {urut}/BMLK/{bulan romawi}/{tahun}
     * cth. 08/BMLK/VIII/2026. Urut reset tiap bulan.
     */
    private function nomorOtomatis(?string $tanggal = null): string
    {
        $date = $tanggal ? Carbon::parse($tanggal) : now();
        $suffix = '/BMLK/' . $this->bulanRomawi((int) $date->format('n')) . '/' . $date->format('Y');

        $urutTerakhir = Invoice::where('nomor_invoice', 'like', '%' . $suffix)
            ->pluck('nomor_invoice')
            ->map(fn ($nomor) => (int) strtok($nomor, '/'))
            ->max() ?? 0;

        return sprintf('%02d', $urutTerakhir + 1) . $suffix;
    }

    private function bulanRomawi(int $bulan): string
    {
        $romawi = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII',
        ];

        return $romawi[$bulan] ?? 'I';
    }
}
