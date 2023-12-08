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
        Schema::create('promosis', function (Blueprint $table) {
            $table->id();
            $table->string('namapromosi');
            $table->string('deskripsi');
            $table->string('jumlahpoin');
            $table->string('gambar');
            $table->timestamps();
            // $table->foreign('jumlahpoin')->references('jumlahpoin')->on('pointransaksis');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::table('promosis', function (Blueprint $table) {
        //     $table->dropForeign(['jumlahpoin']);
        // });
        Schema::dropIfExists('promosis');
    }
};
