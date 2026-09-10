<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    protected $fillable = [
        'nomor_bill', 'vendor_id', 'tanggal', 'jatuh_tempo',
        'beban_coa_id', 'utang_coa_id', 'jumlah', 'status',
        'keterangan', 'dibuat_oleh', 'journal_id',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'jatuh_tempo' => 'date',
            'jumlah' => 'decimal:2',
        ];
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function bebanAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'beban_coa_id');
    }

    public function utangAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'utang_coa_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    public function journal()
    {
        return $this->belongsTo(Journal::class);
    }

    public function payments()
    {
        return $this->hasMany(BillPayment::class);
    }

    public function sisaUtang(): float
    {
        return (float) $this->jumlah - (float) $this->payments->sum('jumlah_bayar');
    }
}
