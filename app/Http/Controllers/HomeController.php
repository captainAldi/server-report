<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\ServerGcp;
use App\Models\CloudSqlGcp;

use App\Models\ServerProxmox;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $data_gcp_ce_running    = ServerGcp::where('status', 'RUNNING')->count();
        $data_gcp_csql_running  = CloudSqlGcp::where('status', 'RUNNABLE')->count();

        $data_proxmox_vm_running  = ServerProxmox::where('status', 'running')->count();

        $count_all_instances = $data_gcp_ce_running + $data_gcp_csql_running + $data_proxmox_vm_running;

        return view('home', compact(
            'data_gcp_ce_running',
            'data_gcp_csql_running',
            'data_proxmox_vm_running',
            'count_all_instances',
        ));
    }
}
