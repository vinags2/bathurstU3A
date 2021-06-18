<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSessionAttendeeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('session_attendee', function (Blueprint $table) {
            $table->unsignedBigInteger('session_id');
            $table->unsignedBigInteger('person_id');
            $table->boolean('confirmed')
                ->default(true)
                ->comment('false = on a waiting list');
            $table->date('date_of_enrolment')
                ->nullable();
            $table->date('date_of_withdrawal') // >> Addition <<
                ->nullable();
            $table->year('year'); // >> Addition <<
            $table->unsignedTinyInteger('term')
                ->default(0); // >> Addition <<
            $table->string('updatedby', 20)
                ->nullable()
                ->comment('the original UpdatedBy - the person\'s name');
            $table->unsignedBigInteger('updated_by')
                ->nullable()
                ->comment('pointer to person table id');
            $table->boolean('deleted')
                ->nullable()
                ->default(false);
            $table->timestamps();
            $table->primary(['session_id','person_id','year','term']);
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
        Schema::dropIfExists('session_attendee');
    }
}
