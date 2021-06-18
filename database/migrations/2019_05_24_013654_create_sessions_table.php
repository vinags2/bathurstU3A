<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sessions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('course_id');
            $table->string('name',40)
                ->unique();
            $table->unsignedBigInteger('venue_id')
                ->nullable();
            $table->unsignedBigInteger('facilitator')
                ->nullable()
                ->comment('pointer to persons table');
            $table->unsignedBigInteger('alternate_facilitator')
                ->nullable()
                ->comment('pointer to persons table');
            $table->unsignedTinyInteger('day_of_the_week')
                ->nullable();
            // TODO: Note that his virtualAs column modifier has not been tested.
            $table->virtualAs('dayname(concat(\'1970-09-2\',`day_of_the_week`)) AS day');
            $table->time('start_time')
                ->nullable();
            // TODO: Note that his virtualAs column modifier has not been tested.
            $table->virtualAs('time_format(`start_time`,\'%h:%i%p\')AS start');
            $table->time('end_time')
                ->nullable();
            // TODO: Note that his virtualAs column modifier has not been tested.
            $table->virtualAs('time_format(`start_time`,\'%h:%i%p\') AS end');
            $table->time('week_of_the_month')
                ->nullable()
                ->comment('null = every week of the month');
            $table->boolean('follows_term')
                ->default(true)
                ->comment('true if the course is only run during term time');
            $table->unsignedTinyInteger('term_length')
                ->nullable()
                ->comment('delete follows_term after importing V1 data;
                    when term_length is null, then the course does not follow the term');
            $table->unsignedTinyInteger('roll_type')
                ->default(0)
                ->comment('First bit not set = normal roll (0)
                    First bit set = generic roll, — do not print names (1)
                    Second bit set = between terms roll (2)
                    Third bit set = print an extra generic roll page (4)
                    Fourth bit set = one roll per course, not per course session. (8)
                    Fifth bit set = no roll (16)
                    Sixth bit set = monthly rolls Feb - Nov (32)
                    Seventh bit set = no contact details (64)');
            $table->unsignedInteger('first_20_weeks')
                ->nullable()
                ->comment('The weeks of the year that the Session is run.
                    e.g. 1 - week 1, 2 (binary 10) = week 2, 4 (binary 100) = week 3, etc.
                    first_20_weeks stores the first 20 weeks of the year,
                    second_20_weeks stores the next 20 weeks, and
                    last_20_weeks stores the last 12 weeks of the year.');
            $table->unsignedInteger('second_20_weeks')
                ->nullable()
                ->comment('The weeks of the year that the Session is run.
                    e.g. 1 - week 1, 2 (binary 10) = week 2, 4 (binary 100) = week 3, etc.
                    first_20_weeks stores the first 20 weeks of the year,
                    second_20_weeks stores the next 20 weeks, and
                    last_20_weeks stores the last 12 weeks of the year.');
            $table->unsignedInteger('last_12_weeks')
                ->nullable()
                ->comment('The weeks of the year that the Session is run.
                    e.g. 1 - week 1, 2 (binary 10) = week 2, 4 (binary 100) = week 3, etc.
                    first_20_weeks stores the first 20 weeks of the year,
                    second_20_weeks stores the next 20 weeks, and
                    last_20_weeks stores the last 12 weeks of the year.');
            $table->unsignedTinyInteger('maximum_session_size')
                ->nullable();
            $table->unsignedTinyInteger('minimum_session_size')
                ->nullable();
            $table->string('comment',100)
                ->nullable();
            $table->string('updatedby', 20)
                ->nullable()
                ->comment('the original UpdatedBy - the person\'s name');
            $table->unsignedBigInteger('updated_by')
                ->nullable()
                ->comment('pointer to person table id');
            $table->boolean('deleted')
                ->nullable()
                ->comment('delete this after importing V1 tables')
                ->default(false);
            $table->boolean('suspended')
                ->nullable()
                ->default(false);
            $table->mediumText('description')
                ->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->index(['day_of_the_week','start_time']);
            $table->index('venue_id');
            $table->index('facilitator');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sessions');
    }
}
