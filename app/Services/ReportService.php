<?php

namespace App\Services;

use App\Models\AccountOpeningBalance;
use App\Models\ChartOfAccount;
use App\Models\FiscalPeriod;
use App\Models\JournalItem;
use Illuminate\Support\Facades\DB;

/**
 * Kumpulan query siap-pakai untuk 4 laporan akuntansi utama:
 * General Ledger, Neraca Saldo, Laba Rugi, Neraca.
 *
 * Semua laporan menghormati fiscal_periods & account_opening_balances,
 * jadi hasilnya benar untuk periode berjalan tanpa harus scan seluruh
 * histori transaksi sejak awal.
 */
class ReportService
{
    /**
     * 1) General Ledger (Buku Besar) per akun, untuk satu periode.
     */
    public function generalLedger(int $coaId, string $kodePeriode): array
    {
        $period = FiscalPeriod::where('kode_periode', $kodePeriode)->firstOrFail();

        $opening = AccountOpeningBalance::where('fiscal_period_id', $period->id)
            ->where('coa_id', $coaId)
            ->first();

        $saldoAwal = $opening
            ? (float) $opening->saldo_awal_debit - (float) $opening->saldo_awal_kredit
            : 0.0;

        $mutasi = JournalItem::with('journal')
            ->where('coa_id', $coaId)
            ->whereHas('journal', function ($q) use ($period) {
                $q->whereBetween('tanggal', [$period->tanggal_mulai, $period->tanggal_selesai]);
            })
            ->get()
            ->sortBy(fn ($item) => $item->journal->tanggal)
            ->values();

        $saldoBerjalan = $saldoAwal;
        $rows = $mutasi->map(function ($item) use (&$saldoBerjalan) {
            $saldoBerjalan += (float) $item->debit - (float) $item->kredit;

            return [
                'tanggal' => $item->journal->tanggal->toDateString(),
                'nomor_jurnal' => $item->journal->nomor_jurnal,
                'keterangan' => $item->journal->keterangan,
                'debit' => (float) $item->debit,
                'kredit' => (float) $item->kredit,
                'saldo_berjalan' => round($saldoBerjalan, 2),
            ];
        });

        return [
            'coa' => ChartOfAccount::find($coaId),
            'periode' => $kodePeriode,
            'saldo_awal' => round($saldoAwal, 2),
            'mutasi' => $rows,
            'saldo_akhir' => round($saldoBerjalan, 2),
        ];
    }

    /**
     * 2) Neraca Saldo (Trial Balance) untuk satu periode.
     * Validasi: total_debit harus sama dengan total_kredit.
     */
    public function neracaSaldo(string $kodePeriode): array
    {
        $period = FiscalPeriod::where('kode_periode', $kodePeriode)->firstOrFail();

        $rows = DB::table('chart_of_accounts as coa')
            ->leftJoin('account_opening_balances as ob', function ($join) use ($period) {
                $join->on('ob.coa_id', '=', 'coa.id')
                    ->where('ob.fiscal_period_id', $period->id);
            })
            ->leftJoin('journal_items as ji', 'ji.coa_id', '=', 'coa.id')
            ->leftJoin('journals as j', function ($join) use ($period) {
                $join->on('j.id', '=', 'ji.journal_id')
                    ->whereBetween('j.tanggal', [$period->tanggal_mulai, $period->tanggal_selesai]);
            })
            ->groupBy('coa.id', 'coa.kode_akun', 'coa.nama_akun', 'ob.saldo_awal_debit', 'ob.saldo_awal_kredit')
            ->orderBy('coa.kode_akun')
            ->select([
                'coa.kode_akun',
                'coa.nama_akun',
                DB::raw('COALESCE(ob.saldo_awal_debit, 0) + COALESCE(SUM(ji.debit), 0) as total_debit'),
                DB::raw('COALESCE(ob.saldo_awal_kredit, 0) + COALESCE(SUM(ji.kredit), 0) as total_kredit'),
            ])
            ->get();

        return [
            'periode' => $kodePeriode,
            'rows' => $rows,
            'total_debit' => round($rows->sum('total_debit'), 2),
            'total_kredit' => round($rows->sum('total_kredit'), 2),
            'balanced' => round($rows->sum('total_debit'), 2) === round($rows->sum('total_kredit'), 2),
        ];
    }

