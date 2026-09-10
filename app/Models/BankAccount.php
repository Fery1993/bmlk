<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    protected $fillable = ['coa_id', 'nama_bank', 'nomor_rekening', 'atas_nama', 'saldo_berjalan', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'saldo_berjalan' => 'decimal:2'];
    }

    public function chartOfAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'coa_id');
    }

    public function operationalFees()
    {
        return $this->hasMany(OperationalFee::class);
    }

    public function invoicePayments()
    {
        return $this->hasMany(InvoicePayment::class);
    }

    public function billPayments()
    {
        return $this->hasMany(BillPayment::class);
    }
}
