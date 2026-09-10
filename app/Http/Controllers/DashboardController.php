<?php

namespace App\Http\Controllers;

use App\Models\FiscalPeriod;
use App\Models\Invoice;
use App\Models\Bill;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $periodeAktif = FiscalPeriod::where('status', 'open')->latest()->first();

        // Total piutang belum lunas
        $totalPiutang = Invoice::whereIn('status', ['belum_lunas', 'sebagian'])->sum('jumlah');

        // Total hutang belum lunas
        $totalHutang = Bill::whereIn('status', ['belum_lunas', 'sebagian'])->sum('jumlah');

        // Invoice melewati jatuh tempo
        $piutangJatuhTempo = Invoice::whereIn('status', ['belum_lunas', 'sebagian'])
            ->where('jatuh_tempo', '<', today())->count();

        // Bill mendekati/melewati jatuh tempo
        $hutangJatuhTempo = Bill::whereIn('status', ['belum_lunas', 'sebagian'])
            ->where('jatuh_tempo', '<=', today()->addDays(7))->count();

        // Pendapatan bulan ini (dari journal_items akun pendapatan)
        // Simplified: jumlah invoice bulan ini
        $pendapatanBulanIni = Invoice::whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->whereNotIn('status', ['batal'])
            ->sum('jumlah');

        $jumlahInvoice = Invoice::whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)->count();

        $invoiceBelumLunas = Invoice::whereIn('status', ['belum_lunas', 'sebagian'])->count();

        // Invoice terbaru (10)
        $invoiceTerbaru = Invoice::with('customer')
            ->latest()->take(10)->get();

        // Piutang mendesak (jatuh tempo ≤ 7 hari atau sudah lewat)
        $piutangMendesak = Invoice::with('customer')
            ->whereIn('status', ['belum_lunas', 'sebagian'])
            ->where('jatuh_tempo', '<=', today()->addDays(7))
            ->orderBy('jatuh_tempo')
            ->take(5)->get();

        return view('dashboard.index', compact(
            'periodeAktif',
            'totalPiutang',
            'totalHutang',
            'piutangJatuhTempo',
            'hutangJatuhTempo',
            'pendapatanBulanIni',
            'jumlahInvoice',
            'invoiceBelumLunas',
            'invoiceTerbaru',
            'piutangMendesak',
        ));
    }
}