    /**
     * 3) Laporan Laba Rugi (Income Statement) untuk satu periode.
     */
    public function labaRugi(string $kodePeriode): array
    {
        $period = FiscalPeriod::where('kode_periode', $kodePeriode)->firstOrFail();

        $rows = DB::table('journal_items as ji')
            ->join('journals as j', 'j.id', '=', 'ji.journal_id')
            ->join('chart_of_accounts as coa', 'coa.id', '=', 'ji.coa_id')
            ->whereBetween('j.tanggal', [$period->tanggal_mulai, $period->tanggal_selesai])
            ->whereIn('coa.kelompok', ['pendapatan', 'beban'])
            ->where('j.is_closing', false)
            ->groupBy('coa.kelompok', 'coa.id', 'coa.nama_akun', 'coa.posisi_normal')
            ->select([
                'coa.kelompok',
                'coa.nama_akun',
                DB::raw("SUM(CASE WHEN coa.posisi_normal = 'kredit'
                            THEN ji.kredit - ji.debit
                            ELSE ji.debit - ji.kredit END) as nilai"),
            ])
            ->orderBy('coa.kelompok')
            ->orderBy('coa.nama_akun')
            ->get();

        $pendapatan = $rows->where('kelompok', 'pendapatan');
        $beban = $rows->where('kelompok', 'beban');

        return [
            'periode' => $kodePeriode,
            'pendapatan' => $pendapatan->values(),
            'beban' => $beban->values(),
            'total_pendapatan' => round($pendapatan->sum('nilai'), 2),
            'total_beban' => round($beban->sum('nilai'), 2),
            'laba_bersih' => round($pendapatan->sum('nilai') - $beban->sum('nilai'), 2),
        ];
    }

