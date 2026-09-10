<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FiscalPeriod extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'kode_periode', 'tanggal_mulai', 'tanggal_selesai',
        'status', 'ditutup_oleh', 'ditutup_pada',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'ditutup_pada' => 'datetime',
        ];
    }

    public function openingBalances()
    {
        return $this->hasMany(AccountOpeningBalance::class);
    }

    public function closingEntry()
    {
        return $this->hasOne(ClosingEntry::class);
    }

    public function closer()
    {
        return $this->belongsTo(User::class, 'ditutup_oleh');
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }
}
