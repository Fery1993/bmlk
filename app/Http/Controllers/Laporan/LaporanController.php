<?php

namespace App\Http\Controllers\Laporan;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use App\Models\FiscalPeriod;
use App\Models\Invoice;
use App\Services\ReportService;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function __construct(private ReportService $reports)
    {
    }

    /**
     * Ambil kode_periode dari query string, atau fallback ke periode yang sedang open.
     */
    private function periodeAktif(Request $request): ?string
    {
        if ($request->filled('periode')) {
            return $request->get('periode');
        }

        return FiscalPeriod::where('status', 'open')->latest()->value('kode_periode');
    }

    public function piutang(Request $request)
    {
        $query = Invoice::with('customer')
            ->whereIn('status', ['belum_lunas', 'sebagian'])
            ->orderBy('jatuh_tempo');

        if ($customerId = $request->get('customer_id')) {
            $query->where('customer_id', $customerId);
        }

        $invoices = $query->get();
        $totalPiutang = $invoices->sum(fn ($inv) => $inv->sisaTagihan());

        return view('laporan.piutang', compact('invoices', 'totalPiutang'));
    }

    public function hutang(Request $request)
    {
        $query = \App\Models\Bill::with('vendor')
            ->whereIn('status', ['belum_lunas', 'sebagian'])
            ->orderBy('jatuh_tempo');

        if ($vendorId = $request->get('vendor_id')) {
            $query->where('vendor_id', $vendorId);
        }

        $bills = $query->get();
        $totalHutang = $bills->sum(fn ($bill) => $bill->sisaUtang());

        return view('laporan.hutang', compact('bills', 'totalHutang'));
    }

    public function generalLedger(Request $request)
    {
        $coaList = ChartOfAccount::orderBy('kode_akun')->get();
        $periode = $this->periodeAktif($request);
        $coaId = $request->get('coa_id');

        $data = null;
        if ($periode && $coaId) {
            $data = $this->reports->generalLedger((int) $coaId, $periode);
        }

        return view('laporan.gl', compact('coaList', 'periode', 'coaId', 'data'));
    }

    public function neracaSaldo(Request $request)
    {
        $periode = $this->periodeAktif($request);
        $data = $periode ? $this->reports->neracaSaldo($periode) : null;

        return view('laporan.neraca-saldo', compact('periode', 'data'));
    }

    public function neraca(Request $request)
    {
        $periode = $this->periodeAktif($request);
        $data = $periode ? $this->reports->neraca($periode) : null;

        return view('laporan.neraca', compact('periode', 'data'));
    }

    public function labaRugi(Request $request)
    {
        $periode = $this->periodeAktif($request);
        $data = $periode ? $this->reports->labaRugi($periode) : null;

        return view('laporan.laba-rugi', compact('periode', 'data'));
    }
}
