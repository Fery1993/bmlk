<?php

namespace Database\Seeders;

use App\Models\BankAccount;
use App\Models\ChartOfAccount;
use App\Models\FiscalPeriod;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class FinanceSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Admin awal
        $admin = User::create([
            'nama' => 'Administrator',
            'email' => 'admin@perusahaan.co.id',
            'password' => Hash::make('ganti-password-ini'),
            'role' => 'admin',
        ]);

        // 2. Bagan Akun dasar
        $coa = [
            ['1100', 'Kas & Bank', 'aset', 'debit'],
            ['1200', 'Piutang Usaha', 'aset', 'debit'],
            ['2100', 'Utang Usaha', 'kewajiban', 'kredit'],
            ['3100', 'Modal Disetor', 'ekuitas', 'kredit'],
            ['3200', 'Laba Ditahan', 'ekuitas', 'kredit'],
            ['4100', 'Pendapatan Jasa', 'pendapatan', 'kredit'],
            ['5100', 'Beban Operasional', 'beban', 'debit'],
            ['5200', 'Beban Fee & Komisi', 'beban', 'debit'],
        ];

        foreach ($coa as [$kode, $nama, $kelompok, $posisi]) {
            ChartOfAccount::create([
                'kode_akun' => $kode,
                'nama_akun' => $nama,
                'kelompok' => $kelompok,
                'posisi_normal' => $posisi,
            ]);
        }

        // 3. Rekening kas & bank
        $kasCoaId = ChartOfAccount::where('kode_akun', '1100')->value('id');

        BankAccount::create([
            'coa_id' => $kasCoaId,
            'nama_bank' => 'Kas Tunai',
            'atas_nama' => 'Kas Kantor',
        ]);

        BankAccount::create([
            'coa_id' => $kasCoaId,
            'nama_bank' => 'BCA',
            'nomor_rekening' => '1234567890',
            'atas_nama' => 'PT Contoh Jaya',
        ]);

        // 4. Periode akuntansi awal
        FiscalPeriod::create([
            'kode_periode' => '2026-08',
            'tanggal_mulai' => '2026-08-01',
            'tanggal_selesai' => '2026-08-31',
            'status' => 'open',
        ]);
    }
}
