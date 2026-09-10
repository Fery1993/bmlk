<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    protected $table = 'karyawan';

    protected $fillable = ['nik', 'nama', 'jabatan', 'telepon', 'rekening_bank', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function operationalFees()
    {
        return $this->hasMany(OperationalFee::class);
    }
}
