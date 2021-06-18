<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->year('year');
            $table->boolean('yearly_reset');
            $table->unsignedTinyInteger('weeks_in_term')
                ->default(8);
            $table->string('title',45)
                ->default('The Bathurst U3A Database');
            $table->string('header_image',100)
                ->default('res/cropped-Autumn-29.jpg');
            $table->string('db_home')
                ->default('http://bathurstu3a.com/db/index.php');
            $table->string('db_home_local')
                ->default('http://127.0.0.1/~gregvinall/bathurstu3a/db2/index.php');
            $table->mediumText('terms')
                ->nullable()
                ->comment('Terms stores term start and end dates in JSON format.
                eg {"Term 1":
                    {"start":"20190204","end":"20190329"},
                    "Term 2":
                    {"start":"20190506","end":"20190628"},
                    "Term 3":
                    {"start":"20190729","end":"20190920"},
                    "Term 4":
                    {"start":"20191014","end":"20191206"}
                }');
            $table->unsignedTinyInteger('number_of_terms')
                ->default(4);
            $table->string('email_of_dbadmin')
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
        Schema::dropIfExists('settings');
    }
}