    /**
     * 4) Neraca (Balance Sheet) untuk satu periode.
     * Validasi: total aset = total kewajiban + total ekuitas.
     */
    public function neraca(string $kodePeriode): array
    {
        $period = FiscalPeriod::where('kode_periode', $kodePeriode)->firstOrFail();

        $rows = DB::table('chart_of_accounts as coa')
            ->leftJoin('account_opening_balances as ob', function ($join) use ($period) {
                $join->on('ob.coa_id', '=', 'coa.id')
                    ->where('ob.fiscal_period_id', $period->id);
            })
            ->leftJoin('journal_items as ji', 'ji.coa_id', '=', 'coa.id')
            ->leftJoin('journals as j', function ($join) use ($period) {
                $join->on('j.id', '=', 'ji.journal_id')
                    ->whereBetween('j.tanggal', [$period->tanggal_mulai, $period->tanggal_selesai]);
            })
            ->whereIn('coa.kelompok', ['aset', 'kewajiban', 'ekuitas'])
            ->groupBy('coa.kelompok', 'coa.id', 'coa.nama_akun', 'coa.posisi_normal',
                'ob.saldo_awal_debit', 'ob.saldo_awal_kredit')
            ->select([
                'coa.kelompok',
                'coa.nama_akun',
                DB::raw("COALESCE(ob.saldo_awal_debit, 0) - COALESCE(ob.saldo_awal_kredit, 0)
                    + SUM(CASE WHEN coa.posisi_normal = 'debit'
                            THEN COALESCE(ji.debit, 0) - COALESCE(ji.kredit, 0)
                            ELSE COALESCE(ji.kredit, 0) - COALESCE(ji.debit, 0) END) as saldo_akhir"),
            ])
            ->orderBy('coa.kelompok')
            ->orderBy('coa.nama_akun')
            ->get();

        $aset = $rows->where('kelompok', 'aset');
        $kewajiban = $rows->where('kelompok', 'kewajiban');
        $ekuitas = $rows->where('kelompok', 'ekuitas');

        return [
            'periode' => $kodePeriode,
            'aset' => $aset->values(),
            'kewajiban' => $kewajiban->values(),
            'ekuitas' => $ekuitas->values(),
            'total_aset' => round($aset->sum('saldo_akhir'), 2),
            'total_kewajiban' => round($kewajiban->sum('saldo_akhir'), 2),
            'total_ekuitas' => round($ekuitas->sum('saldo_akhir'), 2),
            'balanced' => round($aset->sum('saldo_akhir'), 2)
                === round($kewajiban->sum('saldo_akhir') + $ekuitas->sum('saldo_akhir'), 2),
        ];
    }

    /**
     * 5) Proses Tutup Buku: buat jurnal penutup, catat di closing_entries,
     * generate saldo awal periode berikutnya, kunci periode ini.
     *
     * Catatan: ini kerangka logikanya. Sesuaikan dengan kebutuhan
     * transaksi database (DB::transaction) di controller/command asli.
     */
    public function tutupBuku(string $kodePeriode, int $retainedEarningsCoaId, int $userId): void
    {
        DB::transaction(function () use ($kodePeriode, $retainedEarningsCoaId, $userId) {
            $period = FiscalPeriod::where('kode_periode', $kodePeriode)->firstOrFail();

            if ($period->isClosed()) {
                throw new \RuntimeException("Periode {$kodePeriode} sudah ditutup sebelumnya.");
            }

            $labaRugi = $this->labaRugi($kodePeriode);
            $labaBersih = $labaRugi['laba_bersih'];

            // 1. Buat jurnal penutup
            $journal = \App\Models\Journal::create([
                'nomor_jurnal' => 'CLS-'.$kodePeriode,
                'tanggal' => $period->tanggal_selesai,
                'keterangan' => "Jurnal penutup periode {$kodePeriode}",
                'sumber_tipe' => 'closing',
                'is_closing' => true,
                'dibuat_oleh' => $userId,
            ]);

            // 2. Nolkan setiap akun pendapatan & beban, alirkan ke Laba Ditahan
            foreach ($labaRugi['pendapatan'] as $row) {
                $coa = ChartOfAccount::where('nama_akun', $row->nama_akun)
                    ->where('kelompok', 'pendapatan')->first();
                $journal->items()->create([
                    'coa_id' => $coa->id,
                    'debit' => $row->nilai > 0 ? $row->nilai : 0,
                    'kredit' => $row->nilai < 0 ? abs($row->nilai) : 0,
                ]);
            }
            foreach ($labaRugi['beban'] as $row) {
                $coa = ChartOfAccount::where('nama_akun', $row->nama_akun)
                    ->where('kelompok', 'beban')->first();
                $journal->items()->create([
                    'coa_id' => $coa->id,
                    'kredit' => $row->nilai > 0 ? $row->nilai : 0,
                    'debit' => $row->nilai < 0 ? abs($row->nilai) : 0,
                ]);
            }
            $journal->items()->create([
                'coa_id' => $retainedEarningsCoaId,
                'debit' => $labaBersih < 0 ? abs($labaBersih) : 0,
                'kredit' => $labaBersih > 0 ? $labaBersih : 0,
            ]);

            // 3. Catat di closing_entries
            \App\Models\ClosingEntry::create([
                'fiscal_period_id' => $period->id,
                'journal_id' => $journal->id,
                'laba_rugi_bersih' => $labaBersih,
                'retained_earnings_coa_id' => $retainedEarningsCoaId,
            ]);

            // 4. Generate saldo awal periode berikutnya dari hasil Neraca periode ini
            $neraca = $this->neraca($kodePeriode);
            $nextPeriod = FiscalPeriod::where('tanggal_mulai', '>', $period->tanggal_selesai)
                ->orderBy('tanggal_mulai')
                ->first();

            if ($nextPeriod) {
                foreach (array_merge(
                    $neraca['aset']->all(), $neraca['kewajiban']->all(), $neraca['ekuitas']->all()
                ) as $row) {
                    $coa = ChartOfAccount::where('nama_akun', $row->nama_akun)->first();
                    AccountOpeningBalance::updateOrCreate(
                        ['fiscal_period_id' => $nextPeriod->id, 'coa_id' => $coa->id],
                        [
                            'saldo_awal_debit' => $row->saldo_akhir > 0 ? $row->saldo_akhir : 0,
                            'saldo_awal_kredit' => $row->saldo_akhir < 0 ? abs($row->saldo_akhir) : 0,
                        ]
                    );
                }
            }

            // 5. Kunci periode ini
            $period->update([
                'status' => 'closed',
                'ditutup_oleh' => $userId,
                'ditutup_pada' => now(),
            ]);
        });
    }
}
