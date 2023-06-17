<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;

use App\Models\LokasiGcp;
use App\Models\ServerGcp;
use App\Models\CloudSqlGcp;

use App\Models\LokasiProxmox;
use App\Models\ServerProxmox;

use Google\Auth\ApplicationDefaultCredentials;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;

use Illuminate\Support\Collection;

class ReportController extends Controller
{

    // --- GCP CE ---
    public function gcp_get_all_ce($id_project)
    {

        // define the scopes for your API call
        $scopes = ['https://www.googleapis.com/auth/compute.readonly'];

        // create middleware
        $middleware = ApplicationDefaultCredentials::getMiddleware($scopes);
        $stack = HandlerStack::create();
        $stack->push($middleware);

        // create the HTTP client
        $client = new Client([
            'handler' => $stack,
            'base_uri' => 'https://compute.googleapis.com',
            'auth' => 'google_auth',  // authorize all requests
            // 'http_errors' => false
        ]);

        // make the request
        $endpoint = 'compute/v1/projects/' . $id_project . '/aggregated/instances';
        $response = $client->get($endpoint);
        $responseJSONencoded = json_decode($response->getBody());


        $mappedData = [];

        foreach ($responseJSONencoded->items as $zone) {

            if (isset($zone->instances)) {
                foreach ($zone->instances as $instance) {
                    array_push($mappedData, $instance);
                }
            };

        };

        return $mappedData;

    }

    public function gcp_get_ce_type($link_ce_type)
    {
        // define the scopes for your API call
        $scopes = [
            'https://www.googleapis.com/auth/compute.readonly',
        ];

        // create middleware
        $middleware = ApplicationDefaultCredentials::getMiddleware($scopes);
        $stack = HandlerStack::create();
        $stack->push($middleware);

        // create the HTTP client
        $client = new Client([
            'handler' => $stack,
            // 'base_uri' => 'https://compute.googleapis.com',
            'auth' => 'google_auth',  // authorize all requests
            // 'http_errors' => false
        ]);

        // make the request
        $response = $client->get($link_ce_type);
        $responseJSONencoded = json_decode($response->getBody());

        return $responseJSONencoded;
    }
    
    public function gcp_ce_sync()
    {

        $data_project = LokasiGcp::all();
        $ce_mapped_data = [];

        // Sync CE
        foreach ($data_project as $key_project => $value_project) {
            
            try {
                $ce_data = $this->gcp_get_all_ce($value_project->id_project);
                $ce_mapped_data[$value_project->id_project] = $ce_data;
            } catch (\Throwable $th) {
                continue;
            }

        }

        // dd($ce_mapped_data);

        // Save CE to DB

        // Proses Simpan ke DB
        DB::beginTransaction();

        try {
            
            foreach ($ce_mapped_data as $key_ce => $value_ce) {
                

                $get_id_project = LokasiGcp::where('id_project', $key_ce)->first();

                foreach ($value_ce as $key_sub_ce => $value_sub_ce) {
                    
                    // Prepare Data

                    $machine_type = $this->gcp_get_ce_type($value_sub_ce->machineType);


                    // Upsert

                    $data = ServerGcp::updateOrCreate(
                        // Cek Variable
                        [
                            'ce_id' => $value_sub_ce->id
                        ],
                        // Simpan Sisa/Semua
                        [
                            'lokasi_gcp_id'     => $get_id_project->id,
                            'nama'              => $value_sub_ce->name,
                            'tipe'              => $machine_type->name,
                            'priv_ip'           => $value_sub_ce->networkInterfaces[0]->networkIP,
                            'pub_ip'            => $value_sub_ce->networkInterfaces[0]->accessConfigs[0]->natIP ?? 'Tidak Ada',
                            'v_cpu'             => $machine_type->guestCpus,
                            'ram'               => $machine_type->memoryMb / 1024,
                            'disk'              => $value_sub_ce->disks[0]->diskSizeGb,
                            'status'            => $value_sub_ce->status,
                            'dibuat'            => $value_sub_ce->creationTimestamp
                        ]
                    );

                }

            };

            // Jika Semua Normal, Commit ke DB
            DB::commit(); 
            
        } catch (\Exception $e) {
            // Jika ada yang Gagal, Rollback DB
            DB::rollBack();

            Log::error('ERROR - GCP - Save Get CE', (array)$e->getMessage());
        }

        // Return
        return back()->with('pesan', 'Berhasil Sync Data !');


    }

