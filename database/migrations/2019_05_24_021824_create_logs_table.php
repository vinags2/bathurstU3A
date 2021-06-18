<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('type',10);
            $table->boolean('success')
                ->default(true);
            $table->unsignedBigInteger('person_id')
                ->nullable();
            $table->text('description_1')
                ->nullable();
            $table->text('description_2')
                ->nullable();
            $table->timestamps();
            $table->index('person_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('logs');
    }
}
