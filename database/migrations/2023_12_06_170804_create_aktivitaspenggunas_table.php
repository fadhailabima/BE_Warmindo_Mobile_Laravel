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
        Schema::create('aktivitaspenggunas', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->time('waktu');
            $table->string('id_pengguna');
            $table->enum('aktivitas', ['login', 'akses shift', 'logout'])->nullable();
            $table->timestamps();
            // $table->foreign('id_pengguna')->references('id')->on('users');
        });

        Schema::table('aktivitaspenggunas', function (Blueprint $table) {
            $table->foreign('id_pengguna')->references('idkaryawan')->on('users'); // Kunci asing ke kolom 'user_id' pada tabel 'users'
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('aktivitaspenggunas', function (Blueprint $table) {
            $table->dropForeign(['id_pengguna']);
        });
        Schema::dropIfExists('aktivitaspenggunas');
    }
};
