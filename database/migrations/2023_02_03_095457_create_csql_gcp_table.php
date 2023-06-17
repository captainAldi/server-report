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
        Schema::create('csql_gcp', function (Blueprint $table) {
            $table->id();
            $table->integer('lokasi_gcp_id');
            $table->string('nama');
            $table->string('tipe');
            $table->string('pub_ip');
            $table->float('v_cpu');
            $table->float('ram');
            $table->float('disk');
            $table->string('db_ver');
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
        Schema::dropIfExists('csql_gcp');
    }
};
