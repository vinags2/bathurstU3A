<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateCourseHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('course_id');
            $table->string('name',40);
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
            $table->boolean('suspended')
                ->nullable()
                ->default(false);
            $table->date('effective_from')
                ->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->date('effective_to')
                ->nullable()
                ->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('course_histories');
    }
}
