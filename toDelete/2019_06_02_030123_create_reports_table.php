<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedTinyInteger('report');
            $table->unsignedTinyInteger('security')
                ->nullable();
            $table->string('name',45);
            $table->string('description',255);
            $table->unsignedTinyInteger('report_sql_id')
                ->nullable();
            $table->unsignedTinyInteger('report_sql_id_for_another_year')
                ->nullable();
            $table->unsignedTinyInteger('report_title_id')
                ->nullable();
            $table->unsignedTinyInteger('report_column_width_id')
                ->nullable();
            $table->unsignedTinyInteger('report_page_name_id')
                ->nullable();
            $table->unsignedTinyInteger('report_query_string_id')
                ->nullable();
            $table->unsignedTinyInteger('report_lookup_table_id')
                ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reports');
    }
}
