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

use App\Exports\ServerGcpExport;
use App\Exports\CloudSqlGcpExport;
use App\Exports\ServerProxmoxExport;

use Google\Auth\ApplicationDefaultCredentials;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;

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

        // Save CE to DB

        // Proses Simpan ke DB
        DB::beginTransaction();

        try {
            
            $valid_ce_ids = [];

            foreach ($ce_mapped_data as $key_ce => $value_ce) {
                

                $get_id_project = LokasiGcp::where('id_project', $key_ce)->first();

                foreach ($value_ce as $key_sub_ce => $value_sub_ce) {
                    
                    // Prepare Data

                    $machine_type = $this->gcp_get_ce_type($value_sub_ce->machineType);

                    $valid_ce_ids[] = $value_sub_ce->id;

                    // Upsert

                    $data = ServerGcp::withTrashed()->updateOrCreate(
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

                    if ($data->trashed()) {
                        $data->restore();
                    }

                }

            };

            // Soft delete instances that are no longer in GCP
            ServerGcp::whereNotIn('ce_id', $valid_ce_ids)->delete();

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

    public function gcp_ce_sync_del()
    {

        $data_project = LokasiGcp::all();
        $ce_mapped_data_server = [];
        $ce_mapped_data_db = [];

        $ce_to_delete = [];

        // Sync CE
        foreach ($data_project as $key_project => $value_project) {
            
            try {
                $ce_data_server = $this->gcp_get_all_ce($value_project->id_project);
                $ce_mapped_data_server[$value_project->id_project] = $ce_data_server;
                
                $ce_data_db = ServerGcp::where('lokasi_gcp_id', $value_project->id)->get()->toArray();
                $ce_mapped_data_db[$value_project->id_project] = $ce_data_db;
                
            } catch (\Throwable $th) {
                continue;
            }

        }

        // --- Check All ---

        // Looping Projects GCP
        foreach ($ce_mapped_data_db as $key_ce_mapped_data_db => $value_ce_mapped_data_db) {

            try {
                
                // Looping Setiap VM di Projects
                foreach ($value_ce_mapped_data_db as $key_ce => $value_ce) {
                
                    // Cek Array
                    try {
                        
                        $data_to_check = array_search($value_ce['ce_id'], array_column($ce_mapped_data_server[$key_ce_mapped_data_db], 'id'));    

                        // Jika Tidak ada di Cloud
                        if ($data_to_check === false) {
                            array_push($ce_to_delete, $value_ce['ce_id']);
                        }              

                    } catch (\Throwable $th) {
                        continue;
                    }

                }

            } catch (\Throwable $th) {
                continue;
            }
            

        }


        // Update to DB for Deleted

        DB::beginTransaction();

        try {
            
            // Update Deleted to DB
            foreach ($ce_to_delete as $key => $value) {
                
                // Get Server from DB
                $db_to_update = ServerGcp::where('ce_id', $value)->first();
                $db_to_update->status = "DELETED";

                // Save
                $db_to_update->save();

            }

            // Jika Semua Normal, Commit ke DB
            DB::commit(); 

        } catch (\Throwable $th) {
            // Jika ada yang Gagal, Rollback DB
            DB::rollBack();

            Log::error('ERROR - GCP - Update Deleted CE', (array)$e->getMessage());
        }

        // Return
        return back()->with('pesan', 'Berhasil Sync Deleted Data !');
        
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
            
            $valid_csql_names = [];

            foreach ($csql_mapped_data as $key_csql => $value_csql) {
                
                $get_id_project = LokasiGcp::where('id_project', $key_csql)->first();

                foreach ($value_csql as $key_sub_csql => $value_sub_csql) {

                    foreach ($value_sub_csql as $key_sub_2_csqsl => $value_sub_2_csql) {
                        // Prepare Data

                        $valid_csql_names[] = $value_sub_2_csql->name;

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

                        $data = CloudSqlGcp::withTrashed()->updateOrCreate(
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

                        if ($data->trashed()) {
                            $data->restore();
                        }
                    }

                }

            };

            // Soft delete Cloud SQL instances that are no longer in GCP
            CloudSqlGcp::whereNotIn('nama', $valid_csql_names)->delete();

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

    public function gcp_csql_sync_del()
    {

        $data_project = LokasiGcp::all();
        $csql_mapped_data_server = [];
        $csql_mapped_data_db = [];

        $csql_to_delete = [];

        // Sync CSQL
        foreach ($data_project as $key_project => $value_project) {
            
            try {
                $csql_data_server = $this->gcp_get_all_csql($value_project->id_project);
                $csql_mapped_data_server[$value_project->id_project] = $csql_data_server;
                
                $csql_data_db = CloudSqlGcp::where('lokasi_gcp_id', $value_project->id)->get()->toArray();
                $csql_mapped_data_db[$value_project->id_project] = $csql_data_db;
                
            } catch (\Throwable $th) {
                continue;
            }

        }


        // --- Check All ---

        // Looping Projects GCP
        foreach ($csql_mapped_data_db as $key_csql_mapped_data_db => $value_csql_mapped_data_db) {

            try {
                
                // Looping Setiap VM di Projects
                foreach ($value_csql_mapped_data_db as $key_csql => $value_csql) {
                
                    // Cek Array
                    try {
                        
                        $data_to_check = array_search($value_csql['nama'], array_column($csql_mapped_data_server[$key_csql_mapped_data_db], 'name'));    

                        // Jika Tidak ada di Cloud
                        if ($data_to_check === false) {
                            array_push($csql_to_delete, $value_csql['nama']);
                        }              

                    } catch (\Throwable $th) {
                        continue;
                    }

                }

            } catch (\Throwable $th) {
                continue;
            }
            

        }


        // Update to DB for Deleted

        DB::beginTransaction();

        try {
            
            // Update Deleted to DB
            foreach ($csql_to_delete as $key => $value) {
                
                // Get Server from DB
                $db_to_update = ServerGcp::where('nama', $value)->first();
                $db_to_update->status = "DELETED";

                // Save
                $db_to_update->save();

            }

            // Jika Semua Normal, Commit ke DB
            DB::commit(); 

        } catch (\Throwable $th) {
            // Jika ada yang Gagal, Rollback DB
            DB::rollBack();

            Log::error('ERROR - GCP - Update Deleted Cloud SQL', (array)$e->getMessage());
        }

        // Return
        return back()->with('pesan', 'Berhasil Sync Deleted Data !');
        
    }

    // GCP Index
    public function gcp_index(Request $request)
    {
        $data_semua_ce    = null;
        $data_semua_csql  = null;
        $data_semua_lokasi = LokasiGcp::withTrashed()->get();

        $cari_layanan   = $request->get('cari_layanan');

        //Variable Pencarian
        $cari_nama = $request->get('cari_nama');
        $cari_lokasi = $request->get('cari_lokasi');
        $cari_status = $request->get('cari_status');

        $tipe_sort = 'desc';
        $var_sort = 'created_at';

        $set_pagination = $request->get('set_pagination');

        // Sum Aggregates 
        $total_cpu_used = 0;
        $total_ram_used = 0;
        $total_disk_used = 0;

        if ($cari_layanan == 'Compute Engine') {

            // Semua CD
            $data_semua_ce = ServerGcp::withTrashed();

            //Kondisi
            if($cari_nama != '') {
                $data_semua_ce = $data_semua_ce->where('nama','LIKE','%'.$cari_nama.'%');
            }

            if($cari_lokasi != '') {
                $data_semua_ce = $data_semua_ce->where('lokasi_gcp_id', $cari_lokasi);
            }

            if($cari_status != '') {
                $data_semua_ce = $data_semua_ce->where('status', $cari_status);
            }

            if( $request->has('tipe_sort') || $request->has('var_sort') ) {
                $tipe_sort = $request->get('tipe_sort');
                $var_sort = $request->get('var_sort');

                $data_semua_ce = $data_semua_ce->orderBy($var_sort, $tipe_sort);
            }


            // Paginate

            if ($set_pagination != '') {
                $data_semua_ce = $data_semua_ce
                            ->orderBy($var_sort, $tipe_sort)
                            ->paginate($set_pagination);
            } else {
                $data_semua_ce = $data_semua_ce
                            ->orderBy($var_sort, $tipe_sort)
                            ->paginate(10);
            }

            $data_semua_ce->appends($request->only(
                $cari_layanan,
                $cari_nama, 
                $cari_lokasi, 
                $cari_status, 

                $tipe_sort,
                $var_sort
            ));

            // Sum Resources
            $total_cpu_used = DB::table('server_gcp')
                                ->where('status', 'running')
                                ->whereNull('deleted_at')
                                ->sum("v_cpu");
            
            $total_ram_used = DB::table('server_gcp')
                                ->where('status', 'running')
                                ->whereNull('deleted_at')
                                ->sum("ram");
            
            $total_disk_used = DB::table('server_gcp')
                                ->where('status', 'running')
                                ->whereNull('deleted_at')
                                ->sum("disk");

        } elseif ($cari_layanan == 'Cloud SQL') {
            
            // Semua CloudSQL
            $data_semua_csql = CloudSqlGcp::withTrashed();

            //Kondisi
            if($cari_nama != '') {
                $data_semua_csql = $data_semua_csql->where('nama','LIKE','%'.$cari_nama.'%');
            }

            if($cari_lokasi != '') {
                $data_semua_csql = $data_semua_csql->where('lokasi_gcp_id', $cari_lokasi);
            }

            if($cari_status != '') {
                $data_semua_csql = $data_semua_csql->where('status', $cari_status);
            }

            if( $request->has('tipe_sort') || $request->has('var_sort') ) {
                $tipe_sort = $request->get('tipe_sort');
                $var_sort = $request->get('var_sort');

                $data_semua_csql = $data_semua_csql->orderBy($var_sort, $tipe_sort);
            }


            // Paginate

            if ($set_pagination != '') {
                $data_semua_csql = $data_semua_csql
                            ->orderBy($var_sort, $tipe_sort)
                            ->paginate($set_pagination);
            } else {
                $data_semua_csql = $data_semua_csql
                            ->orderBy($var_sort, $tipe_sort)
                            ->paginate(10);
            }

            $data_semua_csql->appends($request->only(
                $cari_layanan,
                $cari_nama, 
                $cari_lokasi, 
                $cari_status, 

                $tipe_sort,
                $var_sort
            ));

            // Sum Resources
            $total_cpu_used = DB::table('csql_gcp')
                                ->where('status', 'runnable')
                                ->whereNull('deleted_at')
                                ->sum("v_cpu");
            
            $total_ram_used = DB::table('csql_gcp')
                                ->where('status', 'runnable')
                                ->whereNull('deleted_at')
                                ->sum("ram");
            
            $total_disk_used = DB::table('csql_gcp')
                                ->where('status', 'runnable')
                                ->whereNull('deleted_at')
                                ->sum("disk");

        } 

        

        
         return view('report.gcp', compact(
            'data_semua_ce',
            'data_semua_csql',
            'data_semua_lokasi',

            'cari_layanan',
            'cari_nama',
            'cari_lokasi',
            'cari_status',

            'tipe_sort',
            'var_sort',
            'set_pagination',

            'total_cpu_used',
            'total_ram_used',
            'total_disk_used'
        ));
        
    }

    public function gcp_ce_excel(Request $request)
    {
        
        $cari_nama = $request->get('cari_nama');
        $cari_lokasi = $request->get('cari_lokasi');
        $cari_status = $request->get('cari_status');

        $date = date("Y-m-d_H:i:s");

        return Excel::download(new ServerGcpExport($cari_nama, $cari_lokasi, $cari_status), 'server-report-' . $date . '.xlsx');
    }

    public function gcp_csql_excel(Request $request)
    {
        
        $cari_nama = $request->get('cari_nama');
        $cari_lokasi = $request->get('cari_lokasi');
        $cari_status = $request->get('cari_status');

        $date = date("Y-m-d_H:i:s");

        return Excel::download(new CloudSqlGcpExport($cari_nama, $cari_lokasi, $cari_status), 'csql-report-' . $date . '.xlsx');
    }

    public function gcp_ce_detail($id)
    {
        $data_server = ServerGcp::with('lokasi_gcp')->findOrFail($id);
        
        return view('report.gcp-ce-detail', compact('data_server'));
    }

    public function gcp_csql_detail($id)
    {
        $data_server = CloudSqlGcp::with('lokasi_gcp')->findOrFail($id);
        
        return view('report.gcp-csql-detail', compact('data_server'));
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

        // Sum Resources
        $total_cpu_used = 0;
        $total_ram_used = 0;
        $total_disk_used = 0;

        if ($cari_node != '') {
            $total_cpu_used = DB::table('server_proxmox')
                                ->where('lokasi_proxmox_id', $cari_node)
                                ->where('status', 'running')
                                ->sum("v_cpu");

            $total_ram_used = DB::table('server_proxmox')
                                ->where('lokasi_proxmox_id', $cari_node)
                                ->where('status', 'running')
                                ->sum("ram");

            $total_disk_used = DB::table('server_proxmox')
                                ->where('lokasi_proxmox_id', $cari_node)
                                ->where('status', 'running')
                                ->sum("disk");
        }

        return view('report.proxmox', compact(
            'data_server',
            'data_nama_node',
            'cari_node',
            'total_cpu_used',
            'total_ram_used',
            'total_disk_used'
        ));
    }

    public function proxmox_excel(Request $request)
    {

        $cari_node = $request->get('cari_node');

        $date = date("Y-m-d_H:i:s");

        return Excel::download(new ServerProxmoxExport($cari_node), 'proxmox-report-' . $date . '.xlsx');
    }

    public function proxmox_start_vm($id_node, $id_vm)
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

        $endpoint = 'api2/json/nodes/' . $data_auth['node'] . '/qemu/' . $id_vm . '/status/start';

        $response = $client->post($endpoint);
        $responseJSONencoded = json_decode($response->getBody())->data;

        return back()->with('pesan', 'berhasil hidupkan vm !');
    }

    public function proxmox_detail($id)
    {
        $data_server = ServerProxmox::with('lokasi_proxmox')->findOrFail($id);
        
        return view('report.proxmox-detail', compact('data_server'));
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
