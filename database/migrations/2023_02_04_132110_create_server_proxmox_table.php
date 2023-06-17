<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('server_proxmox', function (Blueprint $table) {
            $table->id();
            $table->integer('lokasi_proxmox_id');
            $table->string('vm_id');
            $table->string('nama');
            $table->string('priv_ip');
            $table->integer('v_cpu');
            $table->integer('ram');
            $table->integer('disk');
            $table->string('status');
            $table->string('usage_cpu');
            $table->string('usage_ram');
            $table->string('rec_cpu');
            $table->string('rec_ram');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('server_proxmox');
    }
};
