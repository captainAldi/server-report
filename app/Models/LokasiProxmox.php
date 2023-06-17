<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LokasiProxmox extends Model
{
    use HasFactory;

    protected $table = "lokasi_proxmox";
    protected $guarded = [];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    protected $hidden = [
        'token',
    ];

    public function server_proxmox()
    {
        return $this->hasMany(ServerProxmox::class, 'lokasi_proxmox_id', 'id');
    }
}
