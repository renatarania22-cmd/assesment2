<?php

namespace Modules\Setoran\Entities;

use Illuminate\Database\Eloquent\Model;

class Setoran extends Model
{
    protected $table = 'setorans';

    protected $fillable = [
        'user_id',
        'jenis_sampah',
        'berat',
        'harga',
        'total_harga',
        'status',
        'tanggal_setoran',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}