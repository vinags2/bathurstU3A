<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePeopleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('people', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('last_name',20);
            $table->string('first_name',20);
            // TODO: Note that his virtualAs column modifier has not been tested.
            $table->virtualAs('concat_ws(\' \',`first_name`,`last_name`) AS `name`');
            $table->string('preferred_name',20);
            $table->unsignedBigInteger('partner')
                ->nullable();
            $table->unsignedBigInteger('residential_address')
                ->nullable();
            $table->unsignedBigInteger('postal_address')
                ->nullable();
            $table->string('appellation',4)
                ->nullable();
            $table->boolean('prefer_email')
                ->default(true);
            $table->string('email',60)
                ->nullable();
            $table->string('phone',20)
                ->nullable();
            $table->string('mobile',20)
                ->nullable();
            // TODO: Note that his virtualAs column modifier has not been tested.
            $table->virtualAs('if(`phone` = \'\' or `phone` is null,`mobile`,`phone`) AS any_phone');
            $table->boolean('member')
                ->default(true);
            $table->unsignedTinyInteger('committee_member')
                ->default(true)
                ->comment('an integer which sets the order that the committee is displayed');
            $table->string('committee_position',45)
                ->nullable();
            $table->unsignedBigInteger('emergency_contact')
                ->nullable();
            $table->unsignedTinyInteger('payment_method')
                ->nullable()
                ->comment('0 = not paid
                    1 = paid by leaving cash/cheque at BINC
                    2 = posted a cheque in the mail to Bathurst U3A
                    3 = by direct credit
                    4 = other
                    5 = honorary member');
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
            // create indices
            $table->unique('last_name', 'first_name');
            $table->index('residential_address');
            $table->index('committee_member');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('people');
    }
}
