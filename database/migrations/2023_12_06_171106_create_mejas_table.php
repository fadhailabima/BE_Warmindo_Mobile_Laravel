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
        Schema::create('mejas', function (Blueprint $table) {
            $table->id();
            $table->string('id_warung');
            $table->string('kodemeja');
            $table->foreign('id_warung')->references('idwarung')->on('warungs');
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

        Schema::table('mejas', function (Blueprint $table) {
            $table->dropForeign(['id_warung']);
        });
        Schema::dropIfExists('mejas');
    }
};
