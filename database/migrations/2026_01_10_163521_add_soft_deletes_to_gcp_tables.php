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
        Schema::table('lokasi_gcp', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('server_gcp', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('csql_gcp', function (Blueprint $table) {
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
        Schema::table('lokasi_gcp', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('server_gcp', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('csql_gcp', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};