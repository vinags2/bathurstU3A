<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name',40)
                ->unique();
            $table->boolean('one_enrolment_form')
                ->nullable()
                ->comment('delete this after importing V1 tables');
            $table->mediumText('description')
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
            $table->boolean('no_longer_offerred')
                ->nullable()
                ->comment('delete this after importing V1 tables')
                ->default(false);
            $table->softDeletes();
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
        Schema::dropIfExists('courses');
    }
}
