<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOldUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('old_users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('user_name',30)
                ->unique();
            $table->string('user_email',60)
                ->nullable();
            $table->string('user_password',255);
            $table->string('real_name',30);
            $table->string('role',45);
            $table->boolean('active');
            $table->unsignedTinyInteger('person_id')
                ->comment('pointer to persons table');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('old_users');
    }
}
