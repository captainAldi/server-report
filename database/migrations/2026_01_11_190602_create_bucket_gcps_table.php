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
        Schema::create('bucket_gcp', function (Blueprint $table) {
            $table->id();
            $table->integer('lokasi_gcp_id');
            $table->string('nama');
            $table->string('lokasi')->nullable();
            $table->string('tipe_storage')->nullable();
            $table->timestamp('dibuat')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bucket_gcp');
    }
};