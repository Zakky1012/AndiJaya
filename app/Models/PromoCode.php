<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PromoCode extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'kode',
        'tipe_diskon',
        'diskon',
        'valid',
        'is_used',
    ];

    public function transactions(){
        return $this->hasOne(Transaksi::class);
    }
}
