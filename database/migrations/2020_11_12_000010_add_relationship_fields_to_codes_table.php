<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToCodesTable extends Migration
{
    public function up()
    {
        Schema::table('codes', function (Blueprint $table) {
            $table->unsignedInteger('coupon_id');
            $table->foreign('coupon_id', 'coupon_fk_2578365')->references('id')->on('coupons');
            $table->unsignedInteger('reserved_by_id')->nullable();
            $table->foreign('reserved_by_id', 'reserved_by_fk_2578368')->references('id')->on('users');
            $table->unsignedInteger('purchased_by_id')->nullable();
            $table->foreign('purchased_by_id', 'purchased_by_fk_2578370')->references('id')->on('users');
        });
    }
}
