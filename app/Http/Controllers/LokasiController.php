<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

use App\Models\LokasiGcp;
use App\Models\LokasiProxmox;


use Google\Auth\ApplicationDefaultCredentials;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;

class LokasiController extends Controller
{
    // --- GCP ---

    public function gcp_index()
    {
        $data_lokasi = LokasiGcp::all();

        return view('lokasi.gcp', compact('data_lokasi'));
    }

    public function gcp_sync()
    {
        // define the scopes for your API call
        $scopes = [
            'https://www.googleapis.com/auth/cloud-platform.read-only',
            'https://www.googleapis.com/auth/cloudplatformprojects.readonly'
        ];

        // create middleware
        $middleware = ApplicationDefaultCredentials::getMiddleware($scopes);
        $stack = HandlerStack::create();
        $stack->push($middleware);

        // create the HTTP client
        $client = new Client([
            'handler' => $stack,
            'base_uri' => 'https://cloudresourcemanager.googleapis.com',
            'auth' => 'google_auth'  // authorize all requests
        ]);

        try {
            // make the request
            $endpoint = 'v1/projects';
            $response = $client->get($endpoint);
            $responseJSONencoded = json_decode($response->getBody());

        } catch (\Exception $e) {
            Log::error('ERROR - GCP - Get Project', (array)$e->getMessage());
        }

        
        // Proses Simpan ke DB
        DB::beginTransaction();

        try {
            
            foreach ($responseJSONencoded->projects as $project) {


                $data = LokasiGcp::updateOrCreate(
                    // Cek Variable
                    [
                        'id_project' => $project->projectId
                    ],
                    // Simpan Sisa/Semua
                    [
                        'nama_project' => $project->name,
                        'dibuat'       => date('Y-m-d H:i:s', strtotime($project->createTime))
                    ]
                );

                // Jika Semua Normal, Commit ke DB
                DB::commit(); 

            };
            
        } catch (\Exception $e) {
            // Jika ada yang Gagal, Rollback DB
            DB::rollBack();

            Log::error('ERROR - GCP - Save Get Project', (array)$e->getMessage());
        }

        return back()->with('pesan', 'Berhasil Sync Data !');
    }

    // --- Proxmox ---

    public function proxmox_index()
    {
        $data_lokasi = LokasiProxmox::all();
        

        return view('lokasi.proxmox', compact('data_lokasi'));
    }
    
    public function proxmox_proses_simpan(Request $request)
    {
         // Aturan Validasi
        $rule_validasi = [
            'nama_node'         => 'required|unique:lokasi_proxmox,nama_node',
            'ip_node'           => 'required|unique:lokasi_proxmox,ip_node',
            'port_node'         => 'required',
            'token'             => 'required'
        ];

        // Custom Message
        $pesan_validasi = [
            'nama_node.required'  => 'Node Harus di Isi !',
            'ip_node.required'    => 'IP Node Harus di Isi !',
            'port_node.required'  => 'Port Node Harus di Isi !',
            'token.required'      => 'Token Harus di Isi !',

        ];

        // Lakukan Validasi
        $request->validate($rule_validasi, $pesan_validasi);

        // Mapping All Request 
        $data_to_save               = new LokasiProxmox();
        $data_to_save->nama_node    = $request->nama_node;
        $data_to_save->ip_node      = $request->ip_node;
        $data_to_save->port_node    = $request->port_node;
        $data_to_save->token        = Crypt::encryptString($request->token);

        // Save to DB
        $data_to_save->save();


        // Kembali dengan Flash Session Data
        return back()->with('pesan', 'Data Telah Disimpan !');
    }

    public function proxmox_proses_ubah(Request $request, $id)
    {
         // Aturan Validasi
        $rule_validasi = [
            'nama_node_update'         => 'required|unique:lokasi_proxmox,nama_node,' . $id,
            'ip_node_update'           => 'required|unique:lokasi_proxmox,nama_node,' . $id,
            'port_node_update'         => 'required',
            'token_update'             => $request->filled('token_update') ? 'required' : ''
        ];

        // Custom Message
        $pesan_validasi = [
            'nama_node_update.required'  => 'Node Harus di Isi !',
            'nama_node_update.required'  => 'Node Harus Unik !',

            'ip_node_update.required'    => 'IP Node Harus di Isi !',
            'ip_node_update.required'    => 'IP Node Harus Unik !',

            'port_node_update.required'  => 'Port Node Harus di Isi !',
            'token_update.required'      => 'Token Harus di Isi !',

        ];

        // Lakukan Validasi
        $request->validate($rule_validasi, $pesan_validasi);

        // Mapping All Request 
        $data_to_save               = LokasiProxmox::findOrFail($id);

        $data_to_save->nama_node    = $request->nama_node_update;
        $data_to_save->ip_node      = $request->ip_node_update;
        $data_to_save->port_node    = $request->port_node_update;

        if($request->filled('token_update'))
        {
            $data_to_save->token    = Crypt::encryptString($request->token_update) ;
        }
        

        // Save to DB
        $data_to_save->save();


        // Kembali dengan Flash Session Data
        return back()->with('pesan', 'Data Telah Disimpan !');
    }

}
