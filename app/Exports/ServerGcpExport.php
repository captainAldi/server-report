<?php

namespace App\Exports;

use App\Models\ServerGcp;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;

class ServerGcpExport implements ShouldAutoSize, FromView
{
    use Exportable;

    protected $date;

    function __construct($nama, $lokasi, $status) {
            $this->nama = $nama;
            $this->lokasi = $lokasi;
            $this->status = $status;
    }

    public function view(): View 
    {
        // Data
        $nama = $this->nama;
        $lokasi = $this->lokasi;
        $status = $this->status;

        $data_server_gcp = ServerGcp::query();

        // Kondisi
        if ($nama != '') {
            $data_server_gcp = $data_server_gcp->where('nama', 'LIKE', '%' . $nama . '%');
        }

        if ($lokasi != '') {
            $data_server_gcp = $data_server_gcp->where('lokasi_gcp_id', $lokasi);
        }

        if ($status != '') {
            $data_server_gcp = $data_server_gcp->where('status', 'LIKE', '%' . $status . '%');
        }

        
        // Get Data
        $data_server_gcp = $data_server_gcp->with([
                                'lokasi_gcp' => function ($q) {
                                    $q->orderBy('nama_project', 'ASC');
                                }
                            ])
                            ->get();

        return view('exports.server-gcp', compact(
            'data_server_gcp',
        ));
    }


}
