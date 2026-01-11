<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BucketGcp extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "bucket_gcp";
    protected $guarded = [];

    protected $casts = [
        'dibuat' => 'datetime',
    ];

    public function lokasi_gcp()
    {
        return $this->belongsTo(LokasiGcp::class, 'lokasi_gcp_id', 'id');
    }
}