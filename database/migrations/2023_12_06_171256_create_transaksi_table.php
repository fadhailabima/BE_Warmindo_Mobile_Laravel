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
            $table->string('idtransaksi')->primary();
            $table->date('tanggal');
            $table->time('waktu');
            $table->enum('shift', ['1', '2'])->nullable();
            $table->string('id_pengguna');
            $table->unsignedBigInteger('id_pelanggan')->nullable();
            $table->enum('status', ['baru','diproses','disajikan','selesai'])->nullable();
            $table->string('kode_meja')->nullable();
            $table->string('namapelanggan')->nullable();
            $table->string('total');
            $table->enum('metode_pembayaran', ['cash', 'kartu kredit', 'kartu debit', 'qris'])->nullable();
            $table->string('totaldiskon')->nullable();
            $table->unsignedBigInteger('idpromosi')->nullable();
            $table->foreign('id_pengguna')->references('idkaryawan')->on('users');
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
