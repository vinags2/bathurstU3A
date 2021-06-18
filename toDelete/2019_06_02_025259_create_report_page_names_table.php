<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReportPageNamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_page_names', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name',100);
            $table->unsignedTinyInteger('type')
                ->comment('1 = normal PHP
                    2 = a webpage
                    3 = PDF generator');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('report_page_names');
    }
}
