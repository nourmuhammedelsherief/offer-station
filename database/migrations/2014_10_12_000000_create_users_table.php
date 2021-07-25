<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('en_name')->nullable();
            $table->string('phone_number');
            $table->string('email')->nullable();
            $table->string('photo')->nullable();
            $table->string('api_token')->nullable();
            $table->enum('active' , ['0' , '1'])->default('0');
            $table->string('verification_code')->nullable();
            $table->string('password');
            $table->enum('type' , ['1' , '2']);
            $table->bigInteger('store_type_id')->unsigned()->nullable();
            $table->foreign('store_type_id')
                ->references('id')
                ->on('store_types')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->bigInteger('city_id')->unsigned()->nullable();
            $table->foreign('city_id')
                ->references('id')
                ->on('cities')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->string('commercial_register')->nullable();
            $table->string('license')->nullable();
            $table->string('work_times')->nullable();
            $table->string('video_link')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('store_url')->nullable();
            $table->string('logo')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
