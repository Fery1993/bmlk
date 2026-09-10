<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClosingEntry extends Model
{
    const UPDATED_AT = null;
    const CREATED_AT = 'dibuat_pada';

    protected $fillable = [
        'fiscal_period_id', 'journal_id', 'laba_rugi_bersih', 'retained_earnings_coa_id',
    ];

    protected function casts(): array
    {
        return ['laba_rugi_bersih' => 'decimal:2'];
    }

    public function fiscalPeriod()
    {
        return $this->belongsTo(FiscalPeriod::class);
    }

    public function journal()
    {
        return $this->belongsTo(Journal::class);
    }

    public function retainedEarningsAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'retained_earnings_coa_id');
    }
}
