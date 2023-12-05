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
        Schema::create('beca_4_house_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('beca_id')->constrained('becas', 'id');
            $table->string('house_is')->nullable();
            $table->string('roof_material')->nullable();
            $table->string('floor_material')->nullable();
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
        Schema::dropIfExists('beca_4_house_data');
    }
};