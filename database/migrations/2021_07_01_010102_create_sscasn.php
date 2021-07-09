<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSscasn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sscasn', function (Blueprint $table) {
            $table->increments('id');
            $table->string('jenis')->nullable();
            $table->string('instansi')->nullable();
            $table->string('jabatan')->nullable();
            $table->text('pendidikan')->nullable();
            $table->string('formasi')->nullable();
            $table->string('disabilitas')->nullable();
            $table->integer('kebutuhan')->nullable();
            $table->string('kode')->nullable();
            $table->text('lokasi')->nullable();
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
        Schema::dropIfExists('sscasn');
    }
}
