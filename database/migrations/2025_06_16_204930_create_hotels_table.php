<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('hotels', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(false)->index();

            $table->unsignedBigInteger('city_id');
            $table->foreign('city_id')->references('id')->on('cities')->nullOnDelete();

            $table->string('address',255)->nullable(false);
            $table->string('tax_id',125)->nullable(false)->index();
            $table->integer('quantity_rooms')->nullable(false)->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotels');
    }
};
