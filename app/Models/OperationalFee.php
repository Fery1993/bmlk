<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OperationalFee extends Model
{
    protected $fillable = [
        'nomor_fee', 'karyawan_id', 'beban_coa_id', 'bank_account_id',
        'user_id', 'tanggal', 'jumlah_fee', 'keterangan', 'journal_id',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'jumlah_fee' => 'decimal:2',
        ];
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function bebanAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'beban_coa_id');
    }

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function journal()
    {
        return $this->belongsTo(Journal::class);
    }
}
