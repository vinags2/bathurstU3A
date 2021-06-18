<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSessionAttendanceHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('session_attendance_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('person_id')
                ->comment('attendee');
            $table->unsignedBigInteger('session_id');
            $table->date('date_of_enrolment')
                ->nullable();
            $table->date('date_of_withdrawal')
                ->nullable();
            $table->year('year');
            $table->unsignedTinyInteger('term')
                ->nullable();
            $table->string('updatedby', 20)
                ->nullable()
                ->comment('the original UpdatedBy - the person\'s name');
            $table->timestamp('updated_at')
                ->nullable();
            $table->boolean('deleted')
                ->nullable()
                ->default(false);
            $table->softDeletes();
            $table->index('session_id');
            // Uncomment this when in production
            // During testing, the index is created during the data import stage.
            // $table->unique(['person_id','session_id','year','term'], 'psyt_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('session_attendance_histories');
    }
}
