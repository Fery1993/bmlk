<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'nomor_invoice', 'customer_id', 'tanggal', 'jatuh_tempo',
        'pendapatan_coa_id', 'piutang_coa_id', 'jumlah', 'status',
        'keterangan', 'dibuat_oleh', 'journal_id',
        // Detail pekerjaan bongkar muat (lihat migration add_detail_columns_to_invoices_table)
        'volume_ton', 'tarif_per_ton', 'dpp', 'ppn', 'materai', 'pakai_materai',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'jatuh_tempo' => 'date',
            'jumlah' => 'decimal:2',
            'volume_ton' => 'decimal:3',
            'tarif_per_ton' => 'decimal:2',
            'dpp' => 'decimal:2',
            'ppn' => 'decimal:2',
            'materai' => 'decimal:2',
            'pakai_materai' => 'boolean',
        ];
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function pendapatanAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'pendapatan_coa_id');
    }

    public function piutangAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'piutang_coa_id');
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
        return $this->hasMany(InvoicePayment::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class)->orderBy('urutan');
    }

    /**
     * Nilai jasa bongkar muat = volume (ton) x tarif per ton.
     * Ditampilkan sebagai baris tersendiri di invoice (bukan bagian
     * dari checklist items), persis seperti contoh cetakan invoice.
     */
    public function jasaBongkarMuat(): float
    {
        return (float) $this->volume_ton * (float) $this->tarif_per_ton;
    }

    public function sisaTagihan(): float
    {
        return (float) $this->jumlah - (float) $this->payments->sum('jumlah_bayar');
    }
}
