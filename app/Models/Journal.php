<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Journal extends Model
{
    protected $fillable = [
        'nomor_jurnal', 'tanggal', 'keterangan', 'sumber_tipe',
        'sumber_id', 'is_closing', 'dibuat_oleh',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'is_closing' => 'boolean',
        ];
    }

    public function items()
    {
        return $this->hasMany(JournalItem::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    /** Total debit harus selalu sama dengan total kredit (double-entry) */
    public function isBalanced(): bool
    {
        return round($this->items->sum('debit'), 2) === round($this->items->sum('kredit'), 2);
    }
}
