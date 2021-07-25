<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOfferDiscriminatePlacesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offer_discriminate_places', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('views_count');
            $table->double('views_price');
            $table->enum('discriminate_place' , ['0' , '1' , '2' , '3'])->default('0');
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
        Schema::dropIfExists('offer_discriminate_places');
    }
}
