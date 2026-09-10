<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountOpeningBalance extends Model
{
    public $timestamps = false;

    protected $fillable = ['fiscal_period_id', 'coa_id', 'saldo_awal_debit', 'saldo_awal_kredit'];

    protected function casts(): array
    {
        return [
            'saldo_awal_debit' => 'decimal:2',
            'saldo_awal_kredit' => 'decimal:2',
        ];
    }

    public function fiscalPeriod()
    {
        return $this->belongsTo(FiscalPeriod::class);
    }

    public function chartOfAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'coa_id');
    }
}
