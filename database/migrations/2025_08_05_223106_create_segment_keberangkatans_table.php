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
        Schema::create('segment_keberangkatans', function (Blueprint $table) {
            $table->id();
            $table->integer('sequence');
            $table->foreignId('keberangkatan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('destinasi_id')->constrained()->cascadeOnDelete();
            $table->dateTime('time');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('segment_keberangkatans');
    }
};
