<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CloudSqlGcp extends Model
{
    use HasFactory;

    protected $table = "csql_gcp";
    protected $guarded = [];

    protected $casts = [
        'dibuat' => 'datetime',
    ];

    public function lokasi_gcp()
    {
        return $this->belongsTo(LokasiGcp::class, 'lokasi_gcp_id', 'id');
    }
}
