<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChartOfAccount extends Model
{
    protected $table = 'chart_of_accounts';

    protected $fillable = ['kode_akun', 'nama_akun', 'kelompok', 'posisi_normal', 'parent_id', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function parent()
    {
        return $this->belongsTo(ChartOfAccount::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(ChartOfAccount::class, 'parent_id');
    }

    public function journalItems()
    {
        return $this->hasMany(JournalItem::class, 'coa_id');
    }

    public function openingBalances()
    {
        return $this->hasMany(AccountOpeningBalance::class, 'coa_id');
    }
}
