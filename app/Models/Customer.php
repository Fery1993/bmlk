<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = ['kode', 'nama', 'telepon', 'email', 'alamat', 'npwp'];

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}
