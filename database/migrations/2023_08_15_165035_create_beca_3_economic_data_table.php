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
        Schema::create('beca_3_economic_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('beca_id')->constrained('becas', 'id');
            $table->decimal('food', 11, 2)->nullable()->comment("despensa");
            $table->decimal('transport', 11, 2)->nullable();
            $table->decimal('living_place', 11, 2)->nullable()->comment("vivienda");
            $table->decimal('services', 11, 2)->nullable()->comment("agua y luz");
            $table->decimal('automobile', 11, 2)->nullable();
            $table->boolean('finished')->nullable()->default(false);

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
        Schema::dropIfExists('beca_3_economic_data');
    }
};
