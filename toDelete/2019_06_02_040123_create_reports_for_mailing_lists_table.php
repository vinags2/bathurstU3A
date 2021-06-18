<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReportsForMailingListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reports_for_mailing_lists', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('report_id');
            $table->unsignedTinyInteger('first_name')
                ->comment('the column number of the report which contains the first name');
            $table->unsignedTinyInteger('last_name')
                ->comment('the column number of the report which contains the last name');
            $table->unsignedTinyInteger('email')
                ->comment('the column number of the report which contains the email');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reports_for_mailing_lists');
    }
}
