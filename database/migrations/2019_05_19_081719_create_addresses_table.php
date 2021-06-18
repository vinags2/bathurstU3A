<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('company_name', 40)
                ->nullable();
            $table->string('line_1', 40);
            $table->string('line_2', 40)
                ->nullable();
            // TODO: Note that his virtualAs column modifier has not been tested.
            $table->virtualAs('ifnull(concat_ws(\', \',`line_1`,nullif(`line_2`,\'\')),\'\') AS `address`');
            $table->string('suburb', 40)
                ->nullable();
            $table->string('postcode', 4)
                ->nullable();
            $table->string('updatedby', 20)
                ->nullable()
                ->comment('the original UpdatedBy - the person\'s name');
            $table->unsignedBigInteger('updated_by')
                ->nullable()
                ->comment('pointer to person table id');
            $table->string('comment', 100)->nullable();
            $table->boolean('deleted')
                ->nullable()
                ->default(false);
            $table->softDeletes();
            $table->timestamps();
            $table->unique(['line_1','suburb']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('addresses');
    }
}
