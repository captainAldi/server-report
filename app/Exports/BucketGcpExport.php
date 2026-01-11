<?php

namespace App\Exports;

use App\Models\BucketGcp;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BucketGcpExport implements FromCollection, WithHeadings, WithMapping
{
    protected $cari_nama;
    protected $cari_lokasi;
    protected $cari_status;

    public function __construct($cari_nama = '', $cari_lokasi = '', $cari_status = '')
    {
        $this->cari_nama = $cari_nama;
        $this->cari_lokasi = $cari_lokasi;
        $this->cari_status = $cari_status;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $data = BucketGcp::with('lokasi_gcp');

        if($this->cari_nama != '') {
            $data = $data->where('nama','LIKE','%'.$this->cari_nama.'%');
        }

        if($this->cari_lokasi != '') {
            $data = $data->where('lokasi_gcp_id', $this->cari_lokasi);
        }

        if($this->cari_status != '') {
            if($this->cari_status == 'ACTIVE') {
                $data = $data->whereNull('deleted_at');
            } elseif($this->cari_status == 'DELETED') {
                $data = $data->whereNotNull('deleted_at');
            }
        }

        return $data->get();
    }

    public function headings(): array
    {
        return [
            'Nama Bucket',
            'Lokasi Project',
            'Lokasi Bucket',
            'Tipe Storage',
            'Tanggal Dibuat',
            'Status'
        ];
    }

    public function map($bucket): array
    {
        return [
            $bucket->nama,
            $bucket->lokasi_gcp ? $bucket->lokasi_gcp->id_project : '-',
            $bucket->lokasi,
            $bucket->tipe_storage,
            $bucket->dibuat ? $bucket->dibuat->format('d M Y H:i') : '-',
            $bucket->trashed() ? 'DELETED' : 'ACTIVE'
        ];
    }
}