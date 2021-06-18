<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePendingMembershipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pending_memberships', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('last_name',20);
            $table->string('first_name',20);
            $table->string('phone',20)
                ->nullable();
            $table->string('mobile',20)
                ->nullable();
            $table->string('email',60);
            $table->string('address_line_1',40);
            $table->string('address_line_2',40)
                ->nullable();
            $table->string('suburb',40)
                ->nullable();
            $table->string('postcode',4)
                ->nullable();
            $table->string('emergency_last_name',20);
            $table->string('emergency_first_name',20);
            $table->string('emergency_phone',20)
                ->nullable();
            $table->string('emergency_mobile',20)
                ->nullable();
            $table->string('emergency_email',60)
                ->nullable();
            $table->string('payment',6);
            $table->string('newsletter',6);
            $table->string('hash',32);
            $table->string('password',20);
            $table->string('username',20);
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
        Schema::dropIfExists('pending_memberships');
    }
}
