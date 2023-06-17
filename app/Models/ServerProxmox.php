<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServerProxmox extends Model
{
    use HasFactory;

    protected $table = "server_proxmox";
    protected $guarded = [];

    // protected $casts = [
    //     'dibuat' => 'datetime',
    // ];

    public function lokasi_proxmox()
    {
        return $this->belongsTo(LokasiProxmox::class, 'lokasi_proxmox_id', 'id');
    }

}