    // --- GCP Cloud SQL ---
    public function gcp_get_all_csql($id_project)
    {
        // define the scopes for your API call
        $scopes = [
            'https://www.googleapis.com/auth/cloud-platform.read-only',
            'https://www.googleapis.com/auth/sqlservice.admin'
        ];

         // create middleware
        $middleware = ApplicationDefaultCredentials::getMiddleware($scopes);
        $stack = HandlerStack::create();
        $stack->push($middleware);

        // create the HTTP client
        $client = new Client([
            'handler' => $stack,
            'base_uri' => 'https://sqladmin.googleapis.com',
            'auth' => 'google_auth'  // authorize all requests
        ]);

        // make the request
        $endpoint = 'v1/projects/' . $id_project . '/instances' ;
        $response = $client->get($endpoint);
        $responseJSONencoded = json_decode($response->getBody());

        return $responseJSONencoded;
    }

    public function gcp_csql_sync()
    {
        $data_project = LokasiGcp::all();
        $csql_mapped_data = [];

        // Sync CE
        foreach ($data_project as $key_project => $value_project) {
            
            try {
                $csql_data = $this->gcp_get_all_csql($value_project->id_project);
                $csql_mapped_data[$value_project->id_project] = $csql_data;
            } catch (\Exception $e) {
                continue;
            }

        }

        // dd($csql_mapped_data);

         // Save Cloud SQL to DB

        // Proses Simpan ke DB
        DB::beginTransaction();

        try {
            
            foreach ($csql_mapped_data as $key_csql => $value_csql) {
                
                $get_id_project = LokasiGcp::where('id_project', $key_csql)->first();

                foreach ($value_csql as $key_sub_csql => $value_sub_csql) {

                    foreach ($value_sub_csql as $key_sub_2_csqsl => $value_sub_2_csql) {
                        // Prepare Data

                        // vCPU - RAM
                        $tipe_array = explode('-', $value_sub_2_csql->settings->tier);

                        if (in_array("custom", $tipe_array)) {
                            $data_vcpu = $tipe_array[2];
                            $data_ram = $tipe_array[3] / 1024;
                        } else {
                            $data_vcpu  = 1;

                            if ($value_sub_2_csql->settings->tier == "db-f1-micro") {
                                $data_ram   = 0.6;
                            } else {
                                $data_ram   = 1.7;
                            }
                    
                        }
                        

                        // Upsert

                        $data = CloudSqlGcp::updateOrCreate(
                            // Cek Variable
                            [
                                'nama' => $value_sub_2_csql->name
                            ],
                            // Simpan Sisa/Semua
                            [
                                'lokasi_gcp_id'     => $get_id_project->id,
                                'tipe'              => $value_sub_2_csql->settings->tier,
                                'pub_ip'            => $value_sub_2_csql->ipAddresses[0]->ipAddress,
                                'v_cpu'             => $data_vcpu,
                                'ram'               => $data_ram,
                                'disk'              => $value_sub_2_csql->settings->dataDiskSizeGb,
                                'db_ver'            => $value_sub_2_csql->databaseVersion,
                                'status'            => $value_sub_2_csql->state,
                                'dibuat'            => $value_sub_2_csql->createTime
                            ]
                        );
                    }

                }

            };

            // Jika Semua Normal, Commit ke DB
            DB::commit(); 
            
        } catch (\Exception $e) {
            // Jika ada yang Gagal, Rollback DB
            DB::rollBack();

            Log::error('ERROR - GCP - Save Get CloudSQL', (array)$e->getMessage());
        }

        // Return
        return back()->with('pesan', 'Berhasil Sync Data !');

    }

    public function gcp_index(Request $request)
    {
        $data_server    = ServerGcp::all();
        $data_csql      = CloudSqlGcp::all();
        $cari_layanan   = $request->cari_layanan;

        return view('report.gcp', compact(
            'data_server',
            'data_csql',
            'cari_layanan'
        ));
    }

