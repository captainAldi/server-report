<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LokasiGcp extends Model
{
    use HasFactory;

    protected $table = "lokasi_gcp";
    protected $guarded = [];

    protected $casts = [
        'dibuat' => 'datetime',
    ];

    public function server_gcp()
    {
        return $this->hasMany(ServerGcp::class, 'lokasi_gcp_id', 'id');
    }

    public function csql_gcp()
    {
        return $this->hasMany(CloudSqlGcp::class, 'lokasi_gcp_id', 'id');
    }
}
