<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('beca_5_household_equipment_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('beca_id')->constrained('becas', 'id');
            $table->integer('beds')->nullable();
            $table->integer('washing_machines')->nullable();
            $table->integer('boilers')->nullable();
            $table->integer('tvs')->nullable();
            $table->integer('pcs')->nullable();
            $table->integer('music_player')->nullable();
            $table->integer('stoves')->nullable();
            $table->integer('refrigerators')->nullable();

            $table->boolean('drinking_water')->nullable();
            $table->boolean('electric_light')->nullable();
            $table->boolean('sewer_system')->nullable();
            $table->boolean('pavement')->nullable();
            $table->boolean('automobile')->nullable();
            $table->boolean('phone_line')->nullable();
            $table->boolean('internet')->nullable();
            $table->integer('score')->nullable();
            $table->boolean('finished')->default(false);

            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('beca_5_household_equipment_data');
    }
};