    //  --- Proxmox Infra ---

    public function getTimeSeriesData($id_node, $node_name, $vm_id)
    {
       // Get Data Node
        $data_node = LokasiProxmox::where('id', $id_node)->first();

        // Auth to Node
        $data_auth = [
            'ip'    => $data_node->ip_node,
            'port'  => $data_node->port_node,
            'auth'  => Crypt::decryptString($data_node->token),
            'node'  => $data_node->nama_node
        ];

        // Fetch Data to Bunker
        $client = new Client([
            'base_uri' => 'https://' . $data_auth['ip'] . ':' . $data_auth['port'],
            'verify' => false,
            'headers' => [
                'Authorization' => $data_auth['auth']
            ],

        ]);

        $endpoint = 'api2/json/nodes/' . $data_auth['node'] . '/qemu/' . $vm_id . '/rrddata';
        
        $parameterRequest = [
            'query' => [
                'timeframe' => 'month'
            ]
        ];

        $response = $client->request('GET', $endpoint, $parameterRequest);
        $responseJSONencoded = json_decode($response->getBody())->data;

        $cpuUsage = [];
        $memUsage = [];

        $timeSeriesData = [];

        foreach ($responseJSONencoded as $key => $value) {            
            if (isset($value->cpu) && isset($value->mem)) {
                array_push($cpuUsage, $value->cpu);
                array_push($memUsage, $value->mem);

                array_push($timeSeriesData, $value);
            }
        }

        if (count($cpuUsage) != 0 || count($memUsage) != 0) {
            
            $cpuAverage = array_sum($cpuUsage)/count($cpuUsage);
            $cpuUsagePercentage = $cpuAverage * 100;

            $memAverage = array_sum($memUsage)/count($memUsage);
            $memUsagePercentage = ($memAverage / $timeSeriesData[0]->maxmem) * 100;
            // $memUsageReal = $memAverage / (1024*1024*1024);
        } else {
            $cpuAverage = 0;
            $cpuUsagePercentage = $cpuAverage * 100;

            $memAverage = 0;
            $memUsagePercentage = 0 * 100;
        }
        


        $summary = [
            'usage' => [
                'cpu'   => number_format($cpuUsagePercentage, 2, '.', '') . ' %',
                'mem'   => number_format($memUsagePercentage, 2, '.', '') . ' %'
            ],

            'recomendation' => [
                'cpu'   => $retVal = ($cpuUsagePercentage < 60) ? 'Scale it Down !' : 'None',
                'mem'   => $retVal = ($memUsagePercentage < 60) ? 'Scale it Down !' : 'None'
            ]
        ];

        return response()->json([
            'Data'      => $summary,
            'Message'   => 'Data Berhasil di Ambil'
        ], 200);
    }

    public function proxmox_get_all_vm($id_node)
    {
        // Get Data Node
        $data_node = LokasiProxmox::where('id', $id_node)->first();

        // Auth to Node
        $data_auth = [
            'ip'    => $data_node->ip_node,
            'port'  => $data_node->port_node,
            'auth'  => Crypt::decryptString($data_node->token),
            'node'  => $data_node->nama_node
        ];

        // Fetch Data to Bunker
        $client = new Client([
            'base_uri' => 'https://' . $data_auth['ip'] . ':' . $data_auth['port'],
            'verify' => false,
            'headers' => [
                'Authorization' => $data_auth['auth']
            ],
        ]);

        $endpoint = 'api2/json/nodes/' . $data_auth['node'] . '/qemu';

        $response = $client->get($endpoint);
        $responseJSONencoded = json_decode($response->getBody())->data;

        $vmList = [];

        foreach ($responseJSONencoded as $key => $value) {
            $tempData = $value;
            $tempData->insight = $this->getTimeSeriesData($id_node, $data_node->nama_node, $value->vmid)->getData()->Data;

            array_push($vmList, $tempData);
            // $vmList[$data_node->nama_node] = $tempData;
        }

        return $vmList;

    }
    
