<?php

namespace App\Exports;

use App\Models\ServerProxmox;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;

class ServerProxmoxExport implements ShouldAutoSize, FromView
{
    use Exportable;

    protected $lokasi_proxmox_id;

    function __construct($lokasi_proxmox_id) {
            $this->lokasi_proxmox_id = $lokasi_proxmox_id;
    }

    public function view(): View
    {
        // Data
        $lokasi_proxmox_id = $this->lokasi_proxmox_id;

        $data_server_proxmox = ServerProxmox::query();

        // Kondisi
        if ($lokasi_proxmox_id != '') {
            $data_server_proxmox = $data_server_proxmox->where('lokasi_proxmox_id', $lokasi_proxmox_id);
        }


        // Get Data
        $data_server_proxmox = $data_server_proxmox->with([
                                'lokasi_proxmox' => function ($q) {
                                    $q->orderBy('nama_node', 'ASC');
                                }
                            ])
                            ->orderBy('vm_id', 'asc')
                            ->get();

        return view('exports.server-proxmox', compact(
            'data_server_proxmox',
        ));
    }


}
