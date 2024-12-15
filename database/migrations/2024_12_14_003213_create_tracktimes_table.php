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
        Schema::create('tracktimes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->time('clockin');
            $table->time('clockout')->nullable();
            $table->dateTime('workdate');
            $table->bigInteger('totalhours')->nullable();
            $table->string('note')->nullable();
            $table->longText('imagecapture');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracktimes');
    }
};