    public function proxmox_vm_sync()
    {
        // Get Data Node
        $data_node = LokasiProxmox::all();
        $vm_mapped_data = [];
        
        // Sync VM
        foreach ($data_node as $key_node => $value_node) {
            try {
                $vm_data = $this->proxmox_get_all_vm($value_node->id);
                $vm_mapped_data[$value_node->nama_node] = $vm_data;
            } catch (\Exception $e) {
                // continue;
                Log::error('ERROR - Proxmox - Save Get VM', (array)$e->getMessage());
            }
        }

        // dd($vm_mapped_data);

        // Save VM to DB
        DB::beginTransaction();

        try {

            foreach ($vm_mapped_data as $key_vm => $value_vm) {
                
                $get_id_node = LokasiProxmox::where('nama_node', $key_vm)->first();

                foreach ($value_vm as $key_sub_vm => $value_sub_vm) {
                    
                    // dd($value_sub_vm);

                    // Upsert

                    $data = ServerProxmox::updateOrCreate(
                        // Cek Variable
                        [
                            'vm_id' => $value_sub_vm->vmid
                        ],
                        // Simpan Sisa/Semua
                        [
                            'lokasi_proxmox_id' => $get_id_node->id,
                            'vm_id'             => $value_sub_vm->vmid,
                            'nama'              => $value_sub_vm->name,
                            'priv_ip'           => '',
                            'v_cpu'             => $value_sub_vm->cpus,
                            'ram'               => $value_sub_vm->maxmem / (1024*1024*1024),
                            'disk'              => $value_sub_vm->maxdisk / (1024*1024*1024),
                            'status'            => $value_sub_vm->status,
                            'usage_cpu'         => $value_sub_vm->insight->usage->cpu,
                            'usage_ram'         => $value_sub_vm->insight->usage->mem,
                            'rec_cpu'           => $value_sub_vm->insight->recomendation->cpu,
                            'rec_ram'           => $value_sub_vm->insight->recomendation->mem,
                        ]
                    );

                }

            }
            
            // Jika Semua Normal, Commit ke DB
            DB::commit();

        } catch (\Exception $e) {
            
            // Jika ada yang Gagal, Rollback DB
            DB::rollBack();

            Log::error('ERROR - Proxmox - Save Get VM', (array)$e->getMessage());

        }

        // Return
        return back()->with('pesan', 'Berhasil Sync Data !');

    }

    public function proxmox_index(Request $request)
    {
        $cari_node = $request->cari_node;

        $data_server = ServerProxmox::where('lokasi_proxmox_id', $cari_node)
                                    ->orderBy('vm_id', 'asc')
                                    ->get();
                                    
        $data_nama_node = LokasiProxmox::all();

        return view('report.proxmox', compact(
            'data_server',
            'data_nama_node',
            'cari_node'
        ));
    }

    
    // Tes Ajaaaa
    
    public function gcp_ce_sync_vpc()
    {

        $data_project = LokasiGcp::all();
        $ce_mapped_data = [];

        // Sync CE
        foreach ($data_project as $key_project => $value_project) {
            
            try {
                $ce_data = $this->gcp_get_all_ce($value_project->id_project);
                $ce_mapped_data[$value_project->id_project] = $ce_data;
            } catch (\Throwable $th) {
                continue;
            }

        }

        // dd($ce_mapped_data);

        $data_kirim = [];

        foreach ($ce_mapped_data as $key_ce => $value_ce) {
                

            $get_id_project = LokasiGcp::where('id_project', $key_ce)->first();

            foreach ($value_ce as $key_sub_ce => $value_sub_ce) {
                
                // Prepare Data

                $vpc_now        = $value_sub_ce->networkInterfaces[0]->network;
                $vpc_now_array  = explode('/', $vpc_now);
                $vpc_now_extracted = array_slice($vpc_now_array, 9, 1);
            


                // Assign Data
                $data_tambah = [
                    'nama'      => $value_sub_ce->name,
                    'lokasi'    => $get_id_project->nama_project,
                    'vpc'       => $vpc_now_extracted[0]

                ];

                array_push($data_kirim, $data_tambah);

            }

        };

        $final_data = $data_kirim;


        // Return
        return view('tes-vpc', compact('final_data'));


    }
    

}
