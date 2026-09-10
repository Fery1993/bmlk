<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    protected $fillable = [
        'invoice_id', 'is_checked', 'keterangan', 'catatan', 'harga', 'urutan',
    ];

    protected function casts(): array
    {
        return [
            'is_checked' => 'boolean',
            'harga' => 'decimal:2',
            'urutan' => 'integer',
        ];
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
