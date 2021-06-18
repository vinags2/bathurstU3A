<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMembershipHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('membership_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('person_id');
            $table->year('year');
            $table->date('date_of_admission')
                ->nullable();
            $table->date('date_of_withdrawal')
                ->nullable()
                ->comment('Date that the member withdraws from the U3A');
            $table->string('receipt_number',20)
                ->nullable();
            $table->string('updatedby', 20)
                ->nullable()
                ->comment('the original UpdatedBy - the person\'s name');
            $table->unsignedBigInteger('updated_by')
                ->nullable()
                ->comment('pointer to person table id');
            $table->timestamp('updated_at')
                ->nullable();
            $table->boolean('deleted')
                ->nullable()
                ->default(false);
            $table->softDeletes();
            $table->timestamps();
            $table->index(['person_id','year']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('membership_histories');
    }
}
