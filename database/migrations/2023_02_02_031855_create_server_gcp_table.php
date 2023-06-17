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
        Schema::create('server_gcp', function (Blueprint $table) {
            $table->id();
            $table->integer('lokasi_gcp_id');
            $table->string('ce_id');
            $table->string('nama');
            $table->string('tipe');
            $table->string('priv_ip');
            $table->string('pub_ip');
            $table->integer('v_cpu');
            $table->integer('ram');
            $table->integer('disk');
            $table->string('status');
            $table->timestamp('dibuat');
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
        Schema::dropIfExists('server_gcp');
    }
};
