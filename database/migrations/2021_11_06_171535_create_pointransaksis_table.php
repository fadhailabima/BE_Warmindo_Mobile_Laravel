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
        Schema::create('pointransaksis', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->time('waktu');
            $table->unsignedBigInteger('idpelanggan');
            $table->string('jumlahpoin');
            $table->enum('status', ['tambah', 'kurang'])->nullable();
            $table->string('poinawal');
            $table->string('poinakhir');
            $table->enum('sumber', ['transaksi', 'promosi'])->nullable();
            $table->timestamps();
            $table->foreign('idpelanggan')->references('id')->on('pelanggans');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pointransaksis', function (Blueprint $table) {
            $table->dropForeign(['idpelanggan']);
        });
        Schema::dropIfExists('pointransaksis');
    }
};
