<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedTinyInteger('menu')
                ->comment('The first menu is number 0');
            $table->unsignedInteger('order')
                ->comment('used for sorting the menu items');
            $table->unsignedTinyInteger('next_menu')
                ->nullable()
                ->comment('pointer to the next menu after the user selects this item');
            $table->unsignedTinyInteger('report_id')
                ->comment('pointer to an entry in the reports table, containing details about the menu/report');
            $table->index(['menu','order']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menus');
    }
}
