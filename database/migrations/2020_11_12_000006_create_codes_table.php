<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCodesTable extends Migration
{
    public function up()
    {
        Schema::create('codes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->datetime('reserved_at')->nullable();
            $table->datetime('purchased_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
