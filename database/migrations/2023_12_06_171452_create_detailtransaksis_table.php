<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detailtransaksis', function (Blueprint $table) {
            $table->id();
            $table->string('idtransaksi');
            $table->unsignedBigInteger('idmenu');
            $table->string('namamenu');
            $table->string('harga');
            $table->string('jumlah');
            $table->string('subtotal');
            $table->enum('status', ['aktif', 'batal'])->nullable();
            $table->timestamps();
            $table->foreign('idmenu')->references('id')->on('menus');
            // $table->foreign('namamenu')->references('nama_menu')->on('menus');
            // $table->foreign('harga')->references('harga')->on('menus');
        });

        Schema::table('detailtransaksis', function (Blueprint $table) {
            $table->foreign('idtransaksi')->references('idtransaksi')->on('transaksis'); // Kunci asing ke kolom 'user_id' pada tabel 'users'
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('detailtransaksis', function (Blueprint $table) {
            $table->dropForeign(['idtransaksi']);
            $table->dropForeign(['idmenu']);
        });
        Schema::dropIfExists('detailtransaksis');
    }
};
