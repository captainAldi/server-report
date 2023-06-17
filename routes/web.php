<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
})->name('awal');

// Add Verify Page to Auth Scaffolding
Auth::routes(['verify' => true]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Route::get('tes-vpc', [App\Http\Controllers\ReportController::class, 'gcp_ce_sync_vpc']);

// No Prefix and Auth Middleware
Route::middleware(['auth', 'verified'])->group(function () {

    

    // Lokasi
    Route::get('lokasi/gcp', [App\Http\Controllers\LokasiController::class, 'gcp_index'])->name('get.lokasi.gcp');
    Route::get('lokasi/gcp/sync', [App\Http\Controllers\LokasiController::class, 'gcp_sync'])->name('get.lokasi.gcp.sync');
    Route::get('lokasi/gcp/detail/{id}', [App\Http\Controllers\LokasiController::class, 'gcp_detail'])->name('get.lokasi.gcp.detail');

    Route::get('lokasi/proxmox', [App\Http\Controllers\LokasiController::class, 'proxmox_index'])->name('get.lokasi.proxmox');
    Route::post('lokasi/proxmox/save', [App\Http\Controllers\LokasiController::class, 'proxmox_proses_simpan'])->name('post.lokasi.proxmox.proses-simpan');
    Route::patch('lokasi/proxmox/update/{id}', [App\Http\Controllers\LokasiController::class, 'proxmox_proses_ubah'])->name('patch.lokasi.proxmox.proses-ubah');

    // Report Usage
    Route::get('report/usage/gcp', [App\Http\Controllers\ReportController::class, 'gcp_index'])->name('get.report.usage.gcp');
    Route::get('report/usage/gcp/ce/detail/{id}', [App\Http\Controllers\ReportController::class, 'gcp_ce_detail'])->name('get.report.usage.gcp.detail.ce');
    Route::get('report/usage/gcp/csql/detail/{id}', [App\Http\Controllers\ReportController::class, 'gcp_csql_detail'])->name('get.report.usage.gcp.detail.csql');

    Route::get('report/usage/gcp/ce/sync', [App\Http\Controllers\ReportController::class, 'gcp_ce_sync'])->name('get.report.usage.gcp.ce.sync');
    Route::get('report/usage/gcp/csql/sync', [App\Http\Controllers\ReportController::class, 'gcp_csql_sync'])->name('get.report.usage.gcp.csql.sync');

    Route::get('report/usage/proxmox', [App\Http\Controllers\ReportController::class, 'proxmox_index'])->name('get.report.usage.proxmox');
    Route::get('report/usage/proxmox/ce/detail/{id}', [App\Http\Controllers\ReportController::class, 'proxmox_detail'])->name('get.report.usage.proxmox.detail');

    Route::get('report/usage/proxmox/vm/sync', [App\Http\Controllers\ReportController::class, 'proxmox_vm_sync'])->name('get.report.usage.proxmox.vm.sync');

    // Report History
    Route::get('report/history', [App\Http\Controllers\ReportController::class, 'history_index'])->name('get.report.history');

});
