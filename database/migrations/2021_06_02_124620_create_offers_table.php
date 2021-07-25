<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOffersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('status' , ['0' , '1'])->default('0');
            $table->enum('discriminate' , ['0' , '1'])->default('0');
            $table->date('end_discriminate')->nullable();
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->bigInteger('discriminate_place_id')->unsigned()->nullable();
            $table->foreign('discriminate_place_id')
                ->references('id')
                ->on('offer_discriminate_places')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->string('title')->nullable();
            $table->double('price')->default(0.0);
            $table->date('end_date');
            $table->integer('max_quantity')->nullable();
            $table->string('code')->nullable();
            $table->string('transfer_photo')->nullable();
            $table->string('invoice_id')->nullable();
            $table->integer('remaining_views')->default(0);
            $table->integer('views')->default(0);
            $table->integer('views_count')->default(0);
            $table->text('details')->nullable();
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
        Schema::dropIfExists('offers');
    }
}
