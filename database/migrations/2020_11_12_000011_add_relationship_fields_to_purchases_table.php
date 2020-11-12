<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToPurchasesTable extends Migration
{
    public function up()
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->unsignedInteger('user_id');
            $table->foreign('user_id', 'user_fk_2578384')->references('id')->on('users');
            $table->unsignedInteger('code_id');
            $table->foreign('code_id', 'code_fk_2578385')->references('id')->on('codes');
        });
    }
}
