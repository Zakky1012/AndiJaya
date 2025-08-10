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
        Schema::create('keberangkatan_class_fasilitas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('keberangkatan_class_id')->references('id')->on('class_keberangkatans')->onDelete('cascade');
            $table->foreignId('fasilitas_id')->references('id')->on('fasilitas')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keberangkatan_class_fasilitas');
    }
};
