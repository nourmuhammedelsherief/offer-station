<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOfferPhotosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offer_photos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('offer_id')->unsigned();
            $table->foreign('offer_id')
                ->references('id')
                ->on('offers')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->string('photo')->nullable();
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
        Schema::dropIfExists('offer_photos');
    }
}
