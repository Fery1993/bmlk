<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    protected $fillable = ['kode', 'nama', 'telepon', 'email', 'alamat', 'npwp'];

    public function bills()
    {
        return $this->hasMany(Bill::class);
    }
}
