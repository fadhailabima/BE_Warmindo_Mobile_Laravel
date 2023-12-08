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
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->time('waktu');
            $table->boolean('shift')->default('1');
            $table->string('id_pengguna');
            $table->string('id_pelanggan')->nullable();
            $table->enum('status', ['baru','diproses','disajikan','selesai'])->nullable();
            $table->string('kodemeja');
            $table->string('namapelanggan')->nullable();
            $table->string('total');
            $table->enum('metode_pembayaran', ['cash', 'kartu kredit', 'kartu debit', 'qris'])->nullable();
            $table->string('totaldiskon');
            $table->string('idpromosi')->nullable();
            $table->foreign('id_pengguna')->references('id')->on('users');
            $table->foreign('id_pelanggan')->references('id')->on('pelanggans');
            // $table->foreign('kodemeja')->references('kodemeja')->on('mejas');
            // $table->foreign('namapelanggan')->references('namapelanggan')->on('pelanggans');
            $table->foreign('idpromosi')->references('id')->on('promosis');
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
        Schema::table('transaksis', function (Blueprint $table) {
            $table->dropForeign(['id_pengguna']);
            $table->dropForeign(['id_pelanggan']);
            // $table->dropForeign(['kodemeja']);
            // $table->dropForeign(['namapelanggan']);
            $table->dropForeign(['idpromosi']);
        });
        Schema::dropIfExists('transaksis');
    }
};
